<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Records\Lead;
use Illuminate\Support\LazyCollection;

readonly class Leads extends Module
{
    public function listLeadsByVehicle(string $vehicleId): LazyCollection
    {
        return $this->client->__searchRecords($this->apiName, ['Vehicle_of_Interest' => $vehicleId]);
    }

    public function listLeadsByParentLead(string $leadId): LazyCollection
    {
        return $this->client->__searchRecords($this->apiName, ['Parent_Lead' => $leadId]);
    }

    public function getLeadsByEmail(string $email): ?Lead
    {
        return $this->client->__searchRecords($this->apiName, ['Email' => $email])->first();
    }

    public function getLeadsByPhone(string $phone): ?Lead
    {
        return $this->client->__searchRecords($this->apiName, ['Phone' => sanitise_phone($phone)])->first();
    }
}
