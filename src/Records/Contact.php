<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Records;

use Avant\ZohoCRM\Records\Traits\HasPhoneAttribute;

/**
 * @property string $Email
 * @property string $Full_Name
 * @property string $Mailing_Street
 * @property string $Mailing_Locality
 * @property string $Mailing_City
 * @property string $Mailing_Zip
 * @property string $Vehicle_Purchased
 */
class Contact extends Record
{
    use HasPhoneAttribute;
}
