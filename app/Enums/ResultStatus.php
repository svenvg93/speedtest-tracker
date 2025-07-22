<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum ResultStatus: string
{
    case Benchmarking = 'benchmarking';
    case Checking = 'checking';
    case Completed = 'completed';
    case Failed = 'failed';
    case Running = 'running';
    case Started = 'started';
    case Skipped = 'skipped';
    case Waiting = 'waiting';

    public function getLabel(): ?string
    {
        return Str::title($this->name);
    }
}
