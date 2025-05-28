<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

readonly class Module
{
    public function __construct(
        protected Client $client,
        protected string $apiName
    ) {}

    public function recordClass(): string
    {
        $recordClass = str(__NAMESPACE__)
            ->beforeLast('Modules')
            ->append('Records\\')
            ->append(str($this->apiName)->ucfirst()->singular())
            ->toString();

        return $recordClass;
    }

    public function get(string $id): mixed
    {
        $response = $this->client->__getRequest("{$this->apiName}/{$id}");
        if (!$response) {
            return null;
        }

        $recordClass = str($this->apiName);

        return new $recordClass($response);
    }

    public function search(iterable $filters = [], iterable $query = []): LazyCollection
    {
        return $this->client->__searchRecords($this->apiName, $filters, $query)
            ->mapInto($this->recordClass());
    }

    public function list(iterable $query = []): LazyCollection
    {
        return $this->client->__listRequest($this->apiName, $query)
            ->mapInto($this->recordClass());
    }

    public function insert($data): Collection
    {
        return $this->client->__insertRecords($this->apiName, $data);
    }

    public function update($data): Collection
    {
        return $this->client->__updateRecords($this->apiName, $data);
    }

    public function delete(string $id): Collection
    {
        return $this->client->__deleteRecords($this->apiName, $id);
    }

    public function uploadFile(string $id, string $filepath): Response
    {
        return $this->client->__uploadFile("{$this->apiName}/{$id}", $filepath);
    }
}
