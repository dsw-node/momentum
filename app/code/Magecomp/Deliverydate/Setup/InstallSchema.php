<?php
namespace Magecomp\Deliverydate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'deliverydate',
            [
                'type' => 'datetime',
                'nullable' => false,
                'comment' => 'Delivery Date',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'deliverycomment',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Delivery Comment',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'deliverydate',
            [
                'type' => 'datetime',
                'nullable' => false,
                'comment' => 'Delivery Date',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'deliverycomment',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Delivery Comment',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'deliverydate',
            [
                'type' => 'datetime',
                'nullable' => false,
                'comment' => 'Delivery Date',
            ]
        );

        $setup->endSetup();
    }
}