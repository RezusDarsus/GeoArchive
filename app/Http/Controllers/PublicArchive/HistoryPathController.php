<?php

namespace App\Http\Controllers\PublicArchive;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\HistoricalEvent;
use Illuminate\View\View;

class HistoryPathController extends Controller
{
    public function __invoke(): View
    {
        $paths = collect($this->definitions())->map(function (array $path): array {
            $path['nodes'] = collect($path['nodes'])->map(fn (array $node): array => [
                'kind' => $node[0],
                'record' => $this->findRecord($node[0], $node[1]),
            ])->filter(fn (array $node): bool => $node['record'] !== null);

            return $path;
        });

        return view('history-paths.index', compact('paths'));
    }

    private function findRecord(string $kind, string $title): Artifact|HistoricalEvent|null
    {
        return $kind === 'artifact'
            ? Artifact::with('category')->where('title', $title)->first()
            : HistoricalEvent::where('title', $title)->first();
    }

    private function definitions(): array
    {
        return [
            [
                'title' => 'From Bronze Age Georgia to Colchis',
                'summary' => 'Follow early material culture into the wealthy Black Sea kingdom known for metallurgy, ritual centers, and far-reaching trade.',
                'nodes' => [
                    ['artifact', 'Kura-Araxes Ceramic Vessel'],
                    ['artifact', 'Trialeti Gold Cup'],
                    ['event', 'Colchis in Western Georgia'],
                    ['artifact', 'Vani Bronze Figurine'],
                    ['artifact', 'Vani Colchis Coin'],
                ],
            ],
            [
                'title' => 'Iberia, writing, and Christian Georgia',
                'summary' => 'See how the eastern kingdom, Georgian writing, conversion, and monumental church building became parts of one historical transformation.',
                'nodes' => [
                    ['event', 'Rise of the Kingdom of Iberia'],
                    ['artifact', 'Armazi Bilingual Stele'],
                    ['event', 'Christianization of the Kingdom of Iberia'],
                    ['artifact', 'Bolnisi Sioni Inscription'],
                    ['artifact', 'Jvari Monastery'],
                ],
            ],
            [
                'title' => 'The road to David the Builder',
                'summary' => 'Trace the crisis, reforms, battles, and cultural institutions that turned a pressured kingdom into medieval Georgia’s great regional power.',
                'nodes' => [
                    ['event', 'The Great Turkish Invasion'],
                    ['event', 'Accession of David IV the Builder'],
                    ['event', 'Battle of Didgori'],
                    ['event', 'Liberation of Tbilisi'],
                    ['artifact', 'Gelati Monastery'],
                ],
            ],
            [
                'title' => 'Georgia’s medieval Golden Age',
                'summary' => 'Connect royal power with monasteries, metalwork, manuscript culture, and the literary world remembered from the reign of King Tamar.',
                'nodes' => [
                    ['event', 'Golden Age of Georgia'],
                    ['event', 'Reign of King Tamar'],
                    ['artifact', 'King Tamar Period Cross'],
                    ['artifact', 'Khakhuli Triptych'],
                    ['artifact', 'The Knight in the Panther’s Skin Manuscript'],
                ],
            ],
            [
                'title' => 'The modern struggle for independence',
                'summary' => 'Move through democratic independence, Soviet occupation, resistance, national tragedy, and the restoration of Georgian statehood.',
                'nodes' => [
                    ['event', 'First Democratic Republic of Georgia'],
                    ['event', 'Soviet Invasion and Occupation of Georgia'],
                    ['event', 'August Uprising against Soviet Rule'],
                    ['event', 'April 9 Tragedy'],
                    ['event', 'Independence Referendum and Restoration'],
                ],
            ],
        ];
    }
}
