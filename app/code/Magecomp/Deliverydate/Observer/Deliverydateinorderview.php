<?php
namespace Magecomp\Deliverydate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magecomp\Deliverydate\Helper\Data as DeliveryHelper;

class Deliverydateinorderview implements ObserverInterface
{
    protected $objectManager;
    protected $deliveryhelper;

    public function __construct(
        DeliveryHelper $deliveryhelper,
        \Magento\Framework\ObjectManagerInterface $objectManager )
    {
        $this->deliveryhelper = $deliveryhelper;
        $this->objectManager = $objectManager;
    }

    public function execute( EventObserver $observer ){
        if ($observer->getElementName() == 'order_shipping_view') {

            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());

            $order = $orderShippingViewBlock->getOrder();

            if ($order->getDeliverydate() != '0000-00-00 00:00:00') {
                $formattedDate = $this->deliveryhelper->getFomatedDate($order->getDeliverydate());
            } else {

                $formattedDate = __('N/A');

            }


            $deliveryDateBlock = $this->objectManager->create('Magento\Framework\View\Element\Template');

            $deliveryDateBlock->setDeliverydate($formattedDate);

            $deliveryDateBlock->setDeliverycomment($order->getDeliverycomment());

            $deliveryDateBlock->setTemplate('Magecomp_Deliverydate::deliverydate.phtml');

            $html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();

            $observer->getTransport()->setOutput($html);

        }

    }

}