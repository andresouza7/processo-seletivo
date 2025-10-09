<?php

namespace Tests\Feature;

use App\Filament\Candidato\Pages\Auth\RequestPasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use App\Models\Candidate;
use App\Notifications\ResetPasswordNotification;
use Filament\Facades\Filament;
use Livewire\Livewire;

class CandidatoRedefineSenhaTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidato_can_request_password_reset(): void
    {
        Notification::fake();

        $user = Candidate::factory()->create([
            'email' => 'andre.costa@ueap.edu.br',
            'password' => Hash::make('old-password'),
        ]);

        Livewire::test(RequestPasswordReset::class)
            ->assertFormExists()
            ->assertFormFieldExists('email')
            ->fillForm(['email' => 'candidato@gmail.com'])
            ->assertFormSet(['email' => 'candidato@gmail.com']);
    }

    public function test_candidato_receives_reset_password_link(): void {

    }

    public function test_candidato_can_reset_password_with_token(): void
    {
        $user = Candidate::factory()->create([
            'email' => 'candidato@example.com',
            'password' => Hash::make('old-password'),
        ]);

        // Generate token with the correct broker
        $token = Password::broker('candidate')->createToken($user);

        // Submit the reset form
        $url = Filament::getPanel('candidato')->getResetPasswordUrl($token, $user);

        $response = $this->get($url);

        $response->assertStatus(200);
    }
}
