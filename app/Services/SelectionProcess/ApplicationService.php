<?php

namespace App\Services\SelectionProcess;

use App\Models\AppealStage;
use App\Models\Application;
use App\Models\Process;
use App\Notifications\NovaInscricaoNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApplicationService
{
    public function canApplyForProcess(Process $process)
    {
        $today = now()->toDateString(); // Get current date in 'Y-m-d' format

        return $process->position->count() > 0 &&
            $process->application_start_date <= $today &&
            $process->application_end_date >= $today;
    }

    public function notifyApplicationCreated(Application $record): void
    {
        $record->candidate->notify(new NovaInscricaoNotification($record));
    }

    public function fetchFinalApplicationsForProcess(Process $process): Builder
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
