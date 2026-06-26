<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\HistoricalEvent;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'artifacts' => Artifact::with('category')->latest()->orderByDesc('id')->take(3)->get(),
            'events' => HistoricalEvent::latest()->orderByDesc('id')->take(3)->get(),
        ]);
    }
}
