<?php

namespace App\Services\SelectionProcess;

use App\Models\Appeal;
use App\Models\AppealStage;
use App\Models\Application;
use App\Models\Process;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppealService
{
    public function canCreateAppealStage(Process $process): bool
    {
        return !$process->activeAppealStage();
    }

    public function canSubmitAppealForProcess(Process $process): bool
    {
        $today = now()->toDateString(); // 'Y-m-d'

        return $process->appeal_stage()
            ->whereDate('submission_start_date', '<=', $today)
            ->whereDate('submission_end_date', '>=', $today)
            ->exists();
    }

    // Lista inscrições que podem efetuar um recurso
    public function listAppealableApplications(): Collection
    {
        $today = now()->toDateString();

        $query = Application::where('candidate_id', Auth::guard('candidato')->id())
            ->where(function ($q) {
                $q->whereDoesntHave('appeals', function ($q) {
                    // No appeal for the latest appeal stage
                    $q->whereIn('appeal_stage_id', function ($sub) {
                        $sub->selectRaw('MAX(id)')
                            ->from('appeal_stages as s')
                            ->whereColumn('s.process_id', 'appeals.process_id');
                    });
                })
                    ->orWhereHas('process.appeal_stage', function ($q) {
                        // The process has a stage that allows multiple appeals
                        $q->whereExists(function ($sub) {
                            $sub->selectRaw(1)
                                ->from('appeal_stages as s')
                                ->whereColumn('s.process_id', 'process_id')
                                ->where('s.allow_many', true);
                        });
                    });
            })
            // mas o processo da inscrição deve ter uma etapa ativa aberta hoje
            ->whereHas('process.appeal_stage', function ($q) use ($today) {
                $q->where(function ($q) use ($today) {
                    $q->whereDate('submission_start_date', '<=', $today)
                        ->whereDate('submission_end_date', '>=', $today);
                });
            });

        return $query->with('position')->get();
    }

    public function createFromApplication(Application $application, array $data): Appeal
    {
        // roda em transacão, se uma etapa falhar, reverte tudo
        return DB::transaction(function () use ($application, $data) {
            // cria o recurso
            $appeal = Appeal::create([
                'candidate_id'    => $application->candidate_id,
                'application_id'  => $application->id,
                'process_id'      => $application->process_id,
                'appeal_stage_id' => $application->process->activeAppealStage()->id,
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

    public function listSubmittedAppeals(): Collection
    {
        $today = now()->toDateString();

        return Appeal::query()
            ->where('candidate_id', Auth::guard('candidato')->id())
            ->whereHas('appeal_stage', function ($query) use ($today) {
                $query->whereDate('result_start_date', '>=', $today);
            })
            ->latest()
            ->get();
    }

    public function listAppealsWithResults(): Collection
    {
        $today = now()->toDateString();

        return Appeal::query()
            ->whereHas('appeal_stage', function ($q) use ($today) {
                $q->whereDate('result_start_date', '<=', $today)
                    ->whereDate('result_end_date', '>=', $today);
            })->whereNotNull('result')
            ->latest()
            ->get();
    }
}
