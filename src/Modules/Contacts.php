<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Avant\ZohoCRM\PushResponse;
use Avant\ZohoCRM\Records\Contact;
use Illuminate\Support\Collection;

/**
 * @mixin Client
 *
 * @method Contact|null getContacts(string $id)
 * @method Contact getContactsOrFail(string $id)
 * @method Collection|PushResponse[] insertContacts(Collection|Contact[]|Contact $records)
 * @method Collection|PushResponse[] updateContacts(Collection|Contact[]|Contact $records)
 * @method Collection|PushResponse[] upsertContacts(Collection|Contact[]|Contact $records)
 * @method Collection|PushResponse[] deleteContacts(string[]|Collection|Contact[]|Contact $records)
 * @method Collection|Contact[] searchContacts(iterable $filters = [], iterable $params = [])
 * @method Collection|Contact[] searchContactsOrFail(iterable $filters = [], iterable $params = [])
 * @method Collection|Contact[] listContacts(iterable $params = [])
 * @method Collection|Contact[] listContactsOrFail(iterable $params = [])
 */
readonly class Contacts extends Module
{
    public function getContactsByEmail(string $email): ?Contact
    {
        return $this->searchContacts(['Email' => $email])->first();
    }

    public function getContactsByPhone(string $phone): ?Contact
    {
        return $this->searchContacts(['Phone' => sanitise_phone($phone)])->first();
    }
}
