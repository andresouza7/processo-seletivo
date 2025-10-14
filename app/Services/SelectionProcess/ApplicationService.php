<?php

namespace App\Services\SelectionProcess;

use App\Models\Application;
use App\Notifications\NovaInscricaoNotification;
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

    // Verifica se já existe uma inscrição igual.
    public function checkExisting($candidateId, array $data): ?Application
    {
        return Application::where('candidate_id', $candidateId)
            ->where('position_id', $data['position_id'])
            ->where('quota_id', $data['quota_id'])
            ->where('process_id', $data['process_id'])
            ->first();
    }

    public function notifyApplicationCreated(Application $record): void
    {
        $record->candidate->notify(new NovaInscricaoNotification($record));
    }
}
