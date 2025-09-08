<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Avant\ZohoCRM\Records\Note;
use Avant\ZohoCRM\Records\Record;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

/** @template T of Record */
readonly class Module
{
    public string $recordClass;

    public function __construct(
        private Client $client,
        private string $apiName,
        ?string $recordClass = null,
    ) {
        $this->recordClass = $recordClass ?: $this->recordClass();

        throw_unless(
            is_a($this->recordClass, Record::class, true),
            new Exception('Record class must be a subclass of '.Record::class)
        );
    }

    /** @return class-string<T> */
    protected function recordClass(): string
    {
        $recordClass = str(__NAMESPACE__)
            ->beforeLast('Modules')
            ->append('Records\\')
            ->append(str($this->apiName)->ucfirst()->singular())
            ->toString();

        return class_exists($recordClass) ? $recordClass : Record::class;
    }

    /** @return ?T */
    public function get(string $id): ?Record
    {
        return collect(data_get($this->client->getRequest("{$this->apiName}/{$id}"), 'data'))
            ->take(1)
            ->map(fn (array $attributes): Record => $this->recordClass::make($attributes))
            ->first();
    }

    /** @return T */
    public function getOrFail(string $id): Record
    {
        throw_unless($record = $this->get($id), new Exception("{$this->apiName}:{$id} not found"));

        return $record;
    }

    /** @return LazyCollection<string, T> */
    public function list(iterable $criteria = [], iterable $query = []): LazyCollection
    {
        $records = $criteria
            ? $this->client->search($this->apiName, $criteria, $query)
            : $this->client->listRequest($this->apiName, $query);

        return $records
            ->keyBy('id')
            ->map(fn (array $attributes): Record => $this->recordClass::make($attributes));
    }

    /** @return ?T */
    public function find(iterable $criteria, iterable $query = []): ?Record
    {
        return $this->list($criteria, $query)->sole();
    }

    /** @return T */
    public function findOrFail(iterable $criteria, iterable $query = []): Record
    {
        throw_unless(
            $record = $this->find($criteria, $query),
            new Exception("Matching {$this->apiName} not found")
        );

        return $record;
    }

    /**
     * @param Collection<string, T> $records
     *
     * @return Collection<string>
     */
    public function insertMany(Collection $records): Collection
    {
        return $this->client
            ->insert($this->apiName, $records);
    }

    public function insert(Record $record): string
    {
        /** @var string */
        return $this->insertMany(collect([$record]))->first();
    }

    /**
     * @param Collection<string, T> $records
     *
     * @return Collection<string>
     */
    public function upsertMany(Collection $records): Collection
    {
        return $this->client
            ->upsert($this->apiName, $records);
    }

    public function upsert(Record $record): string
    {
        /** @var string */
        return $this->upsertMany(collect([$record]))->first();
    }

    public function updateMany(Collection $records): void
    {
        $this->client
            ->update($this->apiName, $records);
    }

    public function update(Record $record): void
    {
        $this->updateMany(collect([$record]));
    }

    public function deleteMany(Collection $records): void
    {
        $this->client
            ->delete($this->apiName, $records);
    }

    public function delete(Record $record): void
    {
        $this->deleteMany(collect([$record]));
    }

    public function uploadAttachment(string $id, string $filePath, ?string $fileName = null): void
    {
        $this->client->upload("{$this->apiName}/{$id}/Attachments", $filePath, $fileName);
    }

    public function uploadPhoto(string $id, string $filePath, ?string $fileName = null): void
    {
        $this->client->upload("{$this->apiName}/{$id}/photo", $filePath, $fileName);
    }

    public function insertNote(Record|string $record, Note $note): string
    {
        /** @var string */
        return $this->insertManyNotes($record, collect([$note]))->first();
    }

    /**
     * @param Collection<Note> $notes
     *
     * @return Collection<string>
     */
    public function insertManyNotes(Record|string $record, Collection $notes): Collection
    {
        $recordId = is_a($record, Record::class, true) ? $record->id : $record;

        return $this->client->insert("{$this->apiName}/{$recordId}/Notes", $notes);
    }
}
