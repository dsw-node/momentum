<?php
namespace Magecomp\Deliverydate\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;

class Data extends AbstractHelper
{
    const CONFIG_DELIVERYDATE_IS_DISPLAYON = 'deliverydate/setting/displayon';
    const DELIVERYDATE_IS_ENABLEDISABLE = 'deliverydate/general/enable';
    const DELIVERYDATE_IS_PRINTDELIVERYDATE = 'deliverydate/setting/printin';
    const DELIVERYDATE_SENDDELEVERYDATEIN = 'deliverydate/setting/sendin';
    const DELIVERYDATE_DATEFORMATETYPE = 'deliverydate/general/dateformatetype';
    const DELIVERYDATE_DISPLAYDATE_TYPE = 'deliverydate/general/datetype';
    const DELIVERYDATE_IS_REQUIRED_CHECKOUT = 'deliverydate/general/isrequiredcheckout';

    protected $_storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager,
        Context $context )
    {
        $this->_storeManager = $storeManager;
        $this->objectManager = $objectManager;
        parent::__construct($context);
    }

    public function DisplayDeliveryDateOn( $id, $StoreId )
    {
        if ($this->isEnabled($StoreId)) {
            $Displayon = explode(',', $this->scopeConfig->getValue(self::CONFIG_DELIVERYDATE_IS_DISPLAYON, ScopeInterface::SCOPE_STORE, $StoreId));
            if (in_array($id, $Displayon) || in_array(0, $Displayon)) {
                return true;
            }
        } else {
            return false;
        }
    }

    public function isEnabled( $StoreId )
    {
        return (bool)$this->scopeConfig->getValue(self::DELIVERYDATE_IS_ENABLEDISABLE, ScopeInterface::SCOPE_STORE, $StoreId);
    }

    public function PrintDeliveryDateIn( $id, $StoreId )
    {
        if ($this->isEnabled($StoreId)) {
            $Printin = explode(',', $this->scopeConfig->getValue(self::DELIVERYDATE_IS_PRINTDELIVERYDATE, ScopeInterface::SCOPE_STORE, $StoreId));
            if (in_array($id, $Printin) || in_array(0, $Printin)) {
                return true;
            }
        } else {
            return false;
        }
    }

    public function SendDeliveryDateIn( $id, $StoreId )
    {
        if ($this->isEnabled($StoreId)) {
            $Sendin = explode(',', $this->scopeConfig->getValue(self::DELIVERYDATE_SENDDELEVERYDATEIN, ScopeInterface::SCOPE_STORE, $StoreId));
            if (in_array($id, $Sendin) || in_array(0, $Sendin)) {
                return true;
            }
        } else {
            return false;
        }
    }

    public function getDisplayDateByConfig( $datetime = null )
    {
        $timestamp = strtotime($datetime);
        $dateformate = $this->getDateformatetype();
        $find = array("yy", "mm", "dd");
        $replace = array("Y", "m", "d");
        $datefmt = str_replace($find, $replace, $dateformate);

        if ($this->getDisplaytype() == 1) {
            $dateformated = date($datefmt . ' H:i:s', $timestamp);
        } else {
            $dateformated = date($datefmt, $timestamp);
        }
        return $dateformated;
    }

    public function getDateformatetype()
    {
        $storeId = $this->getStoreId();
        return $this->scopeConfig->getValue(self::DELIVERYDATE_DATEFORMATETYPE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getDisplaytype()
    {
        $storeId = $this->getStoreId();
        return $this->scopeConfig->getValue(self::DELIVERYDATE_DISPLAYDATE_TYPE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getFomatedDate( $datetime )
    {
        if ($this->getDisplaytype() == 1) {
            $formatedDate = date("M j Y H:i:s", strtotime($datetime));
        } else {
            $formatedDate = date("M j Y", strtotime($datetime));
        }
        return $formatedDate;
    }

    public function isRequiredFieldDatetimepicker()
    {
        $storeId = $this->getStoreId();
        if ($this->isEnabled($storeId)) {
            return $this->scopeConfig->getValue(self::DELIVERYDATE_IS_REQUIRED_CHECKOUT, ScopeInterface::SCOPE_STORE, $storeId);
        }
    }
}