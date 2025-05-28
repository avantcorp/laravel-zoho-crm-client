<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Records\Deal;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

readonly class Deals extends Module
{
    public function searchDealsByVehicle(string $vehicleId): LazyCollection
    {
        return $this->client->__searchRecords($this->apiName, ['Vehicle' => $vehicleId]);
    }

    public function getLatestDealsByVehicle(string $vehicleId): Collection
    {
        return $this->client->__searchRecords("{$this->apiName}/$vehicleId")
            ->filter(fn (Deal $deal) => $deal->Stage !== 'Closed Lost')
            ->sortByDesc('Modified_Time')
            ->first();
    }
}
