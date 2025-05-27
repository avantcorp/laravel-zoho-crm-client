<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Records;

use Avant\ZohoCrm\Records\Traits\HasPhoneAttribute;

/**
 * @property string $Vendor_Name
 * @property string $Type
 * @property string $Street
 * @property string $Locality
 * @property string $City
 * @property string $Zip_Code
 */
class Vendor extends Record
{
    use HasPhoneAttribute;
}
