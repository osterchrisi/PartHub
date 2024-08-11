<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FootprintController;
use App\Http\Controllers\StockLevelController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DatabaseServiceController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ImageController;
use App\Services\DatabaseService;
use App\Http\Controllers\Auth\DemoLoginController;
use App\Http\Controllers\SubscriptionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\SocialiteController;



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
})->name('welcome')->middleware('redirect.if.not.authenticated');

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
Route::middleware(['redirect.if.not.authenticated', 'auth', 'verified'])->group(function () {
    Route::controller(PartController::class)->group(function () {
        Route::get('/parts', 'index')->name('parts');
        Route::get('/part/{id}', 'show');
        Route::get('/part.getName', 'getName');
        Route::post('/part.delete', 'destroy');
        Route::post('/parts.requestStockChange', 'handleStockRequests');
        Route::post('/part.create', 'create');
        Route::get('/parts.partsTable', 'index')->name('parts.partsTable');
    });
});


//* BOM Routes
Route::controller(BomController::class)->group(function () {
    Route::get('/boms', 'index')->middleware(['auth', 'verified'])->name('boms');
    Route::get('/boms.bomsTable', 'index')->middleware(['auth', 'verified'])->name('boms.bomsTable');
    Route::get('/bom/{id}', 'show')->middleware(['auth', 'verified']);
    Route::post('/bom.assemble', 'prepareBomForAssembly')->middleware(['auth', 'verified']);
    Route::post('/bom.import', 'importBom')->name('bom.import');
    Route::get('/bom.import-form', function () {
        return view('boms.import-form', ['title' => 'Import BOM']);
    })->middleware(['auth', 'verified'])->name('bom import');

});

//* Location Routes
Route::controller(LocationController::class)->group(function () {
    Route::get('/locations', 'index')->middleware(['auth', 'verified'])->name('locations');
    Route::get('/locations.locationsTable', 'index')->middleware(['auth', 'verified'])->name('locations.locationsTable');
    Route::post('/location.create', 'create')->middleware(['auth', 'verified']);
    Route::get('/locations.get', 'getLocations')->middleware(['auth', 'verified']);
    Route::get('/location/{id}', 'show')->middleware(['auth', 'verified']);
});

//* Category Routes
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index')->middleware(['auth', 'verified'])->name('categories');
    Route::get('/categories.categoriesTable', 'index')->middleware(['auth', 'verified'])->name('categories.categoriesTable');
    Route::get('/category/{id}', 'show')->middleware(['auth', 'verified']);
    Route::get('/categories.get', 'list')->middleware(['auth', 'verified']);
    Route::post('/category.create', 'create')->middleware(['auth', 'verified']);
});

//* Supplier Routes
Route::controller(SupplierController::class)->group(function () {
    Route::get('/suppliers', 'index')->middleware(['auth', 'verified'])->name('suppliers');
    Route::get('/suppliers.suppliersTable', 'index')->middleware(['auth', 'verified'])->name('suppliers.suppliersTable');
    Route::post('/supplier.create', 'create')->middleware(['auth', 'verified']);
    Route::get('/suppliers.get', 'getSuppliers')->middleware(['auth', 'verified']);
    Route::get('/supplier/{id}', 'show')->middleware(['auth', 'verified']);
});

//* Footprint Routes
Route::middleware(['auth', 'verified', 'subscription'])->group(function () {
    Route::controller(FootprintController::class)->group(function () {
        Route::get('/footprints', 'index')->middleware(['auth', 'verified'])->name('footprints');
        Route::get('/footprints.footprintsTable', 'index')->middleware(['auth', 'verified'])->name('footprints.footprintsTable');
        Route::post('/footprint.create', 'create')->middleware(['auth', 'verified']);
        Route::get('/footprints.get', 'getFootprints')->middleware(['auth', 'verified']);
        Route::get('/footprint/{id}', 'show')->middleware(['auth', 'verified']);
    });
});

//* StockLevel Routes
Route::get('/stocklevels', function () {
    return StockLevelController::index(335);
})
    ->middleware(['auth', 'verified']);

//* Image Controller
Route::controller(ImageController::class)->group(function () {
    Route::post('/upload-image/{type}/{id}', 'upload')->middleware(['auth', 'verified'])->name('upload-image');
    Route::get('/images/{type}/{id}', 'getImagesByTypeAndId')->middleware(['auth', 'verified'])->name('part.images');
    Route::delete('/delete-image/{type}/{id}', [ImageController::class, 'delete'])->middleware(['auth', 'verified'])->name('delete-image');
});

//* Standalone Pages Routes
// Route::get('/pricing', function () {
//     return view('pricing', ['title' => 'Pricing', 'view' => 'pricing']);
// })
//     ->name('pricing');

// Sign Up for new users
Route::get('/signup', function () {
    //! Passwort darf nicht länger als 72 Zeichen sein! (wegen bcrypt -> jetzt argon2)
    //! Passwort darf keine Leerzeichen enthalten
    //TODO: Das geht wahrscheinlich sauberer...
    if (env('APP_ENV') == 'demo') {
        return redirect('https://parthub.online/signup');
    }
    else {
        return view('auth.register', ['title' => 'Signup', 'view' => 'signup']);
    }
})
    ->name('signup');

// What is PartHub?
Route::get('/whatis', function () {
    return view('whatis', ['title' => 'What is PartHub, anyway?', 'view' => 'whatis']);
})
    ->name('whatis');

// Terms of Service
Route::get('/TOS', function () {
    return view('TOS', ['title' => 'Terms of Service', 'view' => 'TOS']);
})
    ->name('TOS');

// Privacy Policy
Route::get('/privacy-policy', function () {
    return view('privacy-policy', ['title' => 'Privacy Policy', 'view' => 'privacy-policy']);
})
    ->name('privacy-policy');

// Imprint
Route::get('/imprint', function () {
    return view('imprint', ['title' => 'Imprint', 'view' => 'imprint']);
})
    ->name('imprint');

// My 'Multi View' Tryout
Route::get('/multi', function () {
    return view('multi', ['title' => 'Multi Test', 'view' => 'multi']);
});

// Demo User Login
Route::get('/demo-login', [DemoLoginController::class, 'login'])->name('demo.login');

// E-Mail Preview
Route::get('/preview-email', function () {
    $user = User::first();
    return new App\Mail\WelcomeEmail($user);
});

// For testing views
Route::get('/image-testing', function () {
    return view('cz-image-test', ['title' => 'Image Upload Test', 'view' => 'cz-image-test']);
});

// Cashier / Subscriptions
Route::get('/subscription-checkout/{type}/{priceId}', [SubscriptionController::class, 'checkout'])->middleware(['auth', 'verified']);

// Socialite / Google OAuth login
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);
