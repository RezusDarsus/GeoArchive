<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArtifactResource;
use App\Models\Artifact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArtifactController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $artifacts = Artifact::with(['category', 'tags'])
            ->search($request->query('q'))
            ->inCategory($request->integer('category') ?: null)
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return ArtifactResource::collection($artifacts);
    }

    public function show(Artifact $artifact): ArtifactResource
    {
        return new ArtifactResource($artifact->load(['category', 'tags', 'historicalEvents']));
    }
}
