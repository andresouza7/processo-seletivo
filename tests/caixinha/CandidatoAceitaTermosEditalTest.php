<?php

namespace Tests\Feature;

use App\Filament\Candidato\Pages\Auth\RequestPasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use App\Models\InscricaoPessoa;
use App\Notifications\ResetPasswordNotification;
use Filament\Facades\Filament;
use Livewire\Livewire;

class CandidatoAceitaTermosEditalTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidato_must_accept_edital_terms(): void
    {
       
    }

   
}
