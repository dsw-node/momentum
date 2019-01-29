<?php
namespace Magento360\Base\Plugin\Quote;
use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
class ToOrderItem
{


    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        try {
            $orderItem = $proceed($item, $additional);
            $orderItem->setDeliverydate($item->getDeliverydate());
            return $orderItem;
        } catch (LocalizedException $e) {
            $this->log($e->getMessage());
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }
    }
    public function log($info) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/exception.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($info);
    }
}
