<?php
namespace Mageants\StorePickup\Model;
use Mageants\StorePickup\Api\GuestUpdatePickupInterface;
use Mageants\StorePickup\Api\UpdatePickupInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
class GuestUpdatePickup implements GuestUpdatePickupInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var \Magento360\Base\Api\UpdateItemDeliveryDateInterface
     */
    private $updateItemManagement;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param UpdateItemManagementInterface $updateItemManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        UpdatePickupInterface $UpdateItemDeliveryDateInterface
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->updateItemManagement = $UpdateItemDeliveryDateInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function update($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address, $pickup, $pickup_date){
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $quoteId = (int) $quoteIdMask->getQuoteId();
        return $this->updateItemManagement->update($quoteId, $address, $pickup, $pickup_date);
    }
}