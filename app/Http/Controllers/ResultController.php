<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Arr;

class ResultController extends Controller
{
    public function index()
    {
        $results = Result::orderByDesc('created_at')->get();
        $transformedResults = $results->map(function ($result) {
            return [
                'id' => $result->id,
                'service' => $result->service,
                'ping' => $result->ping,
                'download' => $result->download,
                'upload' => $result->upload,
                'status' => $result->status,
                'created_at' => $result->created_at,
                'data' => $result->data,
                'comments' => $result->comments,
                'result_url' => $result->result_url,
                'healthy' => $result->healthy,
                'scheduled' => $result->scheduled,
                'updated_at' => $result->updated_at,
            ];
        });
        
        return Inertia::render('results', [
            'results' => [
                'data' => $transformedResults,
                'total' => $results->count(),
                'current_page' => 1,
                'last_page' => 1,
            ],
        ]);
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->input('ids', []);
        Result::whereIn('id', $ids)->delete();
        return redirect()->route('results.index')->with('success', 'Selected results deleted.');
    }

    public function destroy($id)
    {
        \App\Models\Result::findOrFail($id)->delete();
        return redirect()->route('results.index')->with('success', 'Result deleted.');
    }

    public function updateComments(Request $request, $id)
    {
        $result = \App\Models\Result::findOrFail($id);
        $result->comments = $request->input('comments');
        $result->save();
        return back()->with('success', 'Comments updated.');
    }
}
