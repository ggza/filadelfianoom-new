<?php

namespace App\Providers;

use App\Models\Section;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // MySQL 5.7 kompatibilitás (utf8mb4 indexek)
        Schema::defaultStringLength(191);

        // magyar feliratú, az admin felülethez illő lapozó
        Paginator::defaultView('vendor.pagination.admin');

        // Lábléc impresszuma minden nyilvános oldalon
        View::composer('layouts.site', function ($view) {
            static $imprint = null;
            if ($imprint === null) {
                $sec = Section::where('key', 'impresszum')->first();
                $imprint = $sec ? (string) $sec->body() : '';
            }
            $view->with('imprint', $imprint);
        });
    }
}
