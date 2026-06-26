<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\Category;
use App\Models\HistoricalEvent;
use App\Models\Tag;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'Artifacts' => Artifact::count(),
                'Categories' => Category::count(),
                'Tags' => Tag::count(),
                'Historical events' => HistoricalEvent::count(),
                'Users' => User::count(),
            ],
        ]);
    }
}
