<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Records\Contact;

readonly class Contacts extends Module
{
    public function getContactsByEmail(string $email): ?Contact
    {
        return $this->client->__searchRecords($this->apiName, ['Email' => $email])->first();
    }

    public function getContactsByPhone(string $phone): ?Contact
    {
        return $this->client->__searchRecords($this->apiName, ['Phone' => sanitise_phone($phone)])->first();
    }
}
