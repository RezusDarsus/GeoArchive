<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HistoricalEventRequest;
use App\Models\HistoricalEvent;
use App\Services\HistoricalEventManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HistoricalEventController extends Controller
{
    public function __construct(private readonly HistoricalEventManager $events) {}

    public function index(): View
    {
        $this->authorize('viewAny', HistoricalEvent::class);

        return view('admin.events.index', ['events' => HistoricalEvent::latest()->paginate(15)]);
    }

    public function create(): View
    {
        $this->authorize('create', HistoricalEvent::class);

        return view('admin.events.create');
    }

    public function store(HistoricalEventRequest $request): RedirectResponse
    {
        $this->authorize('create', HistoricalEvent::class);
        $this->events->create($request->validated(), $request->file('image'));

        return redirect()->route('admin.events.index')->with('success', 'Historical event created successfully.');
    }

    public function show(HistoricalEvent $event): View
    {
        $this->authorize('view', $event);

        return view('admin.events.show', compact('event'));
    }

    public function edit(HistoricalEvent $event): View
    {
        $this->authorize('update', $event);

        return view('admin.events.edit', compact('event'));
    }

    public function update(HistoricalEventRequest $request, HistoricalEvent $event): RedirectResponse
    {
        $this->authorize('update', $event);
        $this->events->update($event, $request->validated(), $request->file('image'), $request->boolean('remove_image'));

        return redirect()->route('admin.events.index')->with('success', 'Historical event updated successfully.');
    }

    public function destroy(HistoricalEvent $event): RedirectResponse
    {
        $this->authorize('delete', $event);
        $this->events->delete($event);

        return redirect()->route('admin.events.index')->with('success', 'Historical event deleted successfully.');
    }
}
