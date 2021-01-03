<?php

namespace Avant\ZohoClient\Crm;

use Avant\ZohoClient\ZohoClient;
use Illuminate\Support\Collection;

class ZohoCRMClient extends ZohoClient
{
    protected $baseUrl = 'https://www.zohoapis.com/crm/v2';

    protected function getRecords(string $url, iterable $params = []): Collection
    {
        $params = collect($params);
        $response = $this->request()
            ->get($url, $params->toArray());
        if($response->status() >= 400){
            throw new \Exception($response->body());
        }

        $response = $response->object();

        if (empty($response->data) || !is_array($response->data)) {
            return collect();
        }

        $records = collect($response->data)
            ->keyBy('id');

        if ($response->info->more_records ?? false) {
            $nextPage = $response->info->page + 1;
            $records = $records->combine($this->getRecords($url, $params->put('page', $nextPage)));
        }

        return $records;
    }

    protected function searchRecords(string $url, iterable $filters = [], iterable $params = []): Collection
    {
        $params = collect($params)
            ->put('criteria', collect($filters)
                ->map(fn($v, $k) => "($k:equals:$v)")
                ->implode('and')
            );

        return $this->getRecords($url . '/search', $params);
    }

    protected function searchProducts(iterable $filters = [], iterable $params = [])
    {
        $filters = collect($filters)
            ->put('Product_Category', 'Goods');

        return $this->searchRecords('Products', $filters, $params);
    }

    public function getProducts()
    {
        return $this->searchProducts();
    }

    public function getActiveProducts()
    {
        return $this->searchProducts([
            'Product_Active' => 'true',
            'Website_Advert' => 'true',
        ]);
    }
}
