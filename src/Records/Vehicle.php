<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Records;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * @property boolean $Advert
 * @property Carbon $Date_of_First_Registration
 * @property int $Previous_Owners
 * @property string $Fuel
 * @property float $Engine_Capacity
 * @property int $Mileage
 * @property string $Service_History_Type
 * @property int $Number_of_Services
 * @property string $Make
 * @property string $Model
 * @property Carbon $Created_Time
 * @property Collection $MOT_Data
 * @property string $Major_Works
 * @property bool $Clean_BCA_Assured_Report
 *
 * @property int $age_in_years
 * @property int $age_in_months
 * @property bool $warranty_available
 * @property int $service_interval
 * @property bool $self_serviced
 * @property History $latest_service
 */
class Vehicle extends Record
{
    protected array $keep = [
        'MOT_Data',
        'Inspection_Features',
    ];

    protected $casts = [
        'Date_of_First_Registration' => 'datetime',
        'Previous_Owners'            => 'integer',
        'Engine_Capacity'            => 'float',
        'Mileage'                    => 'integer',
        'Number_of_Services'         => 'integer',
        'Created_Time'               => 'datetime',
    ];

    public function __construct(iterable $attributes = [])
    {
        $attributes['MOT_Data'] = collect($attributes['MOT_Data'] ?? [])
            ->map(fn($history) => (array) $history)
            ->mapInto(History::class);

        parent::__construct($attributes);
    }

    public function getAgeInYearsAttribute(): ?int
    {
        if (empty($this->Date_of_First_Registration)) {
            return null;
        }

        return $this->Date_of_First_Registration->diff(today())->y;
    }

    public function getAgeInMonthsAttribute(): ?int
    {
        if (empty($this->Date_of_First_Registration)) {
            return null;
        }
        $diff = $this->Date_of_First_Registration->diff(today());

        return ($diff->y * 12) + $diff->m;
    }

    public function getWarrantyAvailableAttribute(): bool
    {
        return is_int($this->age_in_years)
            && ($this->age_in_years <= config('settings.vehicle.warranty.max_age_in_years'))
            && is_int($this->Mileage)
            && ($this->Mileage <= config('settings.vehicle.warranty.max_mileage'));
    }

    public function getServiceIntervalAttribute(): int
    {
        foreach (config('settings.vehicle.service_interval.engine_size') as $fuel => $engineSize) {
            if ($this->Fuel === $fuel) {
                return $this->Engine_Capacity >= $engineSize
                    ? config('settings.vehicle.service_interval.big')
                    : config('settings.vehicle.service_interval.small');
            }
        }

        return config('settings.vehicle.service_interval.default');
    }

    public function getLatestServiceAttribute(): ?History
    {
        return $this->MOT_Data
            ->filter(fn($history) => in_array($history->Result, ['Service', 'Maindealer Service']))
            ->first();
    }

    public function getSelfServicedAttribute(): bool
    {
        if (!$latestService = $this->latest_service) {
            return true;
        }


    }
}
