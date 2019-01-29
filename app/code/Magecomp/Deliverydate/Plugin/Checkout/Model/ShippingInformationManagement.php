<?php
namespace Magecomp\Deliverydate\Plugin\Checkout\Model;

use Magecomp\Deliverydate\Helper\Data as DeliverydateHelper;
class ShippingInformationManagement
{
    protected $quotemodel;
    protected $dataHelper;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quotemodel,
        DeliverydateHelper $dataHelper
    )
    {
        $this->dataHelper = $dataHelper;
        $this->quotemodel = $quotemodel;
    }

    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $DDate = $extAttributes->getDeliverydate();
        $isRquiredDatetimepicker = $this->dataHelper->isRequiredFieldDatetimepicker();
        if($isRquiredDatetimepicker) {
            if (!$DDate) {
                $message = "Please Select Delivery Date For Your Order.";
                throw new \Magento\Framework\Exception\LocalizedException(__($message));
                return;
            }
        }
        $DComment = $extAttributes->getDeliverycomment();
        $quote = $this->quotemodel->getActive($cartId);
        $quote->setDeliverydate($DDate);
        $quote->setDeliverycomment($DComment);
    }
}