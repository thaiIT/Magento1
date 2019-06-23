<?php
class MGS_Brand_Model_Url extends Mage_Core_Model_Abstract
{
	public function getUrlKey() {
        $helper = Mage::helper('brand');
        if ($helper->urlKey() != '') {
            $urlKey = Mage::helper('brand')->urlKey();
        } else {
            $urlKey = 'brand';
        }
        return $urlKey;
    }
}
