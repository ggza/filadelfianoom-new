<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AdminCodeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// Nyilvános oldal
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::post('/jelentkezes', [SiteController::class, 'storeAdmission'])->middleware('throttle:5,10')->name('admission.store');
Route::post('/kapcsolat', [SiteController::class, 'storeContact'])->middleware('throttle:5,10')->name('contact.store');

// Belépés, regisztráció
Route::middleware('guest')->group(function () {
    Route::get('/belepes', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/belepes', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/belepes/kod', [AdminCodeController::class, 'show'])->name('login.code');
    Route::post('/belepes/kod', [AdminCodeController::class, 'verify'])->middleware('throttle:10,10');
    Route::post('/belepes/kod/ujra', [AdminCodeController::class, 'resend'])->middleware('throttle:3,10')->name('login.code.resend');
    Route::get('/regisztracio', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/regisztracio', [AuthController::class, 'register'])->middleware('throttle:5,10');

    Route::get('/elfelejtett-jelszo', [PasswordResetController::class, 'showRequest'])->name('password.request');
    Route::post('/elfelejtett-jelszo', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,10')->name('password.email');
    Route::get('/jelszo-visszaallitas/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/jelszo-visszaallitas', [PasswordResetController::class, 'reset'])->middleware('throttle:10,10')->name('password.update');
});
Route::post('/kilepes', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Hozzátartozói oldal
Route::middleware('auth')->group(function () {
    Route::get('/fiok', [AccountController::class, 'index'])->name('account');
    Route::put('/fiok/jelszo', [AccountController::class, 'password'])->name('account.password');
    Route::get('/fiok/foto/{photo}/{size?}', [AccountController::class, 'photo'])->whereIn('size', ['full', 'thumb'])->name('account.photo');
});

// Adminisztráció
Route::middleware(['auth', 'admin', 'admin.idle'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::post('/keepalive', [Admin\DashboardController::class, 'keepalive'])->name('keepalive');

    Route::get('/szovegek', [Admin\SectionController::class, 'index'])->name('sections.index');
    Route::get('/szovegek/{section}', [Admin\SectionController::class, 'edit'])->name('sections.edit');
    Route::put('/szovegek/{section}', [Admin\SectionController::class, 'update'])->name('sections.update');

    Route::get('/galeria', [Admin\GalleryController::class, 'index'])->name('gallery.index');
    Route::post('/galeria', [Admin\GalleryController::class, 'store'])->name('gallery.store');
    Route::put('/galeria', [Admin\GalleryController::class, 'updateAll'])->name('gallery.update');
    Route::delete('/galeria/{image}', [Admin\GalleryController::class, 'destroy'])->name('gallery.destroy');

    Route::resource('bejegyzesek', Admin\PostController::class)->except('show')
        ->parameters(['bejegyzesek' => 'post'])->names('posts');

    Route::get('/jelentkezesek', [Admin\AdmissionController::class, 'index'])->name('admissions.index');
    Route::get('/jelentkezesek/{admission}', [Admin\AdmissionController::class, 'show'])->name('admissions.show');
    Route::put('/jelentkezesek/{admission}', [Admin\AdmissionController::class, 'update'])->name('admissions.update');
    Route::delete('/jelentkezesek/{admission}', [Admin\AdmissionController::class, 'destroy'])->name('admissions.destroy');

    Route::get('/uzenetek', [Admin\ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('/uzenetek/{message}', [Admin\ContactMessageController::class, 'show'])->name('messages.show');
    Route::put('/uzenetek/{message}', [Admin\ContactMessageController::class, 'update'])->name('messages.update');
    Route::delete('/uzenetek/{message}', [Admin\ContactMessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('/felhasznalok', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/felhasznalok/{user}', [Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/felhasznalok/{user}', [Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/felhasznalok/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::resource('lakok', Admin\ResidentController::class)->parameters(['lakok' => 'resident'])->names('residents');
    Route::post('/lakok/{resident}/hirek', [Admin\ResidentController::class, 'storeUpdate'])->name('residents.updates.store');
    Route::delete('/lakohirek/{update}', [Admin\ResidentController::class, 'destroyUpdate'])->name('residents.updates.destroy');
    Route::delete('/lakofotok/{photo}', [Admin\ResidentController::class, 'destroyPhoto'])->name('residents.photos.destroy');
});
