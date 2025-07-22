<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $latest = \App\Models\Result::orderByDesc('created_at')->first();
        $previous = $latest
            ? \App\Models\Result::where('id', '<', $latest->id)->orderByDesc('created_at')->first()
            : null;

        // Get unique servers from all results
        $servers = Result::query()
            ->selectRaw('DISTINCT(data->"$.server.name") as name')
            ->orderBy('name')
            ->get()
            ->map(function ($row) {
                return trim($row->name, '"');
            })
            ->unique()
            ->values();

        return \Inertia\Inertia::render('dashboard', [
            'latest' => $latest,
            'previous' => $previous,
            'servers' => $servers,
            'chartDateTimeFormat' => config('app.chart_datetime_format'),
        ]);
    }

    public function resultsData(Request $request)
    {
        $query = \App\Models\Result::query();
        $server = $request->query('server');
        
        if ($server && $server !== 'all') {
            // Handle multiple servers (comma-separated)
            $servers = explode(',', $server);
            $servers = array_map('trim', $servers);
            $servers = array_filter($servers); // Remove empty values
            
            if (!empty($servers)) {
                // Use whereIn with proper parameter binding for multiple values
                $placeholders = str_repeat('?,', count($servers) - 1) . '?';
                $query->whereRaw("json_extract(data, '$.server.name') IN ($placeholders)", $servers);
            }
        }
        
        if ($request->has('from')) {
            // Parse the date as UTC and convert to application timezone
            $fromDate = \Carbon\Carbon::parse($request->from, 'UTC')->setTimezone(config('app.timezone'));
            Log::info('Date filtering - from:', [
                'original' => $request->from,
                'parsed_utc' => \Carbon\Carbon::parse($request->from, 'UTC')->toISOString(),
                'converted_to_app_tz' => $fromDate->toISOString(),
                'app_timezone' => config('app.timezone')
            ]);
            $query->where('created_at', '>=', $fromDate);
        }
        if ($request->has('to')) {
            // Parse the date as UTC and convert to application timezone
            $toDate = \Carbon\Carbon::parse($request->to, 'UTC')->setTimezone(config('app.timezone'));
            Log::info('Date filtering - to:', [
                'original' => $request->to,
                'parsed_utc' => \Carbon\Carbon::parse($request->to, 'UTC')->toISOString(),
                'converted_to_app_tz' => $toDate->toISOString(),
                'app_timezone' => config('app.timezone')
            ]);
            $query->where('created_at', '<=', $toDate);
        }
        $results = $query->orderBy('created_at')->get(['created_at', 'download', 'upload', 'ping', 'healthy', 'data']);
        
        // Add server name and status to each result
        $results = $results->map(function ($result) {
            $data = is_string($result->data) ? json_decode($result->data, true) : $result->data;
            $result->server_name = $data['server']['name'] ?? 'Unknown';
            
            // Determine status based on data presence
            if ($result->download !== null && $result->upload !== null && $result->ping !== null) {
                $result->status = 'completed';
            } else {
                $result->status = 'failed';
            }
            
            return $result;
        });
        
        return response()->json($results);
    }

    public function stats(Request $request)
    {
        return response()->json([
            'download' => $this->getStats($request, 'download'),
            'upload' => $this->getStats($request, 'upload'),
            'ping' => $this->getStats($request, 'ping'),
        ]);
    }

    private function getStats(Request $request, string $field)
    {
        $query = Result::query();
        $server = $request->query('server');
        
        // Apply server filter
        if ($server && $server !== 'all') {
            $servers = explode(',', $server);
            $servers = array_map('trim', $servers);
            $servers = array_filter($servers);
            
            if (!empty($servers)) {
                $placeholders = str_repeat('?,', count($servers) - 1) . '?';
                $query->whereRaw("json_extract(data, '$.server.name') IN ($placeholders)", $servers);
            }
        }
        
        // Apply date filters
        if ($request->has('from')) {
            // Parse the date as UTC and convert to application timezone
            $fromDate = \Carbon\Carbon::parse($request->from, 'UTC')->setTimezone(config('app.timezone'));
            $query->where('created_at', '>=', $fromDate);
        }
        if ($request->has('to')) {
            // Parse the date as UTC and convert to application timezone
            $toDate = \Carbon\Carbon::parse($request->to, 'UTC')->setTimezone(config('app.timezone'));
            $query->where('created_at', '<=', $toDate);
        }

        // Get current period stats
        $currentStats = $query->selectRaw("
            AVG($field) as average,
            MAX(created_at) as latest_time
        ")->first();

        // Get latest value (without date filters to get the most recent overall)
        $latestQuery = Result::query();
        if ($server && $server !== 'all') {
            $servers = explode(',', $server);
            $servers = array_map('trim', $servers);
            $servers = array_filter($servers);
            
            if (!empty($servers)) {
                $placeholders = str_repeat('?,', count($servers) - 1) . '?';
                $latestQuery->whereRaw("json_extract(data, '$.server.name') IN ($placeholders)", $servers);
            }
        }
        $latest = $latestQuery->orderBy('created_at', 'desc')->first();

        // Calculate change from previous period
        $change = null;
        if ($request->has('from') && $request->has('to')) {
            $from = \Carbon\Carbon::parse($request->from, 'UTC')->setTimezone(config('app.timezone'));
            $to = \Carbon\Carbon::parse($request->to, 'UTC')->setTimezone(config('app.timezone'));
            $duration = $to->diffInSeconds($from);
            
            $previousFrom = $from->copy()->subSeconds($duration);
            $previousTo = $from;
            
            $previousQuery = Result::query();
            if ($server && $server !== 'all') {
                $servers = explode(',', $server);
                $servers = array_map('trim', $servers);
                $servers = array_filter($servers);
                
                if (!empty($servers)) {
                    $placeholders = str_repeat('?,', count($servers) - 1) . '?';
                    $previousQuery->whereRaw("json_extract(data, '$.server.name') IN ($placeholders)", $servers);
                }
            }
            
            $previousAverage = $previousQuery
                ->where('created_at', '>=', $previousFrom)
                ->where('created_at', '<', $previousTo)
                ->avg($field);
            
            if ($previousAverage && $currentStats->average) {
                $change = (($currentStats->average - $previousAverage) / $previousAverage) * 100;
            }
        }

        return [
            'latest' => $latest ? $latest->$field : null,
            'average' => $currentStats->average,
            'change' => $change,
        ];
    }
}
