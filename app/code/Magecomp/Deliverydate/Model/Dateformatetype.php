<?php
namespace Magecomp\Deliverydate\Model;

class Dateformatetype
{
    public function toOptionArray()
    {
        return [
            ['value' => 'yy-mm-dd', 'label' => __('yy-mm-dd')],
            ['value' => 'dd-mm-yy', 'label' => __('dd-mm-yy')],
            ['value' => 'mm-dd-yy', 'label' => __('mm-dd-yy')],
            ['value' => 'yy/mm/dd', 'label' => __('yy/mm/dd')],
            ['value' => 'dd/mm/yy', 'label' => __('dd/mm/yy')],
            ['value' => 'mm/dd/yy', 'label' => __('mm/dd/yy')],
            ['value' => 'yy.mm.dd', 'label' => __('yy.mm.dd')],
            ['value' => 'dd.mm.yy', 'label' => __('dd.mm.yy')],
            ['value' => 'mm.dd.yy', 'label' => __('mm.dd.yy')],
            ['value' => 'yy mm dd', 'label' => __('yy mm dd')],
            ['value' => 'dd mm yy', 'label' => __('dd mm yy')],
            ['value' => 'mm dd yy', 'label' => __('mm dd yy')],

        ];
    }
}