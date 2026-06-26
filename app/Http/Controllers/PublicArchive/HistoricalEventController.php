<?php

namespace App\Http\Controllers\PublicArchive;

use App\Http\Controllers\Controller;
use App\Models\HistoricalEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoricalEventController extends Controller
{
    public function index(Request $request): View
    {
        $events = HistoricalEvent::query()
            ->search($request->string('q')->toString())
            ->chronological($request->input('sort') === 'newest')
            ->paginate(18)->withQueryString();

        return view('events.index', compact('events'));
    }

    public function show(HistoricalEvent $event): View
    {
        return view('events.show', ['event' => $event->load('artifacts')]);
    }
}
