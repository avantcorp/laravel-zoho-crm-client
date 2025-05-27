<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Avant\ZohoCrm\PushResponse;
use Avant\ZohoCrm\Records\Call;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

/**
 * @mixin Client
 *
 * @method Call|null getCalls(string $id)
 * @method Call getCallsOrFail(string $id)
 * @method Collection|PushResponse[] insertCalls(Collection|Call[]|Call $records)
 * @method Collection|PushResponse[] updateCalls(Collection|Call[]|Call $records)
 * @method Collection|PushResponse[] upsertCalls(Collection|Call[]|Call $records)
 * @method Collection|PushResponse[] deleteCalls(string[]|Collection|Call[]|Call $records)
 * @method Collection|Call[] searchCalls(iterable $filters = [], iterable $params = [])
 * @method Collection|Call[] searchCallsOrFail(iterable $filters = [], iterable $params = [])
 * @method Collection|Call[] listCalls(iterable $params = [])
 * @method Collection|Call[] listCallsOrFail(iterable $params = [])
 */
readonly class Calls extends Module
{
    public function listCallsByLead(string $leadId, iterable $query = []): LazyCollection
    {
        return $this->client->__listRequest("Leads/$leadId/{$this->apiName}", $query);
    }

    public function listCallsByContact(string $contactId, iterable $query = []): LazyCollection
    {
        return $this->client->__listRequest("Contacts/$contactId/{$this->apiName}", $query);
    }
}
