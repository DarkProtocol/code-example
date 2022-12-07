<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Data\Models\MfaAction;

/*
|--------------------------------------------------------------------------
| Service - API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for this service.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/user', 'RegisterController');
    Route::post('/user/activate', 'UserActivateController');
    Route::post('/resend-confirmation-email', 'ResendConfirmationEmailController')
        ->middleware('throttle.input:3,15,email');
    Route::post('/login', 'LoginController')->name('login');
    Route::post('/reset', 'PasswordResetController');
    Route::post('/reset/complete', 'PasswordResetCompleteController');

    Route::middleware('auth')->group(function () {
        Route::get('/user', 'UserController');
        Route::post('/token/refresh', fn () => abort(204))->name('auth.token.refresh');
        Route::post('/logout', 'LogoutController');
    });
});
