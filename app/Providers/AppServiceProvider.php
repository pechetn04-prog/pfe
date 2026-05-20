<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\ParametreSociete;

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

        View::composer('partials.sidebar', function ($view) {
            $view->with('societe', ParametreSociete::first());
        });
    }
}
