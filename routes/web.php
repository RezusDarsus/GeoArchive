<?php

use App\Http\Controllers\Admin\ArtifactController as AdminArtifactController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\HistoricalEventController as AdminHistoricalEventController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicArchive\ArtifactController;
use App\Http\Controllers\PublicArchive\CategoryController;
use App\Http\Controllers\PublicArchive\HistoricalEventController;
use App\Http\Controllers\PublicArchive\HistoryGraphController;
use App\Http\Controllers\PublicArchive\HistoryPathController;
use App\Http\Controllers\PublicArchive\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/artifacts', [ArtifactController::class, 'index'])->name('artifacts.index');
Route::get('/artifacts/{artifact}', [ArtifactController::class, 'show'])->name('artifacts.show');
Route::get('/events', [HistoricalEventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [HistoricalEventController::class, 'show'])->name('events.show');
Route::get('/history-paths', HistoryPathController::class)->name('history-paths.index');
Route::get('/history-graph', HistoryGraphController::class)->name('history-graph.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tags.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('artifacts', AdminArtifactController::class);
    Route::resource('categories', AdminCategoryController::class)->except('show');
    Route::resource('events', AdminHistoricalEventController::class);
    Route::resource('tags', AdminTagController::class)->except('show');
});
