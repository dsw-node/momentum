<?php
namespace Magecomp\Deliverydate\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magecomp\Deliverydate\Helper\Data as DeliveryHelper;

class Deliverydate extends Column
{
    const ALT_FIELD = 'delivery_date';

    protected $storeManager;
    protected $scopeConfig;
    protected $deliveryhelper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        DeliveryHelper $deliveryhelper,
        array $components = [],
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->deliveryhelper = $deliveryhelper;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource( array $dataSource )
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$fieldName] = $this->deliveryhelper->getDisplayDateByConfig($item[$fieldName]);
            }
        }
        return $dataSource;
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }
}