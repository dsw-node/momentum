<?php
namespace Magecomp\Deliverydate\Model;

class Sendin 
{
    public function toOptionArray(){
        return [
			['value' => 0, 'label'=>__('All')],
			['value' => 1, 'label'=>__('Order Email')],
			['value' => 2, 'label'=>__('Invoice Email')],
			['value' => 3, 'label'=>__('Shipment Email')],
			['value' => 4, 'label'=>__('Refund Email')],
        ];
    }
}