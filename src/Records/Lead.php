<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Records;

use Avant\ZohoCRM\Records\Traits\HasPhoneAttribute;

/**
 * @property string $Email
 * @property string $Lead_Source
 * @property string $Lead_Status
 * @property string $Parent_Customer
 * @property string $Vehicle_of_Interest
 */
class Lead extends Record
{
    use HasPhoneAttribute;
}
