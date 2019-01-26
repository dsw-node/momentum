<?php
namespace Magecomp\Deliverydate\Model\Config\Source;

use \Magento\Framework\Locale\ListsInterface;
class Disabled implements \Magento\Framework\Option\ArrayInterface
{

    protected $localeLists;

    public function __construct(ListsInterface $localeLists )
    {
        $this->localeLists = $localeLists;
    }

    public function toOptionArray()
    {
        $options = $this->localeLists->getOptionWeekdays();
        array_unshift($options, [
            'label' => __('None of The Days'),
            'value' => -1
        ]);
        return $options;
    }
}