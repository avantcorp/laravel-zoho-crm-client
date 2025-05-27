<?php

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Illuminate\Support\LazyCollection;

readonly class Module
{
    public function __construct(
        protected Client $client,
        protected string $apiName
    ) {}

    public function get(string $id)
    {
        $response = $this->client->__getRequest("$this->apiName/$id");
        if (!$response) {
            return null;
        }

        $recordClass = str($this->apiName);

        return new $recordClass($response);
    }

    public function list(string $url, iterable $query = []): LazyCollection
    {
        return $this->client->__listRequest($url, $query);
    }

    public function insert($data)
    {
        return $this->client->__insertRecords($this->apiName, $data);
    }

    public function update(string $id)
    {
        return $this->client->__updateRecords($this->apiName, $id);
    }

    public function delete(string $id)
    {
        return $this->client->__deleteRecords($this->apiName, $id);
    }

    public function uploadImage(string $id, string $path)
    {
        return $this->client->__uploadImage($this->apiName, $id, $path);
    }
}