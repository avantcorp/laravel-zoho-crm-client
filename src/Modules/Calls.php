<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Illuminate\Support\LazyCollection;

readonly class Calls extends Module
{
    public function listCallsByLead(string $leadId, iterable $query = []): LazyCollection
    {
        return $this->client->__listRequest("Leads/{$leadId}/{$this->apiName}", $query)
            ->mapInto($this->recordClass());
    }

    public function listCallsByContact(string $contactId, iterable $query = []): LazyCollection
    {
        return $this->client->__listRequest("Contacts/{$contactId}/{$this->apiName}", $query)
            ->mapInto($this->recordClass());
    }
}
