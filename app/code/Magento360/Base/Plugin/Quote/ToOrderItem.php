<?php
namespace Vendor\ModuleName\Plugin;
use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
class ToOrderItem
{
    /**
     * aroundConvert
     *
     * @param QuoteToOrderItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $data
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        QuoteToOrderItem $subject,
        \Closure $proceed,
        $item,
        $data = []
    ) {
        try {
            $orderItem = $proceed($item, $data);
            $orderItem->setDeliverydate($item->getDeliverydate());
            return $orderItem;
        } catch (LocalizedException $e) {
            var_dump($e->getMessage());
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
