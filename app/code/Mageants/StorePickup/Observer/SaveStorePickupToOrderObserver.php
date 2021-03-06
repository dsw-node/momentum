<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\StorePickup\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveStorePickupToOrderObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * Store Collection
     *
     * @var \Mageants\StoreLocator\Model\ManageStore
     */
    protected $_storeCollection;

    protected $_storeManager;
    protected $_request;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager,
                                \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformation,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Mageants\StoreLocator\Model\ManageStore $storeCollection,
                                \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_storeCollection = $storeCollection;
        $this->_objectManager = $objectmanager;
        $this->countryInformation = $countryInformation;
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;

    }

    public function execute(EventObserver $observer)
    {

        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        $quote = $quoteRepository->get($order->getQuoteId());
        if($quote->getPickupDate()!='0000-00-00 00:00:00' && $quote->getPickupStore()!="") {
            $order->setPickupDate($quote->getPickupDate());
            $order->setPickupStore($quote->getPickupStore());
            $this->log("Pickup Date".$quote->getPickupDate());
            $this->log("Pickup Store".$quote->getPickupStore());
            $collection = '';
            $store = '';
            if (@$_COOKIE['pickupStoreVal']){
                $id = @$_COOKIE['pickupStoreVal'];
                $collection = $this->_storeCollection->getCollection()->addFieldToFilter('store_id', $id);
            }else{
                $id = $quote->getPickupStore();
                $collection = $this->_storeCollection->getCollection()->addFieldToFilter('store_id', $id);
            }
            if($collection){
                foreach ($collection->getData() as $stores){
                    $store = $stores;
                }
                if ($order->getShippingMethod() == "storepickup_storepickup"){
                    $storeName = explode(' ', $store['sname']);
                    $streetAddress = array($store['address']);
                    $firstName = '';
                    $lastName = '';

                    if (count($storeName) == 1)
                    {
                        $firstName = "Store";
                        $lastName = $storeName[0];
                    }
                    elseif (count($storeName) > 2)
                    {
                        $firstName = $storeName[0];
                        for ($i=1; $i < count($storeName); $i++)
                        {
                            $lastName = $lastName." ".$storeName[$i];
                        }
                    }
                    else{
                        $firstName = $storeName[0];
                        $lastName = $storeName[1];
                    }
                    $order->getShippingAddress()->setFirstname($firstName);
                    $order->getShippingAddress()->setLastname($lastName);
                    $order->getShippingAddress()->setStreet($streetAddress);
                    $order->getShippingAddress()->setCity($store['city']);
                    $order->getShippingAddress()->setPostcode(trim($store['postcode']));
                    $order->getShippingAddress()->setRegion(trim($store['region']));
                    $order->getShippingAddress()->setCountryId(strtoupper(trim($store['country'])));
                    $order->getShippingAddress()->setTelephone(trim($store['phone']));
                    $order->getShippingAddress()->setCompany('');
                }
            }
        }
        return $this;
    }
    public function getStoreId(){
        return $this->_storeManager->getStore()->getId();
    }
    public function log($info) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_order.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($info);
    }

}
 