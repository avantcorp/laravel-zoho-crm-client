<?php

declare(strict_types=1);

namespace Avant\ZohoCRM;

use Avant\ZohoCRM\Modules\Module;
use Exception;

/**
 * @method Module accounts()
 * @method Module appointments()
 * @method Module calls()
 * @method Module campaigns()
 * @method Module cases()
 * @method Module contacts()
 * @method Module custom()
 * @method Module deals()
 * @method Module events()
 * @method Module invoices()
 * @method Module leads()
 * @method Module pricebooks()
 * @method Module products()
 * @method Module purchaseorders()
 * @method Module quotes()
 * @method Module salesorders()
 * @method Module services()
 * @method Module solutions()
 * @method Module tasks()
 * @method Module vendors()
 */
readonly class CRM
{
    public function __construct(private array $modules) {}

    public function __call(string $name, array $arguments)
    {
        throw_unless(
            array_key_exists($name, $this->modules),
            new Exception("Module {$name} not found")
        );

        return resolve($this->modules[$name], [
            'apiName' => ucfirst($name),
        ]);
    }
}
