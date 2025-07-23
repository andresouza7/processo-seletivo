<?php

use App\Http\Controllers\AnexoController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/recurso/{id}/anexo', [AnexoController::class, 'showAnexoRecurso'])
        ->name('recurso.anexo');
    Route::get('/inscricoes/{id}/anexo', [AnexoController::class, 'showAnexoInscricao'])
        ->name('inscricoes.anexo');
    Route::get('/inscricoes/{id}/laudo', [AnexoController::class, 'showAnexoLaudoMedico'])
        ->name('inscricoes.laudo');
});