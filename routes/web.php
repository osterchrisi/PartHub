<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\PartsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StockLevelController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DatabaseServiceController;
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
    return view('welcome', ['title' => 'Open Source Inventory and BOM Management', 'view' => 'welcome']);
})->name('welcome');

//* User Stuff
Route::get('/dashboard', function () {
    return view('dashboard', ['title' => 'Settings', 'view' => 'dashboard']);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//* Database Service Routes
Route::post('/updateRow', [DatabaseService::class, 'updateCell'])->middleware(['auth', 'verified']);
Route::post('/deleteRow', [DatabaseServiceController::class, 'deleteRow'])->middleware(['auth', 'verified']);

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
Route::controller(BomController::class)->group(function () {
    Route::get('/boms', 'index')->middleware(['auth', 'verified'])->name('boms');
    Route::get('/boms.bomsTable', 'index')->middleware(['auth', 'verified'])->name('boms.bomsTable');
    Route::get('/bom/{id}', 'show')->middleware(['auth', 'verified']);
    Route::post('/bom.assemble', 'prepareBomForAssembly')->middleware(['auth', 'verified']);
    Route::post('/bom.import', 'importBom')->name('bom.import');
    Route::get('/bom.import-test', function () {
        return view('boms.import', ['title' => 'Import BOM']);
    })->middleware(['auth', 'verified'])->name('bom import');

});

//* Location Routes
Route::controller(LocationController::class)->group(function () {
    Route::get('/locations', 'index')->middleware(['auth', 'verified'])->name('locations');
    Route::get('/locationi.locationsTable', 'index')->middleware(['auth', 'verified'])->name('locations.locationsTable');
    Route::get('/locations.get', function () {
        return LocationController::getLocations();
    })->middleware(['auth', 'verified']);
});

//* Category Routes
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index')->middleware(['auth', 'verified'])->name('categories');
    Route::get('/category/{id}', 'show')->middleware(['auth', 'verified']);
    Route::get('/categories.list', 'list')->middleware(['auth', 'verified']);
});

//* Supplier Routes
Route::get('/suppliers', function () {
    return view('welcome', ['title' => 'Suppliers', 'view' => 'suppliers']);
})
    ->middleware(['auth', 'verified'])
    ->name('suppliers');

//* Footprint Routes
Route::get('/footprints', function () {
    return view('welcome', ['title' => 'Footprints', 'view' => 'footprints']);
})
    ->middleware(['auth', 'verified'])
    ->name('footprints');

//* StockLevel Routes
Route::get('/stocklevels', function () {
    return StockLevelController::index(335);
})
    ->middleware(['auth', 'verified']);

//* Standalone Pages Routes
Route::get('/pricing', function () {
    return view('pricing', ['title' => 'Pricing', 'view' => 'pricing']);
})
    ->name('pricing');

Route::get('/signup', function () {
    return view('auth.register', ['title' => 'Signup', 'view' => 'signup']);
})
    ->name('signup');

Route::get('/whatis', function () {
    return view('welcome', ['title' => 'What is PartHub, anyway?', 'view' => 'whatis']);
})
    ->name('whatis');

Route::get('/multi', function () {
    return view('multi', ['title' => 'Multi Test', 'view' => 'multi']);
});