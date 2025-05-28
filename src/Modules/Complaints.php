<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Illuminate\Support\LazyCollection;

readonly class Complaints extends Module
{
    public function listComplaintsByContact(string $contactId): LazyCollection
    {
        return $this->client->__listRequest("Contacts/{$contactId}/Complaints")
            ->mapInto($this->recordClass());
    }
}
