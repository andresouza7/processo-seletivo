<?php

namespace App\Http\Controllers;

use App\Models\Inscricao;
use App\Models\Recurso;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Abort;

class AnexoController extends Controller
{
    public function showAnexoRecurso($id)
    {
        $recurso = Recurso::findOrFail($id);
        return $this->serveMedia($recurso, 'anexo_recurso');
    }

    public function showAnexoInscricao($id)
    {
        $inscricao = Inscricao::findOrFail($id);
        return $this->serveMedia($inscricao, 'documentos_requeridos');
    }

    public function showAnexoLaudoMedico($id)
    {
        $inscricao = Inscricao::findOrFail($id);
        return $this->serveMedia($inscricao, 'laudo_medico');
    }

    private function serveMedia($model, string $collection)
    {
        if (! $model->hasMedia($collection)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $media = $model->getFirstMedia($collection);

        return response()->file($media->getPath(), [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
