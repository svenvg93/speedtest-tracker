<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';

    public function getLabel(): ?string
    {
        return Str::title($this->name);
    }
}
