<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Avant\ZohoCRM\PushResponse;
use Avant\ZohoCRM\Records\History;
use Illuminate\Support\Collection;

/**
 * @mixin Client
 *
 * @method History|null getHistories(string $id)
 * @method History getHistoriesOrFail(string $id)
 * @method Collection|PushResponse[] insertHistories(Collection|History[]|History $records)
 * @method Collection|PushResponse[] updateHistories(Collection|History[]|History $records)
 * @method Collection|PushResponse[] upsertHistories(Collection|History[]|History $records)
 * @method Collection|PushResponse[] deleteHistories(string[]|Collection|History[]|History $records)
 * @method Collection|History[] searchHistories(iterable $filters = [], iterable $params = [])
 * @method Collection|History[] searchHistoriesOrFail(iterable $filters = [], iterable $params = [])
 * @method Collection|History[] listHistories(iterable $params = [])
 * @method Collection|History[] listHistoriesOrFail(iterable $params = [])
 */
readonly class Histories extends Module
{
    /** @return Collection|History[] */
    public function listHistoriesByVehicle(string $vehicleId): Collection
    {
        return $this->searchHistories(['Parent_Id' => $vehicleId]);
    }
}
