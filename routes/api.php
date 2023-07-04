<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\VerificationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//---------------------------- Authentication Module ----------------------------
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout']);

//---------------------------- Reset Password Module ----------------------------
Route::post('password/email-link', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email-link');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');

//---------------------------- Email Verification Module ----------------------------
Route::get('email/verify/{email}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
