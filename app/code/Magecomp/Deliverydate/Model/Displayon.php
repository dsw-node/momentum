<?php
namespace Magecomp\Deliverydate\Model;

class Displayon 
{
    public function toOptionArray(){
        return [
			['value' => 0, 'label'=>__('All')],
            ['value' => 1, 'label'=>__('Order View (Frontend)')],
			['value' => 2, 'label'=>__('Order View (Backend)')],
			['value' => 3, 'label'=>__('Invoice View (Backend)')],
			['value' => 4, 'label'=>__('Shipment View (Backend)')],
			['value' => 5, 'label'=>__('Credit Memo View (Backend)')],
        ];
    }
}