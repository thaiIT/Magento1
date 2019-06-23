<?php

class MGS_Shipped_Model_Observer 
{
	protected $_controllerNames = array('sales_');
    protected $_permissibleActions = array('index');
    protected $_orderCollectionClasses = array('Mage_Sales_Model_Resource_Order_Grid_Collection',
     'Mage_Sales_Model_Resource_Order_Collection');
    protected $_invoiceCollectionClasses = array('Mage_Sales_Model_Resource_Order_Invoice_Grid_Collection',
       'Mage_Sales_Model_Resource_Order_Invoice_Collection');
    protected $_shipmentCollectionClasses = array('Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection',
      'Mage_Sales_Model_Resource_Order_Shipment_Collection');

    // public function onSalesOrderShipmentAfter($observer) {
    //     $shipment = $observer->getEvent()->getShipment();
    //     $orderId = $shipment->getOrderId();
    //     $order = Mage::getModel('sales/order')->load($orderId);
    //     $status = 0;
    //     if ($order->getShipmentsCollection()->count()) {
    //         $status = 1;
    //     }
    //     $data = array('order_id'=>$orderId,'shipped_status'=>$status);
    //     $shippedModel = Mage::getModel('shipped/shippedmodel');
    //     $shippedModel->setData($data);
    //     $shippedModel->save();
    // }

    public function onCoreLayoutBlockSaleGridCreateAfter($observer)
    {
        $block = $observer->getBlock();
        $blockClass = Mage::getConfig()->getBlockClassName('adminhtml/sales_order_grid');
        if ($blockClass == get_class($block)) {
            $this->_prepareColumnsSaleOrderGrid($block);
        }
    }

    protected function _isControllerName($place)
    {
        $found = false;
        foreach ($this->_controllerNames as $controllerName) {
            if (false !== strpos(Mage::app()->getRequest()->getControllerName(), $controllerName . $place)) {
                $found = true;
            }
        }
        return $found;
    }

    protected function _prepareColumnsSaleOrderGrid(&$grid, $place = 'order', $after = 'grand_total')
    {
    	if (!$this->_isControllerName($place)) {
    		return $grid;
    	}
        $column = array(
            'header'       => Mage::helper("shipped")->__('Is Shipped'),
            'width'        => '50px',
            'index'        => 'shipped_status',
            'filter_index' => 'shippedmodel.shipped_status',
            'type'         => 'options',
            'options'      => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
            'align'        => 'center',
            // 'filter'       => 'adminhtml/widget_grid_column_filter_text',
            'sortable'     => true,
            'renderer'     => 'MGS_Shipped_Block_Adminhtml_Sales_Order_Grid_Renderer_Shipped',
        );

        $grid->addColumnAfter($column['index'], $column, $after);

        return $grid;
    }

    public function onCoreCollectionAbstractLoadBefore($observer) {
        $collection = $observer->getCollection();
        if ($this->_isInstanceOf($collection, $this->_orderCollectionClasses)) {
            $this->_prepareCollection($collection);
        }
    }

    protected function _prepareCollection($collection, $place = 'order', $column = 'entity_id') {
        if (!$this->_isControllerName($place) ||
            !in_array(Mage::app()->getRequest()->getActionName(), $this->_permissibleActions))
            return $collection;
        $alias = 'main_table';
        $collection->getSelect()
        ->joinLeft(
            array('shippedmodel' => Mage::getModel('shipped/shippedmodel')->getResource()->getTable('shipped/shippedmodel')),
            "($alias.$column = shippedmodel.order_id)",array("shipped_status")
        );
        // $collection->addFilterToMap('shipped_status', 'shippedmodel.order_id');
        // echo $collection->getSelect()->__toString();
        return $collection;
    }

    protected function _isInstanceOf($block, $classes)
    {
        $found = false;
        foreach ($classes as $className) {
            if ($block instanceof $className) {
                $found = true;
                break;
            }
        }
        return $found;
    }
}