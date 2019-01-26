<?php
namespace Magento360\Base\Model;
use Magento360\Base\Api\GuestUpdateItemDeliveryDateInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento360\Base\Api\UpdateItemDeliveryDateInterface;

/**
 * Class GuestUpdateItemDeliveryDate
 *
 * @package Magento360\Base\Model
 */
class GuestUpdateItemDeliveryDate implements GuestUpdateItemDeliveryDateInterface
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
        UpdateItemDeliveryDateInterface $UpdateItemDeliveryDateInterface
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->updateItemManagement = $UpdateItemDeliveryDateInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function update($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address, $itemId, $deliverydate){
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $quoteId = (int) $quoteIdMask->getQuoteId();
        return $this->updateItemManagement->update($quoteId, $address, $itemId, $deliverydate);
    }
}