<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Client;
use Avant\ZohoCRM\PushResponse;
use Avant\ZohoCRM\Records\Note;
use Avant\ZohoCRM\Records\Record;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @mixin Client
 *
 * @method Note|null getNotes(string $id)
 * @method Note getNotesOrFail(string $id)
 * @method Collection|PushResponse[] insertNotes(Collection|Note[]|Note $records)
 * @method Collection|PushResponse[] updateNotes(Collection|Note[]|Note $records)
 * @method Collection|PushResponse[] upsertNotes(Collection|Note[]|Note $records)
 * @method Collection|PushResponse[] deleteNotes(string[]|Collection|Note[]|Note $records)
 * @method Collection|Note[] searchNotes(iterable $filters = [], iterable $params = [])
 * @method Collection|Note[] searchNotesOrFail(iterable $filters = [], iterable $params = [])
 * @method Collection|Note[] listNotes(iterable $params = [])
 * @method Collection|Note[] listNotesOrFail(iterable $params = [])
 */
readonly class Notes extends Module
{
    public function listNotesForRecord(Record $record): Collection
    {
        if (get_class($record) === Note::class) {
            throw new \InvalidArgumentException('Notes cannot be added to a note record');
        }

        $module = Str::plural(class_basename($record));

        return $this->client->__listRequest($module."/{$record->id}/".'Notes')
            ->mapInto('Notes');
    }

    public function insertNotesForRecord(Record $record, $notes)
    {
        if (get_class($record) === Note::class) {
            throw new \InvalidArgumentException('Notes cannot be added to a note record');
        }

        $module = Str::plural(class_basename($record));
        collect(Arr::wrap($notes))
            ->each(function (Note $note) use ($record, $module) {
                $note->fill([
                    'Parent_Id' => $record->id,
                    'se_module' => $module,
                ]);
            });

        return $this->insertNotes($notes);
    }
}
