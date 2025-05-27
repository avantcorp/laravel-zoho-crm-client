<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Avant\ZohoCRM\PushResponse;
use Avant\ZohoCRM\Records\Vehicle;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @mixin Client
 *
 * @method Vehicle|null getVehicles(string $id)
 * @method Vehicle getVehiclesOrFail(string $id)
 * @method Collection|PushResponse[] insertVehicles(Collection|Vehicle[]|Vehicle $records)
 * @method Collection|PushResponse[] updateVehicles(Collection|Vehicle[]|Vehicle $records)
 * @method Collection|PushResponse[] upsertVehicles(Collection|Vehicle[]|Vehicle $records)
 * @method Collection|PushResponse[] deleteVehicles(string[]|Collection|Vehicle[]|Vehicle $records)
 * @method Collection|Vehicle[] searchVehiclesOrFail(iterable $filters = [], iterable $params = [])
 * @method Collection|Vehicle[] listVehicles(iterable $params = [])
 * @method Collection|Vehicle[] listVehiclesOrFail(iterable $params = [])
 */
readonly class Vehicles extends Module
{
    /** @return Collection|Vehicle[] */
    public function searchVehicles(iterable $filters = [], iterable $params = []): Collection
    {
        $filters = collect($filters)
            ->put('Product_Category', 'Goods');

        $recordType = Arr::get(['Vehicles'], 'Vehicles', __NAMESPACE__.'\\Records\\'.Str::singular('Vehicles'));

        return $this->__searchRecords('Vehicles', $filters, $params)
            ->map(fn($record) => (array)$record)
            ->mapInto($recordType);
    }

    /** @return Collection|Vehicle[] */
    public function searchVehiclesByActive(iterable $filters = [], iterable $params = []): Collection
    {
        return $this->searchVehicles(
            array_merge($filters, ['Product_Active' => 'true']),
            $params
        );
    }

    /** @return Collection|Vehicle[] */
    public function searchVehiclesByAdvertisedOnWebsite(iterable $filters = [], iterable $params = []): Collection
    {
        return $this->searchVehicles(
            array_merge($filters, ['Website_Advert' => 'true']),
            $params
        );
    }

    public function getVehiclesByVrn(string $vrn): ?Vehicle
    {
        return $this->searchVehicles(['Product_Name' => $vrn])->first();
    }
}
