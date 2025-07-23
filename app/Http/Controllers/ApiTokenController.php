<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class ApiTokenController extends Controller
{
    /**
     * Display the API tokens page
     */
    public function index(): Response
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $tokens = PersonalAccessToken::where('tokenable_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'created_at' => $token->created_at->toISOString(),
                    'last_used_at' => $token->last_used_at?->toISOString(),
                    'expires_at' => $token->expires_at?->toISOString(),
                    'is_expired' => $token->expires_at && $token->expires_at->isPast(),
                ];
            });

        return Inertia::render('api-tokens', [
            'tokens' => $tokens,
            'newToken' => session('new_token'),
        ]);
    }

    /**
     * Store a new API token
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'abilities' => 'required|array|min:1',
            'abilities.*' => 'string|in:results:read,speedtests:run,ookla:list-servers',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $user = Auth::user();
        $token = $user->createToken(
            $request->name,
            $request->abilities,
            $request->expires_at ? now()->parse($request->expires_at) : null
        );

        // Store the plain text token in session to display it
        session()->flash('new_token', [
            'id' => $token->accessToken->id,
            'name' => $token->accessToken->name,
            'plain_text_token' => $token->plainTextToken,
        ]);

        return redirect()->back()->with('success', 'API token created successfully.');
    }

    /**
     * Update an API token
     */
    public function update(Request $request, string $id)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'abilities' => 'required|array|min:1',
            'abilities.*' => 'string|in:results:read,speedtests:run,ookla:list-servers',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $token = PersonalAccessToken::where('id', $id)
            ->where('tokenable_id', Auth::id())
            ->firstOrFail();

        // Don't allow editing expired tokens
        if ($token->expires_at && $token->expires_at->isPast()) {
            return redirect()->back()->withErrors(['expires_at' => 'Cannot edit expired tokens.']);
        }

        $token->update([
            'name' => $request->name,
            'abilities' => $request->abilities,
            'expires_at' => $request->expires_at ? now()->parse($request->expires_at) : null,
        ]);

        return redirect()->back()->with('success', 'API token updated successfully.');
    }

    /**
     * Delete an API token
     */
    public function destroy(string $id)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $token = PersonalAccessToken::where('id', $id)
            ->where('tokenable_id', Auth::id())
            ->firstOrFail();

        $token->delete();

        return redirect()->back()->with('success', 'API token deleted successfully.');
    }

    /**
     * Delete multiple API tokens
     */
    public function destroyMany(Request $request)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:personal_access_tokens,id',
        ]);

        $tokens = PersonalAccessToken::whereIn('id', $request->ids)
            ->where('tokenable_id', Auth::id())
            ->get();

        $tokens->each->delete();

        return redirect()->back()->with('success', count($tokens) . ' API token(s) deleted successfully.');
    }
} 