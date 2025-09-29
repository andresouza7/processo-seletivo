<?php

use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return (Auth::guard('candidato')->check())
        ? redirect()->route('filament.candidato.pages.dashboard')
        : redirect()->route('filament.app.pages.dashboard');
})->name('home');

Route::get('/login', function () {
    // Check if the user is coming from a protected route
    $protectedRoutes = [
        route('log-viewer.index'),
    ];

    if (in_array(url()->previous(), $protectedRoutes)) {
        return redirect()->route('filament.admin.auth.login');
    }

    // Default behavior
    return redirect()->route('filament.candidato.auth.login');
})->name('login');

// rota que entrega o arquivo
Route::get('/media/{uuid}', [MediaController::class, 'serveMedia'])
    ->name('media.serve')
    ->middleware('signed');

// rota que gera URL temporária sob demanda
Route::get('/media/temp/{media}', [MediaController::class, 'getTemporaryUrl'])
    ->name('media.temp')
    ->middleware('auth'); // só usuásrios logado