<?php

namespace App\Http\Controllers\Coursier;

use App\Http\Controllers\Concerns\ModifieSonMotDePasse;

class PasswordController extends Controller
{
    use ModifieSonMotDePasse;

    protected function espace(): string
    {
        return 'coursier';
    }
}
