<?php

namespace App\Services\SelectionProcess;

use App\Models\Application;
use App\Models\Process;
use App\Notifications\NovaInscricaoNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApplicationService
{
    public function prepareFormData(array $data): array
    {
        $data['code'] = Application::generateUniqueCode();
        $data['candidate_id'] = Auth::guard('candidato')->id();
        $data['quota_id'] = $data['pcd'] ? 3 : 1; // defaults to Ampla Concorrencia

        return $data;
    }

    public function checkExistingDifferentPosition(int $candidateId, array $data): ?Application
    {
        return Application::query()
            ->where('candidate_id', $candidateId)
            ->where('process_id', $data['process_id'])
            ->where('position_id', '!=', $data['position_id'])
            ->orderBy('created_at', 'desc')
            ->first();
    }

    // Verifica se já existe uma inscrição igual.
    // public function checkExisting($candidateId, array $data): ?Application
    // {
    //     return Application::where('candidate_id', $candidateId)
    //         ->where('position_id', $data['position_id'])
    //         ->where('quota_id', $data['quota_id'])
    //         ->where('process_id', $data['process_id'])
    //         ->first();
    // }

    public function notifyApplicationCreated(Application $record): void
    {
        $record->candidate->notify(new NovaInscricaoNotification($record));
    }

    public function fetchLatestApplicationsForProcess(Process $process): Builder
    {
        $query = Application::where('process_id', $process->id);

        if ($process->multiple_applications) {
            // ✅ Pega a última inscrição do candidato em cada vaga (distinct por candidato + vaga)
            $query->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('applications')
                    ->groupBy('candidate_id', 'position_id', 'process_id');
            });
        } else {
            // ✅ Só a última inscrição do candidato, independente da vaga
            $query->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('applications')
                    ->groupBy('candidate_id', 'process_id');
            });
        }

        $query->orderByDesc('created_at');

        return $query;
    }
}
