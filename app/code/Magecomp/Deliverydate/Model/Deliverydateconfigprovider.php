<?php
namespace Magecomp\Deliverydate\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

class Deliverydateconfigprovider implements ConfigProviderInterface
{
	const XPATH_SHOW = 'deliverydate/general/enable';
	const XPATH_DATELABEL = 'deliverydate/general/deliverydatelabel';
	const XPATH_COMMENTSHOW = 'deliverydate/general/enabledcomment';
	const XPATH_COMMENTLABEL = 'deliverydate/general/commentlabel';
    const XPATH_DISPLAYTYPE = 'deliverydate/general/datetype';
    const XPATH_DATEFORMATEYTYPE = 'deliverydate/general/dateformatetype';

	
    const XPATH_DISABLED = 'deliverydate/setting/disabled';
    const XPATH_HOURSTART = 'deliverydate/setting/hourstart';
    const XPATH_HOUREND = 'deliverydate/setting/hourend';
	
	const XPATH_SAMEDAY = 'deliverydate/setting/enabledsameday';
	const XPATH_MINIMAL = 'deliverydate/setting/minimalinterval';
	const XPATH_SPCDATE = 'deliverydate/setting/specialdates';
	const XPATH_ISRANGE = 'deliverydate/setting/showdaterange';
	const XPATH_RANGEFROM = 'deliverydate/setting/showdaterangefrom';
	const XPATH_RANGETO = 'deliverydate/setting/showdaterangeto';
	
	const XPATH_CUSFILTER = 'deliverydate/setting/cgroupfilter';
	const XPATH_CUSGROUP = 'deliverydate/setting/custgroup';

    protected $storeManager;
    protected $scopeConfig;
	protected $customer;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Customer\Model\Session $customer
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
		$this->customer = $customer;
    }

    public function getConfig()
    {
        $store = $this->getStoreId();
		$isshow = $this->scopeConfig->getValue(self::XPATH_SHOW, ScopeInterface::SCOPE_STORE, $store);
		$datelabel = $this->scopeConfig->getValue(self::XPATH_DATELABEL, ScopeInterface::SCOPE_STORE, $store);
		$commentshow = $this->scopeConfig->getValue(self::XPATH_COMMENTSHOW, ScopeInterface::SCOPE_STORE, $store);
		$commentlabel = $this->scopeConfig->getValue(self::XPATH_COMMENTLABEL, ScopeInterface::SCOPE_STORE, $store);	
        $disabled = $this->scopeConfig->getValue(self::XPATH_DISABLED, ScopeInterface::SCOPE_STORE, $store);
        $hourstart = $this->scopeConfig->getValue(self::XPATH_HOURSTART, ScopeInterface::SCOPE_STORE, $store);
        $hourend = $this->scopeConfig->getValue(self::XPATH_HOUREND, ScopeInterface::SCOPE_STORE, $store);
		$displaydatetype = $this->scopeConfig->getValue(self::XPATH_DISPLAYTYPE, ScopeInterface::SCOPE_STORE, $store);
        $dateformatetype = $this->scopeConfig->getValue(self::XPATH_DATEFORMATEYTYPE, ScopeInterface::SCOPE_STORE, $store);

		// DISABLE NUMBER DAYS OF WEEKS
		$noday = 0;
        if($disabled == -1) {
            $noday = 1;
        }
		
		// Provide Same Day Delivery Or Not
		$issameday = $this->scopeConfig->getValue(self::XPATH_SAMEDAY, ScopeInterface::SCOPE_STORE, $store);
        $issameday = ($issameday ? 0 : 1);
		
		// Minimal Interval Range For Delivery
		$minimalinterval = $this->scopeConfig->getValue(self::XPATH_MINIMAL, ScopeInterface::SCOPE_STORE, $store);
		$issameday = (int)($issameday + $minimalinterval);
		
		// Special Dates, Not Allowed Delivery
		$spcdate = $this->scopeConfig->getValue(self::XPATH_SPCDATE, ScopeInterface::SCOPE_STORE, $store);

		// Date Range Not Allowed For Selection
		$isrange = $this->scopeConfig->getValue(self::XPATH_ISRANGE, ScopeInterface::SCOPE_STORE, $store);
		$from = $this->scopeConfig->getValue(self::XPATH_RANGEFROM, ScopeInterface::SCOPE_STORE, $store);
		$to = $this->scopeConfig->getValue(self::XPATH_RANGETO, ScopeInterface::SCOPE_STORE, $store);
		if($isrange && $from != '' && $to != '')
		{
			$DFrom = mktime(1,0,0,substr($from,5,2), substr($from,8,2),substr($from,0,4));
            $DTo = mktime(1,0,0,substr($to,5,2), substr($to,8,2),substr($to,0,4));
			
			if ($DTo >= $DFrom)
			{
				//From Date Added To Special Date Array
				if($spcdate == '')
				{
					$spcdate = date('Y-m-d',$DFrom);
				}
				else
				{
					$spcdate .= ",".date('Y-m-d',$DFrom);
				}
				
				// ADD ANOTHER DATES TO ARRAY
				while ($DFrom < $DTo)
				{
					$DFrom += 86400; // add 24 hours
					$spcdate .= ",".date('Y-m-d',$DFrom);
				}
			}
		}
		
		// Customer Group Filter For Delivery Date
		if($isshow)
		{
			if($this->scopeConfig->getValue(self::XPATH_CUSFILTER, ScopeInterface::SCOPE_STORE, $store))
			{
				$groupFilter = explode(',',$this->scopeConfig->getValue(self::XPATH_CUSGROUP, ScopeInterface::SCOPE_STORE, $store));
				if(!in_array($this->customer->getCustomer()->getGroupId(), $groupFilter)) 
				{
					$isshow = 0;
				}
			}
		}

        $config = [
            'shipping' => [
                'deliverydate' => [
					'isshow' => $isshow,
					'datelabel' => $datelabel,
					'commentshow' => $commentshow,
					'commentlabel' => $commentlabel,
                    'disabled' => $disabled,
                    'noday' => $noday,
                    'hourstart' => $hourstart,
                    'hourend' => $hourend,
					'issameday' => $issameday,
					'spcdate' => $spcdate,
                    'displaytype' => $displaydatetype,
                    'dateformatetype' => $dateformatetype
                ]
            ]
        ];

        return $config;
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }
}