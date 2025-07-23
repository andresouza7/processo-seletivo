<?php

namespace App\Services\MediaLibrary;

use App\Models\Inscricao;
use App\Models\ProcessoSeletivoAnexo;
use App\Models\Recurso;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Este gerador de caminhos personalizado define como os arquivos de mídia são armazenados
 * com base no modelo associado ao arquivo (media).
 *
 * Regras de armazenamento:
 * 
 * 1. Se o modelo for uma instância de:
 *    - ProcessoSeletivoAnexo: os arquivos são armazenados em:
 *        {tipo}/{diretorio}/{idprocesso_seletivo_anexo}/
 * 
 *    - Inscricao: os arquivos são armazenados em:
 *        {tipo}/{diretorio}/inscricoes/
 * 
 *    - Recurso: os arquivos são armazenados em:
 *        {tipo}/{diretorio}/recursos/
 *
 * 2. Se o modelo não for um dos acima, um caminho padrão é gerado com um hash MD5:
 *        md5(media_id + app_key)
 *
 * 3. Conversões e imagens responsivas são armazenadas dentro dos respectivos diretórios:
 *    - conversions/
 *    - responsive-images/
 *
 * Observação:
 * - Arquivos vinculados ao modelo ProcessoSeletivoAnexo são de acesso público na web.
 * - Arquivos vinculados a outros modelos são armazenados localmente e acessados
 *   somente através de uma rota protegida personalizada.
 *
 * Exemplo de caminho gerado:
 *    "seletivo2025/documentos/inscricoes/conversions/"
 */

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media);
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . 'responsive-images/';
    }

    private function getBasePath(Media $media): string
    {
        if (! $this->shouldHaveCustomPath($media)) {
            return $this->getDefaultPath($media);
        }

        $model = $media->model;

        $subfolder = match (true) {
            $model instanceof ProcessoSeletivoAnexo => $model->getAttribute('idprocesso_seletivo_anexo'),
            $model instanceof Inscricao             => 'inscricoes',
            $model instanceof Recurso               => 'recursos',
            default                                 => 'outros',
        };

        $processoSeletivo = optional($model->getAttribute('processo_seletivo'));
        $tipo = optional($processoSeletivo->tipo)->chave;
        $diretorio = $processoSeletivo->diretorio;

        return "{$tipo}/{$diretorio}/{$subfolder}/";
    }

    private function getDefaultPath(Media $media): string
    {
        return md5($media->id . config('app.key'));
    }

    private function shouldHaveCustomPath(Media $media): bool
    {
        return $media->model instanceof ProcessoSeletivoAnexo
            || $media->model instanceof Inscricao
            || $media->model instanceof Recurso;
    }
}
