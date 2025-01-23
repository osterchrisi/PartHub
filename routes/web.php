<?php

use App\Http\Controllers\Auth\DemoLoginController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DatabaseServiceController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FootprintController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockLevelController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierDataController;
use App\Http\Controllers\UserSettingController;
use App\Models\User;
use App\Providers\RouteServiceProvider;
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
Route::patch('/updateCell', [DatabaseServiceController::class, 'updateCell'])->middleware(['auth', 'verified']);
Route::post('/deleteRow', [DatabaseServiceController::class, 'deleteRow'])->middleware(['auth', 'verified']);

//* Part Routes
Route::middleware(['redirect.if.not.authenticated', 'auth', 'verified'])->group(function () {
    Route::controller(PartController::class)->group(function () {
        Route::get('/parts', 'index')->name('parts');
        Route::get('/part/{id}', 'show');
        Route::get('/part.getName', 'getName');
        Route::post('/parts.requestStockChange', 'handleStockRequests');
        Route::post('/part.create', 'create')->middleware('resource.limits');
        Route::get('/parts.partsTable', 'index')->name('parts.partsTable');
        Route::get('/search-mouser-part/{searchTerm}', 'searchMouserPartNumber');
        Route::get('/parts/{id}/alternatives', 'PartController@getAlternatives');
        Route::post('/parts/{id}/alternatives', 'PartController@addAlternative');
        Route::delete('/parts/{id}/alternatives/{alt_id}', 'PartController@removeAlternative');

    });
});

//* BOM Routes
Route::controller(BomController::class)->group(function () {
    Route::get('/boms', 'index')->middleware(['auth', 'verified'])->name('boms');
    Route::get('/boms.bomsTable', 'index')->middleware(['auth', 'verified'])->name('boms.bomsTable');
    Route::get('/bom/{id}', 'show')->middleware(['auth', 'verified']);
    Route::post('/bom.assemble', 'prepareBomForAssembly')->middleware(['auth', 'verified']);
    Route::post('/bom.import', 'importBom')->name('bom.import')->middleware('resource.limits');
    Route::get('/bom.import-form', function () {
        return view('boms.import-form', ['title' => 'Import BOM']);
    })->middleware(['auth', 'verified'])->name('bom import');

});

//* Location Routes
Route::controller(LocationController::class)->group(function () {
    Route::get('/locations', 'index')->middleware(['auth', 'verified'])->name('locations');
    Route::get('/locations.locationsTable', 'index')->middleware(['auth', 'verified'])->name('locations.locationsTable');
    Route::post('/location.create', 'create')->middleware(['auth', 'verified', 'resource.limits']);
    Route::get('/locations.get', 'getLocations')->middleware(['auth', 'verified']);
    Route::get('/location/{id}', 'show')->middleware(['auth', 'verified']);
});

//* Category Routes
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index')->middleware(['auth', 'verified'])->name('categories');
    Route::get('/categories.categoriesTable', 'index')->middleware(['auth', 'verified'])->name('categories.categoriesTable');
    Route::get('/category/{id}', 'show')->middleware(['auth', 'verified']);
    Route::get('/categories.get', 'list')->middleware(['auth', 'verified']);
    Route::post('/category.create', 'create')->middleware(['auth', 'verified', 'resource.limits']);
});

//* Supplier Routes
Route::controller(SupplierController::class)->group(function () {
    Route::get('/suppliers', 'index')->middleware(['auth', 'verified'])->name('suppliers');
    Route::get('/suppliers.suppliersTable', 'index')->middleware(['auth', 'verified'])->name('suppliers.suppliersTable');
    Route::post('/supplier.create', 'create')->middleware(['auth', 'verified', 'resource.limits']);
    Route::get('/suppliers.get', 'getSuppliers')->middleware(['auth', 'verified']);
    Route::get('/supplier/{id}', 'show')->middleware(['auth', 'verified']);
});
Route::post('supplierData.create', [SupplierDataController::class, 'create'])->middleware(['auth', 'verified', 'resource.limits']);

