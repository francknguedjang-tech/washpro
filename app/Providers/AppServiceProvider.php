<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            return (new MailMessage)
                ->subject('Réinitialisation de votre mot de passe - WashPro')
                ->greeting('Bonjour !')
                ->line('Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.')
                ->action('Réinitialiser le mot de passe', route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()]))
                ->line('Ce lien de réinitialisation de mot de passe expirera dans '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60).' minutes.')
                ->line('Si vous n\'avez pas demandé cette réinitialisation, aucune action supplémentaire n\'est requise de votre part.')
                ->salutation('Cordialement, L\'équipe WashPro');
        });
    }
}
