<?php
namespace Magecomp\Deliverydate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Deliverydatetoshipping implements ObserverInterface
{
    protected $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function execute(EventObserver $observer)
    {
        if($observer->getElementName() == 'sales.order.info') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();

            if($order->getDeliverydate() != '0000-00-00 00:00:00') {
                $objMan = \Magento\Framework\App\ObjectManager::getInstance();
                $helper = $objMan->get('Magecomp\Deliverydate\Helper\Data');
                $formattedDate = $helper->getFomatedDate($order->getDeliverydate());
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