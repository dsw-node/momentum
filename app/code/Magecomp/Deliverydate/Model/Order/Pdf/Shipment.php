<?php
namespace Magecomp\Deliverydate\Model\Order\Pdf;

class Shipment extends \Magento\Sales\Model\Order\Pdf\Shipment
{
    public function getPdf( $shipments = [] )
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                $this->_localeResolver->emulate($shipment->getStoreId());
                $this->_storeManager->setCurrentStore($shipment->getStoreId());
            }
            $page = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Packing Slip # ') . $shipment->getIncrementId());
            /* Add table */

            // ADD NEW DELIVERY DATE CUSTOMIZATION (START)
            $objMan = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objMan->get('Magecomp\Deliverydate\Helper\Data');
            if ($helper->PrintDeliveryDateIn(5, $order->getStoreId()) && $order->getDeliverydate() != '0000-00-00 00:00:00') {
                $this->_drawHeaderDD($page, $order);
            } else {
                $this->_drawHeader($page);
            }
            // ADD NEW DELIVERY DATE CUSTOMIZATION (END)

            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }
        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            $this->_localeResolver->revert();
        }
        return $pdf;
    }

    protected function _drawHeaderDD( \Zend_Pdf_Page $page, $order )
    {
        // ADD NEW DELIVERY DATE CUSTOMIZATION (START)
        $objMan = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objMan->get('Magecomp\Deliverydate\Helper\Data');
        $formattedDate = $helper->getFomatedDate($order->getDeliverydate());

        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));

        $linesd[0][] = ['text' => __('Delivery Date and Comment'), 'feed' => 35, 'align' => 'center'];
        $lineBlock = ['lines' => $linesd, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
        $page->drawText(trim($formattedDate), 25, $this->y, 'UTF-8');
        $this->y -= 20;
        $page->drawText(trim($order->getDeliverycomment()), 25, $this->y, 'UTF-8');
        $this->y -= 20;
        // ADD NEW DELIVERY DATE CUSTOMIZATION (END)

        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Products'), 'feed' => 100];

        $lines[0][] = ['text' => __('Qty'), 'feed' => 35];

        $lines[0][] = ['text' => __('SKU'), 'feed' => 565, 'align' => 'right'];

        $lineBlock = ['lines' => $lines, 'height' => 10];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
}