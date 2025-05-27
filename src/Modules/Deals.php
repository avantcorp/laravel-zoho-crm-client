<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Avant\ZohoCRM\PushResponse;
use Avant\ZohoCRM\Records\Deal;
use Illuminate\Support\Collection;

/**
 * @mixin Client
 *
 * @method Deal|null getDeals(string $id)
 * @method Deal getDealsOrFail(string $id)
 * @method Collection|PushResponse[] insertDeals(Collection|Deal[]|Deal $records)
 * @method Collection|PushResponse[] updateDeals(Collection|Deal[]|Deal $records)
 * @method Collection|PushResponse[] upsertDeals(Collection|Deal[]|Deal $records)
 * @method Collection|PushResponse[] deleteDeals(string[]|Collection|Deal[]|Deal $records)
 * @method Collection|Deal[] searchDeals(iterable $filters = [], iterable $params = [])
 * @method Collection|Deal[] searchDealsOrFail(iterable $filters = [], iterable $params = [])
 * @method Collection|Deal[] listDeals(iterable $params = [])
 * @method Collection|Deal[] listDealsOrFail(iterable $params = [])
 */
readonly class Deals extends Module
{

    /** @return Collection|Deal[] */
    public function searchDealsByVehicle(string $vehicleId): Collection
    {
        return $this->searchDeals(['Vehicle' => $vehicleId]);
    }

    public function getLatestDealsByVehicle(string $vehicleId): ?Deal
    {
        return $this->searchDealsByVehicle($vehicleId)
            ->filter(fn(Deal $deal) => $deal->Stage !== 'Closed Lost')
            ->sortByDesc('Modified_Time')
            ->first();
    }
}
