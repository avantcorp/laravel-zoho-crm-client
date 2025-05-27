<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Avant\ZohoCRM\PushResponse;
use Avant\ZohoCRM\Records\Lead;
use Illuminate\Support\Collection;

/**
 * @mixin Client
 *
 * @method Lead|null getLeads(string $id)
 * @method Lead getLeadsOrFail(string $id)
 * @method Collection|PushResponse[] insertLeads(Collection|Lead[]|Lead $records)
 * @method Collection|PushResponse[] updateLeads(Collection|Lead[]|Lead $records)
 * @method Collection|PushResponse[] upsertLeads(Collection|Lead[]|Lead $records)
 * @method Collection|PushResponse[] deleteLeads(string[]|Collection|Lead[]|Lead $records)
 * @method Collection|Lead[] searchLeads(iterable $filters = [], iterable $params = [])
 * @method Collection|Lead[] searchLeadsOrFail(iterable $filters = [], iterable $params = [])
 * @method Collection|Lead[] listLeads(iterable $params = [])
 * @method Collection|Lead[] listLeadsOrFail(iterable $params = [])
 */
readonly class Leads extends Module
{

    /** @return Collection|Lead[] */
    public function listLeadsByVehicle(string $vehicleId): Collection
    {
        return $this->searchLeads(['Vehicle_of_Interest' => $vehicleId]);
    }

    /** @return Collection|Lead[] */
    public function listLeadsByParentLead(string $leadId): Collection
    {
        return $this->searchLeads(['Parent_Lead' => $leadId]);
    }

    public function getLeadsByEmail(string $email): ?Lead
    {
        return $this->searchLeads(['Email' => $email])->first();
    }

    public function getLeadsByPhone(string $phone): ?Lead
    {
        return $this->searchLeads(['Phone' => sanitise_phone($phone)])->first();
    }
}
