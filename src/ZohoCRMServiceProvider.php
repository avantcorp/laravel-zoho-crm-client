<?php

declare(strict_types=1);

namespace Avant\ZohoCRM;

use Illuminate\Support\ServiceProvider;

class ZohoCRMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, fn () => new Client());
    }
}
