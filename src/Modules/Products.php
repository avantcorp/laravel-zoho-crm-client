<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Modules;

use Avant\ZohoCRM\Records\Product;
use Illuminate\Support\LazyCollection;

readonly class Products extends Module
{
    public function searchActive(iterable $filters = [], iterable $query = []): LazyCollection
    {
        $filters = collect($filters)
            ->put('Product_Active', 'true');

        return $this->search($filters, $query);
    }

    public function getByName(string $name): ?Product
    {
        return $this->search(['Product_Name' => $name])->first();
    }
}
