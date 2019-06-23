<?php

class MGS_Shipped_Model_Mysql4_Shippedmodel extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {
        $this->_init('shipped/shippedmodel', 'shipped_id');
    }
}