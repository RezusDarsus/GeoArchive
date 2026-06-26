<?php

namespace App\Http\Controllers\PublicArchive;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\View\View;

class TagController extends Controller
{
    public function show(Tag $tag): View
    {
        return view('tags.show', [
            'tag' => $tag,
            'artifacts' => $tag->artifacts()->with(['category', 'tags'])->latest()->paginate(9),
        ]);
    }
}
