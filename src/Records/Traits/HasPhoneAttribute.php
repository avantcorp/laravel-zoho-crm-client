<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Records\Traits;

/**
 * @property string $Phone
 */
trait HasPhoneAttribute
{
    public function setPhoneAttribute(?string $phone): void
    {
        $this->attributes['Phone'] = sanitise_phone($phone);
    }
}
