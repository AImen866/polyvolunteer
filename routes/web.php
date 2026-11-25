<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// Публичная часть
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/search', [EventController::class, 'search'])->name('events.search');
Route::get('/events/{event}', [EventController::class, 'show'])->whereNumber('event')->name('events.show');
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator');
Route::post('/calculator', [CalculatorController::class, 'calculate'])->name('calculator.calculate');

// Гости
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Авторизованные пользователи
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Волонтёр: заявки
    Route::middleware('role:volunteer')->group(function () {
        Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('registrations.store');
        Route::delete('/events/{event}/register', [RegistrationController::class, 'destroy'])->name('registrations.destroy');
    });

    // Организатор (и администратор): свои мероприятия
    Route::middleware('role:organizer,admin')->prefix('organizer')->name('organizer.')->group(function () {
        Route::get('/events', [OrganizerController::class, 'index'])->name('events');
        Route::get('/events/create', [OrganizerController::class, 'create'])->name('events.create');
        Route::post('/events', [OrganizerController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [OrganizerController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [OrganizerController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [OrganizerController::class, 'destroy'])->name('events.destroy');
        Route::get('/events/{event}/applications', [OrganizerController::class, 'applications'])->name('applications');
        Route::patch('/registrations/{registration}', [OrganizerController::class, 'updateRegistration'])->name('registrations.update');
    });

    // Администратор
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [Admin\UserController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [Admin\UserController::class, 'index'])->name('users');
        Route::patch('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/events', [Admin\EventController::class, 'index'])->name('events');
        Route::resource('articles', Admin\ArticleController::class)->except('show');
        Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    });
});
