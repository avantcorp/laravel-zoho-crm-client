<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Records\Vendor;

readonly class Vendors extends Module
{
    public function getVendorsByEmail(string $email): ?Vendor
    {
        return $this->client->__searchRecords($this->apiName, ['Email' => $email])->first();
    }

    public function getVendorsByPhone(string $phone): ?Vendor
    {
        return $this->client->__searchRecords($this->apiName, ['Phone' => sanitise_phone($phone)])->first();
    }
}
