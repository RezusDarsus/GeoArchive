<?php

namespace App\Http\Controllers\PublicArchive;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\HistoricalEvent;
use Illuminate\View\View;

class HistoryGraphController extends Controller
{
    /**
     * Render an interactive, Obsidian-style graph of the archive.
     *
     * Nodes are historical events and artifacts; edges are built from real
     * relationships in the database: the chronological timeline that links one
     * event to the next, and the many-to-many pivot that links each artifact to
     * the events it documents. Clicking a node opens that record's detail page.
     */
    public function __invoke(): View
    {
        $events = HistoricalEvent::chronological()->get(['id', 'title', 'date_or_period', 'sort_year']);
        $artifacts = Artifact::with(['category:id,name', 'historicalEvents:id'])
            ->orderBy('title')
            ->get(['id', 'title', 'period', 'category_id']);

        $nodes = [];
        $edges = [];

        foreach ($events as $event) {
            $nodes[] = [
                'id' => 'e'.$event->id,
                'label' => $event->title,
                'type' => 'event',
                'meta' => $event->date_or_period,
                'url' => route('events.show', $event->id),
            ];
        }

        // Timeline backbone: connect each event to the next one in history.
        $previous = null;
        foreach ($events as $event) {
            if ($previous !== null) {
                $edges[] = ['source' => 'e'.$previous->id, 'target' => 'e'.$event->id, 'kind' => 'era'];
            }
            $previous = $event;
        }

        foreach ($artifacts as $artifact) {
            $nodes[] = [
                'id' => 'a'.$artifact->id,
                'label' => $artifact->title,
                'type' => 'artifact',
                'meta' => $artifact->category?->name,
                'url' => route('artifacts.show', $artifact->id),
            ];

            // Many-to-many links: artifact <-> historical events it documents.
            foreach ($artifact->historicalEvents as $event) {
                $edges[] = ['source' => 'a'.$artifact->id, 'target' => 'e'.$event->id, 'kind' => 'link'];
            }
        }

        return view('history-graph.index', [
            'nodes' => $nodes,
            'edges' => $edges,
            'eventCount' => $events->count(),
            'artifactCount' => $artifacts->count(),
            'linkCount' => count($edges),
        ]);
    }
}
