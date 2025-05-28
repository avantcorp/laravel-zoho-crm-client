<?php

declare(strict_types=1);

namespace Avant\ZohoCRM;

use Exception;

/**
 * @property string $id
 * @property string $code
 * @property string $message
 * @property object $details
 */
class PushResponse extends OfflineModel
{
    public function throw(): void
    {
        throw_unless(
            $this->code === 'SUCCESS',
            new Exception("[{$this->id}] {$this->message}")
        );
    }

    public function getIdAttribute(): string
    {
        return $this->details->id;
    }
}
