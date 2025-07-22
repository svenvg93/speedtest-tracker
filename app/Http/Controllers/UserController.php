<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        $transformedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });
        
        return Inertia::render('users', [
            'users' => [
                'data' => $transformedUsers,
                'total' => $users->count(),
                'current_page' => 1,
                'last_page' => 1,
            ],
            'roles' => collect(UserRole::cases())->map(fn($role) => [
                'value' => $role->value,
                'label' => $role->getLabel(),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
            'password' => ['nullable', Password::defaults()],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting the last admin
        if ($user->role === UserRole::Admin && User::where('role', UserRole::Admin)->count() <= 1) {
            return redirect()->route('users.index')->with('error', 'Cannot delete the last admin user.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->input('ids', []);
        $users = User::whereIn('id', $ids);
        
        // Prevent deleting all admins
        $adminCount = User::where('role', UserRole::Admin)->count();
        $deletingAdmins = $users->where('role', UserRole::Admin)->count();
        
        if ($adminCount - $deletingAdmins <= 0) {
            return redirect()->route('users.index')->with('error', 'Cannot delete all admin users.');
        }

        $users->delete();
        return redirect()->route('users.index')->with('success', 'Selected users deleted successfully.');
    }
} 