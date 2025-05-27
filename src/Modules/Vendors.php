<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Avant\ZohoCRM\PushResponse;
use Avant\ZohoCRM\Records\Vendor;
use Illuminate\Support\Collection;

/**
 * @mixin Client
 *
 * @method Vendor|null getVendors(string $id)
 * @method Vendor getVendorsOrFail(string $id)
 * @method Collection|PushResponse[] insertVendors(Collection|Vendor[]|Vendor $records)
 * @method Collection|PushResponse[] updateVendors(Collection|Vendor[]|Vendor $records)
 * @method Collection|PushResponse[] upsertVendors(Collection|Vendor[]|Vendor $records)
 * @method Collection|PushResponse[] deleteVendors(string[]|Collection|Vendor[]|Vendor $records)
 * @method Collection|Vendor[] searchVendors(iterable $filters = [], iterable $params = [])
 * @method Collection|Vendor[] searchVendorsOrFail(iterable $filters = [], iterable $params = [])
 * @method Collection|Vendor[] listVendors(iterable $params = [])
 * @method Collection|Vendor[] listVendorsOrFail(iterable $params = [])
 */
readonly class Vendors extends Module
{
    public function getVendorsByEmail(string $email): ?Vendor
    {
        return $this->searchVendors(['Email' => $email])->first();
    }

    public function getVendorsByPhone(string $phone): ?Vendor
    {
        return $this->searchVendors(['Phone' => sanitise_phone($phone)])->first();
    }
}
