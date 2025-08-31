<?php

declare(strict_types=1);

namespace Avant\ZohoCRM;

use Illuminate\Support\ServiceProvider;

class ZohoCRMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/zoho-crm.php', 'zoho-crm');

        $this->app->singleton(Client::class, fn () => new Client());

        $this->app->singleton(CRM::class, fn () => new CRM(
            config('zoho-crm.modules')
        ));
    }
}
