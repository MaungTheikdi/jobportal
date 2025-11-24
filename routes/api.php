<?php
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\BetUserAuthApiController;
use App\Http\Controllers\BetUserAuthController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\EmployerController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthMobileController;

// Public API routes
Route::post('/betuser/login', [BetUserAuthApiController::class, 'login']);
Route::post('/betuser/register', [BetUserAuthApiController::class, 'register']);


Route::post('/register', [AuthMobileController::class, 'register']);
Route::post('/login', [AuthMobileController::class, 'login']);
Route::get('/jobs', [JobController::class, 'index']);



//Jobs
Route::post('/jobs', [JobController::class, 'store']);
Route::patch('/jobs/{id}', [JobController::class, 'update']);
Route::delete('/jobs/{id}', [JobController::class, 'destroy']);

//employers
Route::get('/employers', [EmployerController::class, 'index']);
Route::post('/employers', [EmployerController::class, 'store']);
Route::post('/employer/{id}', [EmployerController::class, 'update']);
Route::delete('/delete/employer/{id}', [EmployerController::class, 'destroy']);

//candidates
Route::get('/candidates', [CandidateController::class, 'index']);
Route::post('/candidates', [CandidateController::class, 'store']);
Route::post('/candidate/{id}', [CandidateController::class, 'update']);
Route::delete('/delete/candidate/{id}', [CandidateController::class, 'destroy']);

//applications
Route::get('/applications', [ApplicationController::class, 'index']);
Route::post('/applications', [ApplicationController::class, 'store']);
Route::post('/application/{id}', [ApplicationController::class, 'update']);
Route::delete('/delete/application/{id}', [ApplicationController::class, 'destroy']);


//Protected routes (requires API token)
Route::middleware('auth:sanctum')->group(function () {
	Route::get('/profile/{user_id}', [BetUserAuthApiController::class, 'profile']);
	Route::get('/get/single_bets_list', [BetUserAuthApiController::class, 'singleBetList']);
	Route::get('/user/transaction/{userId}', [BetUserAuthController::class, 'getUserTransaction']);
	Route::get('/user/match_result', [BetUserAuthController::class, 'getAllMatchResults']);
	Route::get('/user/history/{user_id}', [BetUserAuthApiController::class, 'getUserBetsData']);

	Route::post('/create/single_bet', [BetUserAuthApiController::class, 'createSingleBet']);
	Route::post('/create/multi_bet', [BetUserAuthApiController::class, 'createMultiBet']);
	Route::get('/user/multibethistory/{user_id}', [BetUserAuthApiController::class, 'getUserMultiBetsData']);


});
