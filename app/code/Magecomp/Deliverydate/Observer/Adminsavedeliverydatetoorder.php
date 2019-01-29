<?php
namespace Magecomp\Deliverydate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Adminsavedeliverydatetoorder implements ObserverInterface
{
    protected $_objectManager;
    protected $_request;

    public function __construct( \Magento\Framework\ObjectManagerInterface $objectmanager,\Magento\Framework\App\RequestInterface $request)
    {
        $this->_objectManager = $objectmanager;
        $this->_request = $request;
    }

    public function execute(EventObserver $observer )
    {
        $deliverydate1 = $this->_request->getPost('deliverydate1');

        $delivery_comment = $this->_request->getPost('delivery-comment');
        $order = $observer->getOrder();

        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $quoteRepository->get($order->getQuoteId());

        if(!empty($deliverydate1)) {
            $order->setDeliverydate($deliverydate1);
        }
        else{
            $order->setDeliverydate($quote->getDeliverydate());
        }
        $order->setDeliverycomment($delivery_comment);

        return $this;
    }
}