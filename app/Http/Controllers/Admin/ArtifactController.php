<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArtifactRequest;
use App\Models\Artifact;
use App\Models\Category;
use App\Models\HistoricalEvent;
use App\Models\Tag;
use App\Services\ArtifactManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ArtifactController extends Controller
{
    public function __construct(private readonly ArtifactManager $artifacts) {}

    public function index(): View
    {
        $this->authorize('viewAny', Artifact::class);

        return view('admin.artifacts.index', ['artifacts' => Artifact::with(['category', 'user'])->latest()->paginate(15)]);
    }

    public function create(): View
    {
        $this->authorize('create', Artifact::class);

        return view('admin.artifacts.create', $this->formData());
    }

    public function store(ArtifactRequest $request): RedirectResponse
    {
        $this->authorize('create', Artifact::class);
        $this->artifacts->create($request->user(), $request->validated(), $request->file('image'));

        return redirect()->route('admin.artifacts.index')->with('success', 'Artifact created successfully.');
    }

    public function show(Artifact $artifact): View
    {
        $this->authorize('view', $artifact);

        return view('admin.artifacts.show', ['artifact' => $artifact->load(['category', 'tags', 'user', 'historicalEvents'])]);
    }

    public function edit(Artifact $artifact): View
    {
        $this->authorize('update', $artifact);

        return view('admin.artifacts.edit', array_merge($this->formData(), ['artifact' => $artifact->load('tags', 'historicalEvents')]));
    }

    public function update(ArtifactRequest $request, Artifact $artifact): RedirectResponse
    {
        $this->authorize('update', $artifact);
        $this->artifacts->update($artifact, $request->validated(), $request->file('image'), $request->boolean('remove_image'));

        return redirect()->route('admin.artifacts.index')->with('success', 'Artifact updated successfully.');
    }

    public function destroy(Artifact $artifact): RedirectResponse
    {
        $this->authorize('delete', $artifact);
        $this->artifacts->delete($artifact);

        return redirect()->route('admin.artifacts.index')->with('success', 'Artifact deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'events' => HistoricalEvent::chronological()->get(),
        ];
    }
}
