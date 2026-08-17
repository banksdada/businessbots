<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalController extends Controller
{
    public function terms(): View
    {
        return view('legal.terms', ['legal' => config('legal')]);
    }

    public function privacy(): View
    {
        return view('legal.privacy', ['legal' => config('legal')]);
    }

    public function cookies(): View
    {
        return view('legal.cookies', ['legal' => config('legal')]);
    }
}
