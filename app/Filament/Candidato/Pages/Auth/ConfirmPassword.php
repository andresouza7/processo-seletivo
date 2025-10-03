<?php

namespace App\Filament\Candidato\Pages\Auth;

use App\Notifications\ConfirmEmailNotification;
use Exception;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt as BaseConfirmEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class ConfirmCandidatoEmail extends BaseConfirmEmail {
    protected function sendEmailVerificationNotification(MustVerifyEmail $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        // $notification = app(VerifyEmail::class);
        // $notification->url = Filament::getVerifyEmailUrl($user);
        $notification = new ConfirmEmailNotification("");

        $user->notify($notification);
    }

    public function resendNotificationAction(): Action
    {
        return Action::make('resendNotification')
            ->link()
            ->label(__('filament-panels::pages/auth/email-verification/email-verification-prompt.actions.resend_notification.label') . '.')
            ->action(function (): void {
                try {
                    $this->rateLimit(2);
                } catch (TooManyRequestsException $exception) {
                    $this->getRateLimitedNotification($exception)?->send();

                    return;
                }

                $this->sendEmailVerificationNotification($this->getVerifiable());

                Notification::make()
                    ->title(__('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resent.title'))
                    ->success()
                    ->send();
            });
    }
}