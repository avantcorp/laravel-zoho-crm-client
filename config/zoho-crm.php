<?php

declare(strict_types=1);

return [
    'base_url' => env('ZOHO_CRM_BASE_URL', 'https://www.zohoapis.com/crm'),
    'modules'  => [
        'accounts'       => \Avant\ZohoCRM\Modules\Module::class,
        'appointments'   => \Avant\ZohoCRM\Modules\Module::class,
        'calls'          => \Avant\ZohoCRM\Modules\Module::class,
        'campaigns'      => \Avant\ZohoCRM\Modules\Module::class,
        'cases'          => \Avant\ZohoCRM\Modules\Module::class,
        'contacts'       => \Avant\ZohoCRM\Modules\Module::class,
        'custom'         => \Avant\ZohoCRM\Modules\Module::class,
        'deals'          => \Avant\ZohoCRM\Modules\Module::class,
        'events'         => \Avant\ZohoCRM\Modules\Module::class,
        'invoices'       => \Avant\ZohoCRM\Modules\Module::class,
        'leads'          => \Avant\ZohoCRM\Modules\Module::class,
        'pricebooks'     => \Avant\ZohoCRM\Modules\Module::class,
        'products'       => \Avant\ZohoCRM\Modules\Module::class,
        'purchaseorders' => \Avant\ZohoCRM\Modules\Module::class,
        'quotes'         => \Avant\ZohoCRM\Modules\Module::class,
        'salesorders'    => \Avant\ZohoCRM\Modules\Module::class,
        'services'       => \Avant\ZohoCRM\Modules\Module::class,
        'solutions'      => \Avant\ZohoCRM\Modules\Module::class,
        'tasks'          => \Avant\ZohoCRM\Modules\Module::class,
        'vendors'        => \Avant\ZohoCRM\Modules\Module::class,
    ],
];