//* Footprint Routes
Route::middleware(['auth', 'verified', 'subscription'])->group(function () {
    Route::controller(FootprintController::class)->group(function () {
        Route::get('/footprints', 'index')->middleware(['auth', 'verified'])->name('footprints');
        Route::get('/footprints.footprintsTable', 'index')->middleware(['auth', 'verified'])->name('footprints.footprintsTable');
        Route::post('/footprint.create', 'create')->middleware(['auth', 'verified', 'resource.limits']);
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
    Route::post('/reorder-images/{type}/{id}', [ImageController::class, 'reorderImages']);
});

//* File Controller
Route::get('/files/{fileType}/{type}/{userId}/{id}/{filename}', [FileController::class, 'serveFile'])->middleware(['auth', 'verified']);
Route::get('/files/images/{type}/{userId}/{id}/thumbnails/{filename}', [FileController::class, 'serveThumbnail'])->middleware(['auth', 'verified']);

//* Document Controller
Route::get('/documents/{type}/{id}', [DocumentController::class, 'getDocumentsByTypeAndId']);
Route::post('/upload-document/{type}/{id}', [DocumentController::class, 'upload'])->middleware(['auth', 'verified'])->name('upload-document');
Route::delete('/delete-document/{type}/{id}', [DocumentController::class, 'delete'])->middleware(['auth', 'verified'])->name('delete-document');

//* Cashier / Subscriptions
Route::get('/subscription-checkout/{plan}/{priceId}', [SubscriptionController::class, 'checkout'])->middleware(['auth', 'verified'])->name('subscription.checkout');
Route::get('/subscription-manage', [SubscriptionController::class, 'manage'])->middleware(['auth', 'verified'])->name('subscription.manage');

//* Socialite / Google OAuth login
Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

//* User Settings
Route::middleware('auth')->group(function () {
    Route::get('/settings/{setting_name}', [UserSettingController::class, 'getUserSetting'])->middleware(['auth', 'verified']);
    Route::post('/settings/{setting_name}', [UserSettingController::class, 'updateUserSetting'])->middleware(['auth', 'verified']);
});
Route::get('/user-settings', [UserSettingController::class, 'index'])->middleware(['auth', 'verified'])->name('user-settings');
Route::post('/user/settings', [UserSettingController::class, 'update'])->name('user.settings.update')->middleware('auth');

//* Standalone Pages Routes

// Sign Up for new users
Route::get('/signup', function () {
    //! Passwort darf nicht länger als 72 Zeichen sein! (wegen bcrypt -> jetzt argon2)
    //! Passwort darf keine Leerzeichen enthalten
    //TODO: Das geht wahrscheinlich sauberer...
    if (config('app.env') == 'demo') {
        return redirect('https://parthub.online/signup');
    }
    else {
        return view('auth.register', ['title' => 'Signup', 'view' => 'signup']);
    }
})
    ->name('signup');

// What is PartHub?
Route::get('/whatis', function () {
    return view('whatis', ['title' => 'Open-source inventory management', 'view' => 'whatis']);
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

// Support
Route::get('/support', function () {
    return view('support', ['title' => 'Support', 'view' => 'support']);
})
    ->name('support');

// My 'Multi View' Tryout
Route::get('/multi', function () {
    return view('multi', ['title' => 'Multi Test', 'view' => 'multi']);
});

// Demo User Login
Route::get('/demo-login', [DemoLoginController::class, 'login'])->name('demo.login');

//* For testing
Route::get('/image-testing', function () {
    return view('cz-image-test', ['title' => 'Image Upload Test', 'view' => 'cz-image-test']);
});

Route::get('register-testing', function () {
    return view('auth/register-testing', ['title' => 'Create Your Free Account', 'view' => 'register-testing']);
});

Route::get('/test-flash', function () {
    return redirect()->route('welcome')->with('firstLogin', true);
});

// E-Mail Preview
Route::get('/preview-email', function () {
    $user = User::first();

    return new App\Mail\WelcomeEmail($user);
});
