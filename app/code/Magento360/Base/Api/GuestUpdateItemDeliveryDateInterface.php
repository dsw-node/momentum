<?php
namespace Magento360\Base\Api;
interface GuestUpdateItemDeliveryDateInterface
{
    /**
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int $itemId
     * @param string $deliverydate
     * @return \Magento360\Base\Api\Data\UpdateItemDetailsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address, $itemId, $deliverydate);
}