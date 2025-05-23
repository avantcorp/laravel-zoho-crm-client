<?php

namespace Avant\ZohoClient\Crm;

use Illuminate\Support\ServiceProvider;

class ZohoCRMServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ZohoCRMClient::class, fn () => new ZohoCRMClient());
    }
}