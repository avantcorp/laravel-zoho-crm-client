<?php

declare(strict_types=1);

namespace Avant\ZohoCRM;

use Avant\Zoho\Client as ZohoClient;
use Avant\ZohoCRM\Modules\Calls;
use Avant\ZohoCRM\Modules\Complaints;
use Avant\ZohoCRM\Modules\Contacts;
use Avant\ZohoCRM\Modules\Deals;
use Avant\ZohoCRM\Modules\Histories;
use Avant\ZohoCRM\Modules\Leads;
use Avant\ZohoCRM\Modules\Module;
use Avant\ZohoCRM\Modules\Notes;
use Avant\ZohoCRM\Modules\Products;
use Avant\ZohoCRM\Modules\Vendors;
use Avant\ZohoCrm\Records\Record;
use CURLFile;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

/**
 * @method Calls calls(),
 * @method Contacts contacts(),
 * @method Leads leads(),
 * @method Deals deals(),
 * @method Notes notes(),
 * @method Vendors vendors(),
 * @method Histories histories(),
 * @method Products products(),
 * @method Complaints complaints()
 */

class Client extends ZohoClient
{
    protected string $baseUrl = 'https://www.zohoapis.com/crm/v8';

    public function __call($name, $arguments)
    {
        $moduleClass = str(__NAMESPACE__."\\Modules\\")
            ->append(str($name)->ucfirst())
            ->toString();

        if (class_exists($moduleClass)) {
            return new $moduleClass($this, ucfirst($name));
        }

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
                $records->values()->each(function (Record $record, int $index) use ($results): void {
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
        return $this->__insertRecords("{$url}/upsert", $records);
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
                    ->delete("{$url}?ids={$ids->implode(',')}")
                    ->throw()
                    ->object();

                return collect($response->data)
                    ->map(fn ($pushResponse) => (array)$pushResponse)
                    ->mapInto(PushResponse::class);
            })
            ->collapse()
            ->keyBy(fn ($pushResponse) => $pushResponse->id);
    }

    public function __searchRecords(string $url, iterable $filters = [], iterable $params = []): LazyCollection
    {
        $params = collect($params)
            ->put(
                'criteria',
                collect($filters)
                    ->map(fn ($v, $k) => "({$k}:equals:{$v})")
                ->implode('and')
            );

        return $this->__listRequest("{$url}/search", $params);
    }

    public function __getRequest(string $url, iterable $params = [])
    {
        $params = collect($params);

        return $this
            ->request()
            ->get($url, Collection::wrap($params))
            ->throw()
            ->json();
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
                $params['page_token'] = $response->next_page_token;
            }
        });
    }

    /** @todo */
    public function __uploadFile(string $url, string $filepath): Response
    {
        $response = $this->request()
            ->asMultipart()
            ->post($url, [
                'file' => new CURLFile(
                    realpath($filepath),
                    mime_content_type($filepath),
                    pathinfo($filepath, PATHINFO_FILENAME)
                ),
            ]);

        return $response;
    }
}
