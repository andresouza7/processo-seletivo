<?php

namespace App\Services\MediaLibrary;

use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Define onde os arquivos de mídia serão armazenados, conforme o modelo associado.
 *
 * Regras:
 * 1. Se o model tiver o relacionamento `process`, usa a estrutura:
 *      {type}/{diretorio}
 * 2. Caso contrário, gera um caminho padrão com hash MD5:
 *      media/md5(media_id + app_key)
 * 3. Conversões e imagens responsivas ficam em:
 *      conversions/ e responsive-images/
 *
 * Observações:
 * - Anexos de processo seletivo são públicos.
 * - Demais arquivos ficam no disco local, acessíveis apenas por rota protegida.
 *
 * Exemplo:
 *   "editais/01_2025/"
 */

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->basePath($media);
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->basePath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->basePath($media) . 'responsive-images/';
    }

    private function basePath(Media $media): string
    {
        $process = $media->model?->process;

        return $process
            ? "{$process->type->slug}/{$process->directory}/"
            : $this->defaultPath($media);
    }

    private function defaultPath(Media $media): string
    {
        return md5($media->id . config('app.key'));
    }
}
