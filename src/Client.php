<?php

declare(strict_types=1);

namespace Avant\ZohoCRM;

use Avant\Zoho\Client as ZohoClient;
use Avant\ZohoCRM\Modules\Module;
use Avant\ZohoCrm\Records\Record;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

/**
 * @method Module calls(),
 * @method Module vehicles(),
 * @method Module contacts(),
 * @method Module leads(),
 * @method Module deals(),
 * @method Module notes(),
 * @method Module vendors(),
 * @method Module histories(),
 * @method Module products(),
 * @method Module complaints()
 */

class Client extends ZohoClient
{
    private const RESOURCE_MAP = [];
    protected string $baseUrl = 'https://www.zohoapis.com/crm/v8';

    public function __call($name, $arguments)
    {
        return new Module($this, ucfirst($name));
    }

    public function __insertRecords(string $url, $records): Collection
    {
        return collect(Arr::wrap($records))
            ->filter()
            ->chunk(200)
            ->map(function (Collection $records) use ($url) {
                $response = $this
                    ->request()
                    ->post($url, ['data' => $records->values()])
                    ->throw()
                    ->object();

                $results = collect($response->data)
                    ->map(fn ($pushResponse) => (array)$pushResponse)
                    ->mapInto(PushResponse::class);
                $records->values()->each(function (Record $record, int $index) use ($results) {
                    $record->id = $results->get($index)->id;
                    $record->syncOriginalAttribute('id');
                });

                return $results;
            })
            ->collapse()
            ->keyBy(fn ($pushResponse) => $pushResponse->id);
    }

    public function __updateRecords(string $url, $records): Collection
    {
        return collect(Arr::wrap($records))
            ->keyBy('id')
            ->map(fn (Record $record) => $record->syncChanges()->getChanges())
            ->filter()
            ->chunk(200)
            ->map(function (Collection $records) use ($url) {
                $records->transform(fn ($changes, $id) => compact('id') + $changes);
                $response = $this
                    ->request()
                    ->put($url, ['data' => $records->values()])
                    ->throw()
                    ->object();

                return collect($response->data)
                    ->map(fn ($pushResponse) => (array)$pushResponse)
                    ->mapInto(PushResponse::class);
            })
            ->collapse()
            ->keyBy(fn ($pushResponse) => $pushResponse->id);
    }

    public function __upsertRecords(string $url, $records): Collection
    {
        return $this->__insertRecords("$url/upsert", $records);
    }

    public function __deleteRecords(string $url, $records): Collection
    {
        $records = collect(Arr::wrap($records));

        return $records
            ->when(
                $records->first() instanceof Record,
                fn (Collection $records) => $records->pluck('id')
            )
            ->unique()
            ->chunk(10)
            ->map(function (Collection $ids) use ($url) {
                $response = $this
                    ->request()
                    ->delete("$url?ids={$ids->implode(',')}")
                    ->throw()
                    ->object();

                return collect($response->data)
                    ->map(fn ($pushResponse) => (array)$pushResponse)
                    ->mapInto(PushResponse::class);
            })
            ->collapse()
            ->keyBy(fn ($pushResponse) => $pushResponse->id);
    }

    public function __searchRecords(string $url, iterable $filters = [], iterable $params = []): Collection
    {
        $params = collect($params)
            ->put('criteria', collect($filters)
                ->map(fn($v, $k) => "($k:equals:$v)")
                ->implode('and')
            );

        return $this->__getRequest("$url/search", $params);
    }

    public function __getRequest(string $url, iterable $params = []): ?object
    {
        $params = collect($params);

        return $this
            ->request()
            ->get($url, Collection::wrap($params))
            ->throw()
            ->object();
    }

    public function __listRequest(string $url, iterable $params = []): LazyCollection
    {
        return LazyCollection::make(function () use ($url, $params) {
            $hasMorePage = true;
            while ($hasMorePage) {
                $response = $this->__getRequest($url, $params);

                foreach (data_get($response, 'data') as $record) {
                    yield $record;
                }

                $hasMorePage = $response->info->more_records ?? false;
                $params['page'] = $response->page_context->page + 1;
            }
        });
    }

    /** @todo */
    public function __uploadFile(string $url, string $filepath)
    {
        $response = $this->request()
            ->asMultipart()
            ->post($url, [
                'file' => new \CURLFile(
                    realpath($filepath),
                    mime_content_type($filepath),
                    pathinfo($filepath, PATHINFO_FILENAME)
                ),
            ]);
    }
}
