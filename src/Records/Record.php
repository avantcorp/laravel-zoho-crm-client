<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Records;

use Avant\ZohoCRM\OfflineModel;
use Illuminate\Support\Collection;

/**
 * @property string $id
 */
abstract class Record extends OfflineModel
{
    protected array $keep = [];

    public function __construct(iterable $attributes = [])
    {
        parent::__construct($this->sanitize($attributes));
    }

    private function sanitize(iterable $attributes = []): array
    {
        return collect($attributes)
            ->map(function ($v) {
                return (is_object($v) && property_exists($v, 'id')) ? $v->id : $v;
            })
            ->filter(function ($v, $k) {
                if (in_array($k, $this->keep)) {
                    return true;
                }
                if (in_array($k, ['Owner', 'Modified_By', 'Created_By', 'Record_Image'])) {
                    return false;
                }
                if (strpos($k, '$') === 0) {
                    return false;
                }
                if (!is_scalar($v)
                    && !is_null($v)
                    && !is_array($v)
                    && !$v instanceof Collection
                ) {
                    return false;
                }

                return true;
            })
            ->toArray();
    }
}
