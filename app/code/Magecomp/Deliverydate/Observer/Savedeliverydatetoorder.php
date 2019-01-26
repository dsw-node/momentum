<?php
namespace Magecomp\Deliverydate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Savedeliverydatetoorder implements ObserverInterface
{
    protected $_objectManager;

    public function __construct( \Magento\Framework\ObjectManagerInterface $objectmanager )
    {
        $this->_objectManager = $objectmanager;
    }

    public function execute( EventObserver $observer )
    {
        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $quoteRepository->get($order->getQuoteId());
        $order->setDeliverydate($quote->getDeliverydate());
        $order->setDeliverycomment($quote->getDeliverycomment());

        return $this;
    }
}