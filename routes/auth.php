<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
// use App\Http\Controllers\Auth\RegisteredUserController; // Dinonaktifkan: aplikasi khusus internal
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::post('login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('guest')->group(function () {
    // Aplikasi Internal: Registrasi publik dinonaktifkan, dialihkan ke login
    Route::get('register', fn() => redirect()->route('login'));

    // Aplikasi Internal Pers: Reset sandi mandiri publik dinonaktifkan demi keamanan sistem
    Route::get('forgot-password', fn() => redirect()->route('login')->with('warning', 'Fitur reset sandi publik dinonaktifkan demi keamanan. Silakan hubungi Administrator untuk reset kata sandi Anda.'))->name('password.request');
    Route::post('forgot-password', fn() => redirect()->route('login')->with('warning', 'Fitur reset sandi publik dinonaktifkan demi keamanan. Silakan hubungi Administrator untuk reset kata sandi Anda.'))->name('password.email');
    Route::get('reset-password/{token}', fn() => redirect()->route('login')->with('warning', 'Fitur reset sandi publik dinonaktifkan demi keamanan. Silakan hubungi Administrator untuk reset kata sandi Anda.'))->name('password.reset');
    Route::post('reset-password', fn() => redirect()->route('login')->with('warning', 'Fitur reset sandi publik dinonaktifkan demi keamanan. Silakan hubungi Administrator untuk reset kata sandi Anda.'))->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
