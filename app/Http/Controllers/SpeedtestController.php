<?php

namespace App\Http\Controllers;

use App\Actions\GetOoklaSpeedtestServers;
use App\Actions\Ookla\RunSpeedtest as RunSpeedtestAction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class SpeedtestController extends Controller
{
    public function runManual(Request $request): RedirectResponse
    {
        $serverId = $request->input('server_id');
        
        RunSpeedtestAction::run(
            scheduled: false,
            serverId: $serverId ? (int) $serverId : null
        );

        return redirect()->back()->with('success', 'Speedtest started successfully.');
    }

    public function getServers(): JsonResponse
    {
        $servers = GetOoklaSpeedtestServers::run();
        
        return response()->json([
            'servers' => $servers
        ]);
    }
} 