<?php

namespace Magento360\Invoice\Observer;

use Magento\Framework\Event\ObserverInterface;

class ShipmentExtraDataToTransport implements ObserverInterface
{
    private $orderRepository;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->orderRepository = $orderRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();
        if (is_object($transport)) {
            $order = $this->orderRepository->get($transport->getOrder()->getId());
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $sname = $objectManager->create('Mageants\StoreLocator\Model\ManageStore')->getCollection()->addFieldToFilter('store_id', $order->getPickupStore())->getFirstItem();;
            $pickup_date = date('m-d-Y', strtotime($order->getPickupDate()));
            if (!empty($pickup_date) && $pickup_date != '') {
                $transport['pickup_date'] = $pickup_date;
            }
            if (!empty($sname)) {
                $transport['pickup_store'] = $sname->getSname();
            }
        }
    }
}