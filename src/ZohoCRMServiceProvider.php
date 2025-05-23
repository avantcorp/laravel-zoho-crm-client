<?php

namespace Avant\ZohoCRM;

use Illuminate\Support\ServiceProvider;

class ZohoCRMServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, fn () => new Client());
    }
}