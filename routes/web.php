<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

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

Route::get('/', function () {    
    return view('welcome', ['title' => 'Open Source Inventory and BOM Management']);
});


Route::get('/dashboard', function () {
    return view('dashboard', ['title' => 'Settings']);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/parts', function () {    
    return view('parts', ['title' => 'Parts']);
})->middleware(['auth', 'verified'])->name('parts');

Route::get('/boms', function () {    
    return view('boms', ['title' => 'BOMs']);
})->middleware(['auth', 'verified'])->name('boms');

Route::get('/locations', function () {    
    return view('welcome', ['title' => 'Storage Locations']);
})->middleware(['auth', 'verified'])->name('locations');

Route::get('/categories', function () {    
    return view('welcome', ['title' => 'Categories']);
})->middleware(['auth', 'verified'])->name('categories');

Route::get('/suppliers', function () {    
    return view('welcome', ['title' => 'Suppliers']);
})->middleware(['auth', 'verified'])->name('suppliers');

Route::get('/footprints', function () {    
    return view('welcome', ['title' => 'Footprints']);
})->middleware(['auth', 'verified'])->name('footprints');
