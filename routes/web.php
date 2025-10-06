<?php

use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

// rota que entrega o arquivo
Route::get('/media/{uuid}', [MediaController::class, 'serveMedia'])
    ->name('media.serve')
    ->middleware('signed');

// rota que gera URL temporária sob demanda
Route::get('/media/temp/{media}', [MediaController::class, 'getTemporaryUrl'])
    ->name('media.temp')
    ->middleware('auth'); // só usuários logados