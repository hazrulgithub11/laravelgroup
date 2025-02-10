<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProviderController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\ProviderRegisterController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Provider\ProfileController;
use App\Http\Controllers\TelegramController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Welcome page (accessible to all)
Route::get('/', function () {
    return view('welcome');
});

// Default Authentication Routes
Auth::routes();

// User routes (accessible only when logged in as user)
Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Admin routes
    Route::prefix('admin')->middleware(['admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        // Add more admin routes
    });

    // // Movie routes
    // Route::resource('movies', MovieController::class);

    Route::post('/save-location', [LocationController::class, 'store'])
        ->name('location.store')
        ->middleware('auth');

    // Orders routes - Note the order!
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Make sure this is inside the auth middleware group if you have one
    Route::post('/test-telegram-notification', [TelegramController::class, 'sendTestNotification'])
        ->name('test.telegram.notification');
});

// Add this route to test the master layout
Route::get('/test-layout', function () {
    return view('admin.layouts.master');
});

// Provider Routes
Route::prefix('provider')->name('provider.')->group(function () {
    // Guest provider routes
    Route::middleware('guest:provider')->group(function () {
        Route::get('/login', [ProviderController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [ProviderController::class, 'login'])->name('login.submit');
        Route::get('/register', [ProviderController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [ProviderController::class, 'register']);
    });

    // Authenticated provider routes
    Route::middleware('auth:provider')->group(function () {
        Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
        
        // Orders routes
        Route::get('/orders', [ProviderDashboardController::class, 'orders'])->name('orders.index');
        Route::put('/orders/{order}/update', [ProviderDashboardController::class, 'updateOrderStatus'])->name('orders.update');
        Route::put('/orders/{order}/complete', [ProviderDashboardController::class, 'completeOrder'])->name('orders.complete');
        
        // Other routes...
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/logout', [ProviderController::class, 'logout'])->name('logout');
    });
});

Route::post('/store-location', [App\Http\Controllers\HomeController::class, 'storeLocation'])
    ->name('store.location')
    ->middleware('auth');

Route::post('/provider/register', [ProviderRegisterController::class, 'register'])->name('provider.register');

Route::get('/telegram/test', function() {
    $botToken = config('services.telegram-bot-api.token');
    $response = Http::get("https://api.telegram.org/bot{$botToken}/getMe");
    return response()->json($response->json());
});

Route::get('/provider/{id}/profile', [App\Http\Controllers\ProviderController::class, 'showProfile'])->name('provider.profile.show');


