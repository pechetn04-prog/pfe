<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'Diagnostic'   => \App\Models\Diagnostic::class,
            'Intervention' => \App\Models\Intervention::class,
            'Devis'        => \App\Models\Devis::class,
            'Facture'      => \App\Models\Facture::class,
            'User'         => \App\Models\User::class,
        ]);
    }
}
