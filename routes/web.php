<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PartsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StockLevelController;
use App\Http\Controllers\CategoryController;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//* Landing Page
Route::get('/', function () {
    return view('welcome', ['title' => 'Open Source Inventory and BOM Management']);
});

//* User Stuff
Route::get('/dashboard', function () {
    return view('dashboard', ['title' => 'Settings']);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//* Part Routes
Route::controller(PartsController::class)->group(function () {
    Route::get('/parts', 'index')->middleware(['auth', 'verified'])->name('parts');
    Route::get('/part/{id}', 'show')->middleware(['auth', 'verified']);
    Route::get('/part.getName', 'getName')->middleware(['auth', 'verified']);
    Route::post('/part.delete', 'destroy')->middleware(['auth', 'verified']);
    Route::post('/parts.prepareStockChanges', 'prepareStockChanges')->middleware(['auth', 'verified']);
    Route::post('/parts.create', 'create')->middleware(['auth', 'verified']);
    Route::get('/parts.partsTable', 'index')->middleware(['auth', 'verified'])->name('parts.partsTable');
});

//* BOM Routes
Route::get('/boms', function () {
    return view('boms', ['title' => 'BOMs']);
})
    ->middleware(['auth', 'verified'])
    ->name('boms');

//* Location Routes
Route::get('/locations.get', function () {
    return LocationController::getLocations();
})
    ->middleware(['auth', 'verified'])
    ->name('locations');

//* Category Routes
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index')->middleware(['auth', 'verified'])->name('categories');
    Route::get('/category/{id}', 'show')->middleware(['auth', 'verified']);
    Route::get('/categories.list', 'list')->middleware(['auth', 'verified']);
});

// Route::get('/categories', function () {
//     return view('welcome', ['title' => 'Categories']);
// })
//     ->middleware(['auth', 'verified'])
//     ->name('categories');

Route::get('/suppliers', function () {
    return view('welcome', ['title' => 'Suppliers']);
})
    ->middleware(['auth', 'verified'])
    ->name('suppliers');

Route::get('/footprints', function () {
    return view('welcome', ['title' => 'Footprints']);
})
    ->middleware(['auth', 'verified'])
    ->name('footprints');

Route::get('/pricing', function () {
    return view('welcome', ['title' => 'Pricing']);
})
    ->name('pricing');

Route::get('/signup', function () {
    return view('welcome', ['title' => 'Signup']);
})
    ->name('signup');

Route::get('/stocklevels', function () {
    return StockLevelController::index(335);
})
    ->middleware(['auth', 'verified']);