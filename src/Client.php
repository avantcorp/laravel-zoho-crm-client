<?php

declare(strict_types=1);

namespace Avant\ZohoCRM;

use Avant\Zoho\Client as ZohoClient;
use Avant\ZohoCRM\Records\Record;
use CURLFile;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class Client extends ZohoClient
{
    protected function getBaseUrl(): string
    {
        return config('zoho-crm.base_url');
    }

    public function insert(string $url, Collection $records): Collection
    {
        return $records
            ->filter()
            ->chunk(200)
            ->map(function (Collection $records) use ($url) {
                $response = $this
                    ->request()
                    ->post($url, ['data' => $records->values()])
                    ->throw()
                    ->object();

                $results = collect($response->data);

                $results
                    ->filter(fn ($record) => $record->code !== 'SUCCESS')
                    ->whenNotEmpty(function (Collection $failed): void {
                        throw new Exception(sprintf(
                            'Failed to insert records %s',
                            $failed
                                ->map(fn ($result) => "[{$result?->details?->id}] {$result->message}")
                                ->implode(', ')
                        ));
                    });

                return $results->map(fn ($result) => $result->details->id);
            })
            ->collapse();
    }

    public function update(string $url, Collection $records): Collection
    {
        $records
            ->keyBy('id')
            ->map(fn (Record $record) => $record->syncChanges()->getChanges())
            ->filter()
            ->chunk(200)
            ->map(function (Collection $records) use ($url): void {
                $records->transform(fn ($changes, $id) => compact('id') + $changes);

                $this
                    ->request()
                    ->put($url, ['data' => $records->values()])
                    ->throw()
                    ->object();
            });
    }

    public function upsert(string $url, Collection $records): Collection
    {
        return $this->insert("{$url}/upsert", $records);
    }

    public function delete(string $url, Collection $records): void
    {
        $records
            ->pluck('id')
            ->unique()
            ->chunk(10)
            ->map(function (Collection $ids) use ($url): void {
                $this
                    ->request()
                    ->delete("{$url}?ids={$ids->implode(',')}")
                    ->throw()
                    ->object();
            });
    }

    public function search(string $url, iterable $criteria = [], iterable $query = []): LazyCollection
    {
        $query = collect($query)
            ->put(
                'criteria',
                str(
                    collect($criteria)
                        ->map(function ($v, $k) {
                            $v = is_bool($v) ? ($v ? 'true' : 'false') : $v;
                            return "({$k}:equals:{$v})";
                        })
                        ->implode('and')
                )->toString(),
            );

        return $this->listRequest("{$url}/search", $query);
    }

    public function getRequest(string $url, iterable $query = []): array
    {
        return $this
            ->request()
            ->get($url, Collection::wrap($query)->toArray())
            ->throw()
            ->json() ?: [];
    }

    public function listRequest(string $url, iterable $query = []): LazyCollection
    {
        return LazyCollection::make(function () use ($url, $query) {
            $hasMorePage = true;
            while ($hasMorePage) {
                $response = $this->getRequest($url, $query);

                foreach (data_get($response, 'data') ?: [] as $record) {
                    yield $record;
                }
                if ($hasMorePage = data_get($response, 'info.more_records', false)) {
                    if ($nextPageToken = data_get($response, 'info.next_page_token')) {
                        $query['page_token'] = $nextPageToken;
                    } else {
                        $query['page'] = data_get($query, 'page', 1) + 1;
                    }
                }
            }
        });
    }

    public function uploadRequest(string $url, string $filepath): Response
    {
        return $this->request()
            ->asMultipart()
            ->post($url, [
                'file' => new CURLFile(
                    realpath($filepath),
                    mime_content_type($filepath),
                    pathinfo($filepath, PATHINFO_FILENAME),
                ),
            ])
            ->throw();
    }
}
