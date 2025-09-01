<?php

declare(strict_types=1);

use Avant\ZohoCRM\CRM;
use Avant\ZohoCRM\Records\Record;
use Illuminate\Support\LazyCollection;

beforeEach(function (): void {
    $this->crm = resolve(CRM::class);
    $this->productId = '5692029000128607004';
});

test('can get a record', function (): void {
    $product = $this->crm
        ->products()
        ->getOrFail($this->productId);

    expect($product)
        ->toBeInstanceOf(Record::class);
});

test('can find a record', function (): void {
    $product = $this->crm
        ->products()
        ->findOrFail([
            'Product_Name' => 'KM19FLK',
        ]);

    expect($product)
        ->toBeInstanceOf(Record::class);
});

test('throws exception on getOrFail', function (): void {
    $this->crm
        ->products()
        ->getOrFail('5692029000000000000');
})->throws(Exception::class);

test('throws exception on findOrFail', function (): void {
    $this->crm
        ->products()
        ->findOrFail([
            'Product_Active'   => true,
            'Product_Category' => 'Services',
            'Product_Type'     => 'Vehicle',
        ]);
})->throws(Exception::class);

test('can list records', function (): void {
    $products = $this->crm
        ->products()
        ->list()
        ->take(2);

    expect($products)
        ->toBeInstanceOf(LazyCollection::class)
        ->and($products->collect())
        ->not->toBeEmpty()
        ->toContainOnlyInstancesOf(Record::class);
});

test('can search records', function (): void {
    $products = $this->crm
        ->products()
        ->list([
            'Product_Active'   => true,
            'Product_Category' => 'Goods',
            'Product_Type'     => 'Vehicle',
        ])
        ->take(2);

    expect($products)
        ->toBeInstanceOf(LazyCollection::class)
        ->and($products->collect())
        ->not->toBeEmpty()
        ->toContainOnlyInstancesOf(Record::class);
});

test('can filter changes', function (): void {
    $product = $this->crm
        ->products()
        ->get($this->productId);

    $dirty = $product
        ->fill([
            'Product_Name'     => 'Changed',
            'Product_Category' => $product->Product_Category,
        ])
        ->getDirty();

    expect($dirty)
        ->toHaveKey('Product_Name')
        ->toHaveCount(1);
});

test('can create and delete a record', function (): void {
    $leadId = $this->crm
        ->leads()
        ->upsert(new Record([
            'First_Name' => 'Ali',
            'Last_Name'  => 'Saleem',
            'Email'      => 'alisaleem@outlook.com',
        ]));

    expect($leadId)->toBeString();

    $this->crm->leads()
        ->delete(Record::make(['id' => $leadId]));

    expect($this->crm->leads()->get($leadId))
        ->toBeNull();
});
