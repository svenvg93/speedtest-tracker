<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum ResultService: string
{
    case Faker = 'faker';
    case Ookla = 'ookla';

    public function getLabel(): ?string
    {
        return Str::title($this->name);
    }
}
