<?php

class MGS_Shipped_Block_Adminhtml_Sales_Order_Grid_Renderer_Shipped extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $arrOrderId = [];
    	$orderId = $row->getData('entity_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        $shippedModel = Mage::getModel('shipped/shippedmodel');
        // echo "<pre>";print_r($shippedModel->getCollection()->getData());die();
        foreach ($shippedModel->getCollection() as $value) {
            $arrOrderId[] = $value->getOrderId();
        }
        if ($order->getShipmentsCollection()->count()) {
            if (!in_array($orderId, $arrOrderId)) {
            	$data = array('order_id'=>$orderId,'shipped_status'=>1);
    	        $shippedModel->setData($data);
    	        $shippedModel->save();
            }
        	return "Yes";
        } else {
            if (!in_array($orderId, $arrOrderId)) {
            	$data = array('order_id'=>$orderId,'shipped_status'=>0);
    	        $shippedModel = Mage::getModel('shipped/shippedmodel');
    	        $shippedModel->setData($data);
    	        $shippedModel->save();
            }
        	return "No";
        }
    }
}