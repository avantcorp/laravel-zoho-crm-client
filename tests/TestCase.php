<?php

declare(strict_types=1);

namespace Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            \Avant\Zoho\ZohoServiceProvider::class,
            \Avant\ZohoCRM\ZohoCRMServiceProvider::class,
        ];
    }
}
