<?php
namespace Mageants\StorePickup\Model;
use Mageants\StorePickup\Api\GuestUpdatePickupInterface;
use Mageants\StorePickup\Api\UpdatePickupInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Framework\Escaper;
use Mageants\StorePickup\Api\Data\UpdateItemDetailsInterfaceFactory;

/**
 * Class GuestUpdateItemDeliveryDate
 *
 * @package Magento360\Base\Model
 */
class UpdatePickup implements UpdatePickupInterface
{
   
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var UpdateItemDetailsInterfaceFactory
     */
    private $updateItemDetails;

    /**
     * @var ShippingMethodManagementInterface
     */
    private $shippingMethodManagement;

    /**
     * @var PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * @var CartTotalRepositoryInterface
     */
    private $cartTotalRepository;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param UpdateItemDetailsInterfaceFactory $updateItemDetails
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param CartTotalRepositoryInterface $cartTotalRepository
     * @param Escaper $escaper
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        ShippingMethodManagementInterface $shippingMethodManagement,
        PaymentMethodManagementInterface $paymentMethodManagement,
        CartTotalRepositoryInterface $cartTotalRepository,
        UpdateItemDetailsInterfaceFactory $updateItemDetails,
        Escaper $escaper
    ) {
        $this->cartRepository = $cartRepository;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->escaper = $escaper;
        $this->updateItemDetails = $updateItemDetails;
    }

    /**
     * {@inheritdoc}
     */
    public function update($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address, $pickup, $pickup_date){
        $message = '';
        $status = false;
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        if (!$quote) {
            throw new NoSuchEntityException(__('This quote does not exist.'));
        }
        try {
            if(!empty($pickup_date)){
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $timezone =$objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
                if(!empty($pickup_date)){
                    $pickup_date = $this->converToTz(trim($pickup_date),$timezone->getDefaultTimezone(),$timezone->getConfigTimezone());
                    $quote->setPickupDate($pickup_date);
                }
                $quote->setPickupStore($pickup);
                $this->cartRepository->save($quote);
                //$quote->setPickupStoreVal($pickupStoreVal);
                $status = true;
                $message = __('shopping cart updated');
            }
        } catch (LocalizedException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new CouldNotSaveException(__('We can\'t shopping cart updated.'));
        }
        return $this->getUpdateCartDetails($quote, $address, $cartId, $message, $status);
    }
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address
     * @param int $quoteId
     * @param string $message
     * @param bool $status
     * @return \Bss\OneStepCheckout\Api\Data\UpdateItemDetailsInterface
     */
    private function getUpdateCartDetails($quote, $address, $quoteId, $message, $status)
    {
        $cartDetails = $this->updateItemDetails->create();
        $paymentMethods = $this->paymentMethodManagement->getList($quoteId);
        $totals = $this->cartTotalRepository->get($quoteId);
        $shippingAddress = $quote->getShippingAddress();
        if ($shippingAddress && $shippingAddress->getCustomerAddressId()) {
            $shippingMethods = $this->shippingMethodManagement->estimateByAddressId(
                $quoteId,
                $shippingAddress->getCustomerAddressId()
            );
        } else {
            $shippingMethods = $this->shippingMethodManagement->estimateByAddress($quoteId, $address);
        }
        $cartDetails->setShippingMethods($shippingMethods);
        $cartDetails->setPaymentMethods($paymentMethods);
        $cartDetails->setTotals($totals);
        $cartDetails->setMessage($message);
        $cartDetails->setStatus($status);
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            $cartDetails->setHasError(true);
        }
        return $cartDetails;
    }
    protected function converToTz($dateTime="", $toTz='', $fromTz=''){
        // timezone by php friendly values
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        $dateTime = $date->format('m/d/Y H:i:s');
        return $dateTime;
    }
}