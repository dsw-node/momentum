<?php
namespace Magecomp\Deliverydate\Model;

class Printin 
{
    public function toOptionArray(){
        return [
			['value' => 0, 'label'=>__('All')],
			['value' => 1, 'label'=>__('Print Invoice (Frontend)')],
			['value' => 2, 'label'=>__('Print Shipment (Frontend)')],
			['value' => 3, 'label'=>__('Print Refund (Frontend)')],
			['value' => 4, 'label'=>__('Print Invoice (Backend)')],
			['value' => 5, 'label'=>__('Print Shipment (Backend)')],
			['value' => 6, 'label'=>__('Print Refund (Backend)')],
        ];
    }
}