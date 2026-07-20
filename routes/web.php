<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Countries
    Route::get('/countries-data', [CountryController::class, 'data'])->name('countries.data');
    Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
    Route::get('/sync-countries', [CountryController::class, 'sync'])->name('countries.sync');
    Route::get('/countries/{country}/sync', [CountryController::class, 'syncSingle'])->name('countries.sync_single');

    // Weather Monitoring
    Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');

    // Currency Impact
    Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.index');

    // News Intelligence
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');

    // Port Location
    Route::get('/ports', [PortController::class, 'index'])->name('ports.index');
    Route::get('/ports/world-map', [PortController::class, 'worldMap'])->name('ports.world_map');

    // Comparison Engine
    Route::get('/comparison', [ComparisonController::class, 'index'])->name('comparison.index');

    // Watchlist
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::post('/watchlist/toggle/{country}', [WatchlistController::class, 'toggle'])->name('watchlist.toggle');

    // Admin Dashboard / Kelola User
    Route::get('/admin/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.user.store');
    Route::post('/admin/users/{user}/toggle', [AdminController::class, 'toggleUserStatus'])->name('admin.user.toggle');

    // Kelola Dataset Pelabuhan
    Route::get('/admin/ports', [AdminController::class, 'portsIndex'])->name('admin.ports.index');
    Route::post('/admin/ports', [AdminController::class, 'storePort'])->name('admin.ports.store');
    Route::delete('/admin/ports/{port}', [AdminController::class, 'destroyPort'])->name('admin.ports.destroy');

    // Kelola Artikel Analisis
    Route::get('/admin/articles', [AdminController::class, 'articlesIndex'])->name('admin.articles.index');
    Route::post('/admin/articles', [AdminController::class, 'storeArticle'])->name('admin.articles.store');
    Route::delete('/admin/articles/{article}', [AdminController::class, 'destroyArticle'])->name('admin.articles.destroy');

    // Profile CRUD
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Supplier CRUD
    Route::resource('/suppliers', SupplierController::class);
});

require __DIR__.'/auth.php';