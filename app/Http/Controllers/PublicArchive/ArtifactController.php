<?php

namespace App\Http\Controllers\PublicArchive;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArtifactController extends Controller
{
    public function index(Request $request): View
    {
        $artifacts = Artifact::with(['category', 'tags'])
            ->search($request->string('q')->toString())
            ->inCategory($request->filled('category') ? $request->integer('category') : null)
            ->withTag($request->filled('tag') ? $request->integer('tag') : null)
            ->when($request->input('sort') === 'oldest', fn ($query) => $query->oldest(), fn ($query) => $query->latest())
            ->orderByDesc('id')->paginate(9)->withQueryString();

        return view('artifacts.index', [
            'artifacts' => $artifacts,
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function show(Artifact $artifact): View
    {
        return view('artifacts.show', ['artifact' => $artifact->load(['category', 'tags', 'user', 'historicalEvents'])]);
    }
}
