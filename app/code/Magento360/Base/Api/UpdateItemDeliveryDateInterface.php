<?php
namespace Magento360\Base\Api;
interface UpdateItemDeliveryDateInterface
{
    /**
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int $itemId
     * @param date $date
     * @return \Bss\OneStepCheckout\Api\Data\UpdateItemDetailsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address, $itemId, $date);
}