<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'Diagnostic'   => \App\Models\Diagnostic::class,
            'Intervention' => \App\Models\Intervention::class,
            'Devis'        => \App\Models\Devis::class,
            'Facture'      => \App\Models\Facture::class,
            'User'         => \App\Models\User::class,
        ]);
    }
}
