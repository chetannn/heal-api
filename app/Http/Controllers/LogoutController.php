<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class LogoutController extends Controller
{
    public function __invoke(): Response
    {
        auth()->user()->tokens()->delete();

        return response(['message' => 'Logged out']);
    }
}
