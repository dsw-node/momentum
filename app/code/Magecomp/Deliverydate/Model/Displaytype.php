<?php
namespace Magecomp\Deliverydate\Model;

class Displaytype
{
    public function toOptionArray(){
        return [
			['value' => 0, 'label'=>__('Date')],
            ['value' => 1, 'label'=>__('Date & Time')],
        ];
    }
}