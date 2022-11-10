<?php

declare(strict_types=1);

namespace MyParcelNL\PrestaShop\Adapter;

use MyParcelNL\Pdk\Shipment\Model\DeliveryOptions;

class DeliveryOptionsFromOrderGridAdapter extends DeliveryOptions
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->shipmentOptions = new ShipmentOptionsFromOrderGridAdapter($data['shipmentOptions']);
    }
}
