<?php

namespace App\Providers;

use App\Models\PageView;
use App\Models\Property;
use App\Models\Variable;
use App\Models\Appartement;
use App\Models\Tarification;
use App\Observers\PropertyObserver;
use App\Observers\VariableObserver;
use App\Observers\AppartementObserver;
use Illuminate\Support\Facades\Schema;
use App\Observers\TarificationObserver;
use Illuminate\Support\ServiceProvider;
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
        Schema::defaultStringLength(191);

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('🔑 Réinitialisation de votre mot de passe')
                ->view('mail.password_reset', [
                    'url' => $url,
                    'user' => $notifiable,
                ]);
        });

        // Appartement Observer
        Appartement::observe(AppartementObserver::class);
        Tarification::observe(TarificationObserver::class);
        Property::observe(PropertyObserver::class);
        Variable::observe(VariableObserver::class);

}
}
