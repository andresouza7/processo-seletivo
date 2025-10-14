<?php

namespace App\Services\SelectionProcess;

use App\Models\Appeal;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class AppealService
{
    public function createFromApplication(Application $application, array $data): Appeal
    {
        // roda em transacão, se uma etapa falhar, reverte tudo
        return DB::transaction(function () use ($application, $data) {
            // cria o recurso
            $appeal = Appeal::create([
                'candidate_id'    => $application->candidate_id,
                'application_id'  => $application->id,
                'appeal_stage_id' => $application->activeAppealStage()->id,
                'text'            => $data['text'],
            ]);

            // armazena o anexo 
            if (!empty($data['anexo_candidato'])) {
                foreach ((array) $data['anexo_candidato'] as $file) {
                    $appeal->addMedia($file->getRealPath())
                        ->usingFileName($file->getClientOriginalName())
                        ->toMediaCollection('anexo_candidato', 'local');
                }
            }

            return $appeal;
        });
    }
}
