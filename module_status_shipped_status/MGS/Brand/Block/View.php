<?php

/* * ****************************************************
 * Package   : Brand
 * Author    : HIEPNH
 * Copyright : (c) 2015
 * ***************************************************** */
?>
<?php

class MGS_Brand_Block_View extends Mage_Catalog_Block_Product_List {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getBrand() {
        $params = $this->getRequest()->getParams();
        $model = Mage::getModel('brand/brand')->load($params['id']);
        return $model;
    }
	public function getCategory(){
		$brand = $this->getBrand();
		$storeId = Mage::app()->getStore()->getStoreId();
		$urlkey = $brand->getUrlKey();
		$_category = Mage::getModel('catalog/category')->getCollection()
                ->addNameToResult()
                ->addUrlRewriteToResult()
               ->addAttributeToFilter('url_key',$urlkey )
            ->getFirstItem();
		$category = Mage::getModel('catalog/category')->setStoreId($storeId)->load($_category->getId());
		return $category;
	}
	
	/* public function getLoadedProductCollection(){
		
		$brand = $this->getBrand();
		$defaultValue = Mage::getStoreConfig('catalog/frontend/grid_per_page');
		$page = (Mage::app()->getRequest()->getParam('p', 1)) ? (int) Mage::app()->getRequest()->getParam('p', 1) : 1;

		$products = Mage::getModel('catalog/product')->getCollection()
			//->addStoreFilter(0)
			->addAttributeToSelect('*')
			->addAttributeToFilter('mgs_brand',$brand->getOptionId())
			->addAttributeToFilter('visibility',array('eq'=>4))
			->addAttributeToFilter('status', array('eq'=>1))
			->setPage($page, $defaultValue);
			//->setCurPage(1) // 2nd page
			//->setPageSize($defaultValue);
		//echo $products->getSelect();
		//return $this->_getProductCollection()->addAttributeToFilter('mgs_brand',$brand->getOptionId());
		return $products;
	} */
	
	public function getToolbarHtml() {

		$this->setToolbar($this->getLayout()->createBlock('catalog/product_list_toolbar', 'Toolbar'));

		$toolbar = $this->getToolbar();

		$toolbar->enableExpanded();

		$toolbar->setAvailableOrders(array(

				'position' => $this->__('Position'),
				

				'name' => $this->__('Name'),
				'urviser' => $this->__('Urviser'),

				'price' => $this->__('Price'),
				'qty_ordered' => $this->__('Popularitet'),
			))

			->setDefaultDirection('desc')

			->setCollection($this->_getProductCollection());


		$pager = $this->getLayout()->createBlock('page/html_pager', 'Pager');

		$pager->setCollection($this->_getProductCollection());

		$toolbar->setChild('product_list_toolbar_pager', $pager);

		return $toolbar->_toHtml();

	}
	
}
