<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        dd($user->hasRole('administrator'));
    }
}
