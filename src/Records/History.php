<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Records;

use Carbon\Carbon;

/**
 * @property Carbon $Test_Date
 * @property string $Result
 * @property int $Odometer
 * @property int $age_in_months
 */
class History extends Record
{
    protected $casts = [
        'Test_Date' => 'datetime',
        'Result'    => 'string',
        'Odometer'  => 'integer',
    ];

    public function getAgeInMonthsAttribute(): ?int
    {
        if (empty($this->Test_Date)) {
            return null;
        }
        $diff = $this->Test_Date->diff(today());

        return ($diff->y * 12) + $diff->m;
    }
}
