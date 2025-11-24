<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\BetUserController;
use App\Http\Controllers\BetUserAuthController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ApplicationController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/jobmanager', function () {
    return Inertia::render('JobsManager');
})->middleware(['auth', 'verified'])->name('jobmanager');


Route::get('/employersmanager', function () {
    return Inertia::render('EmployersManager');
})->middleware(['auth', 'verified'])->name('employersmanager');

Route::get('/candidates', function () {
    return Inertia::render('CandidatesManager');
})->middleware(['auth', 'verified'])->name('candidates');

Route::get('/jobs', function () {
    return Inertia::render('Jobs/Index');
})->middleware(['auth', 'verified'])->name('jobs');

Route::get('/applications', function () {
    return Inertia::render('ApplicationManager');
})->middleware(['auth', 'verified'])->name('applications');

// Bet User Routes
Route::prefix('bet')->group(function () {
    // Authentication routes for Bet Users
    Route::middleware('guest:bet_user')->group(function () {
        Route::get('login', [BetUserAuthController::class, 'showLoginForm'])->name('bet.login');
        Route::post('login', [BetUserAuthController::class, 'login']);
        Route::get('register', [BetUserAuthController::class, 'showRegistrationForm'])->name('bet.register');
        Route::post('register', [BetUserAuthController::class, 'register'])->name('betuser.register');
    });

    // Protected routes for Bet Users
    Route::middleware('auth:bet_user')->group(function () {
        
        Route::post('logout', [BetUserAuthController::class, 'logout'])->name('bet.logout');
        
    });

    

});




Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('transfers', YadanaraungTransferController::class);

});

require __DIR__.'/auth.php';
