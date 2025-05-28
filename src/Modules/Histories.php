<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Illuminate\Support\LazyCollection;

readonly class Histories extends Module
{
    public function listHistoriesByVehicle(string $vehicleId): LazyCollection
    {
        return $this->client->__searchRecords($this->apiName, ['Parent_Id' => $vehicleId]);
    }
}
