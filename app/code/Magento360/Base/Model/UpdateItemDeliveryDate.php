<?php
namespace Magento360\Base\Model;
use Magento360\Base\Api\UpdateItemDeliveryDateInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Framework\Escaper;

/**
 * Class UpdateItemDeliveryDateInterface
 *
 * @package Magento360\Base\Model
 */
class UpdateItemDeliveryDate implements UpdateItemDeliveryDateInterface
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
        Escaper $escaper
    ) {
        $this->cartRepository = $cartRepository;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->escaper = $escaper;
    }

    /**
     * {@inheritdoc}
     */
    public function update($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address, $itemId, $deliverydate){
        $message = '';
        $status = false;
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        if (!$quote) {
            throw new NoSuchEntityException(__('This quote does not exist.'));
        }
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(__('We can\'t find the quote item.'));
        }
        try {
            if(!empty($deliverydate)){
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $timezone =$objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
                $deliverydate = $this->converToTz(trim($deliverydate),$timezone->getDefaultTimezone(),$timezone->getConfigTimezone());
                $quoteItem->setDeliverydate($deliverydate);
                if ($quoteItem->getHasError()) {
                    throw new CouldNotSaveException(__($quoteItem->getMessage()));
                } else {
                    $quoteItem->save();
                    $status = true;
                    $message = __(
                        '%1 was updated in your shopping cart.',
                        $this->escaper->escapeHtml($quoteItem->getProduct()->getName())
                    );
                }
            }
            $this->cartRepository->save($quote);
        } catch (LocalizedException $e) {
            $message = $e->getMessage();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('We can\'t update the item right now.'));
        }
    }
    protected function converToTz($dateTime="", $toTz='', $fromTz=''){
        // timezone by php friendly values
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        $dateTime = $date->format('m/d/Y H:i:s');
        return $dateTime;
    }
}