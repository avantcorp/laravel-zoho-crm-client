<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Records\Note;
use Avant\ZohoCRM\Records\Record;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use InvalidArgumentException;

readonly class Notes extends Module
{
    public function listNotesForRecord(Record $record): LazyCollection
    {
        if (get_class($record) === Note::class) {
            throw new InvalidArgumentException('Notes cannot be added to a note record');
        }

        $module = Str::plural(class_basename($record));

        return $this->client->__listRequest($module."/{$record->id}/".'Notes')
            ->mapInto($this->recordClass());
    }

    public function insertNotesForRecord(Record $record, $notes): Collection
    {
        if (get_class($record) === Note::class) {
            throw new InvalidArgumentException('Notes cannot be added to a note record');
        }

        $module = Str::plural(class_basename($record));
        collect(Arr::wrap($notes))
            ->each(function (Note $note) use ($record, $module): void {
                $note->fill([
                    'Parent_Id' => $record->id,
                    'se_module' => $module,
                ]);
            });

        return $this->client->__insertRecords($this->apiName, $notes);
    }
}
