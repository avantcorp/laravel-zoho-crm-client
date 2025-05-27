<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Avant\ZohoCrm\PushResponse;
use Avant\ZohoCrm\Records\Complaint;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

/**
 * @mixin Client
 *
 * @method Complaint|null getComplaints(string $id)
 * @method Complaint getComplaintsOrFail(string $id)
 * @method Collection|PushResponse[] insertComplaints(Collection|Complaint[]|Complaint $records)
 * @method Collection|PushResponse[] updateComplaints(Collection|Complaint[]|Complaint $records)
 * @method Collection|PushResponse[] upsertComplaints(Collection|Complaint[]|Complaint $records)
 * @method Collection|PushResponse[] deleteComplaints(string[]|Collection|Complaint[]|Complaint $records)
 * @method Collection|Complaint[] searchComplaints(iterable $filters = [], iterable $params = [])
 * @method Collection|Complaint[] searchComplaintsOrFail(iterable $filters = [], iterable $params = [])
 * @method Collection|Complaint[] listComplaints(iterable $params = [])
 * @method Collection|Complaint[] listComplaintsOrFail(iterable $params = [])
 */
readonly class Complaints extends Module
{
    public function listComplaintsByContact(string $contactId): LazyCollection
    {
        return $this->client->__listRequest("Contacts/$contactId/Complaints");
    }
}
