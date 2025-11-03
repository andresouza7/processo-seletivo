<?php

use App\Http\Controllers\MediaController;
use App\Models\Process;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

// rota que entrega o arquivo
Route::get('/media/{uuid}', [MediaController::class, 'serveMedia'])
    ->name('media.serve')
    ->middleware('signed');

// rota que gera URL temporária sob demanda
Route::get('/media/temp/{media}', [MediaController::class, 'getTemporaryUrl'])
    ->name('media.temp')
    ->middleware('auth'); // só usuários logados

Route::get('/media/view/{attachment}', [MediaController::class, 'showProcessAttachment'])
    ->name('media.view');

Route::get('/sitemap.xml', function () {
    $processes = Process::where('is_published', true)->limit(15)->get();

    $xml = view('sitemap', [
        'processes' => $processes,
    ])->render();

    return Response::make($xml, 200, [
        'Content-Type' => 'application/xml',
    ]);
});
