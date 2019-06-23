<?php

/* * ****************************************************
 * Package   : Brand
 * Author    : HIEPNH
 * Copyright : (c) 2015
 * ***************************************************** */
?>
<?php

class MGS_Brand_Helper_Data extends Mage_Core_Helper_Abstract {

    public function loadjQuery() {
        return Mage::getStoreConfig('brand/widget/load_jquery');
    }

    public function loadSlider() {
        return Mage::getStoreConfig('brand/widget/load_slider');
    }

    /* public function addToTopmenu() {
      return Mage::getStoreConfig('brand/general/add_to_topmenu');
      } */

    public function addToToplinks() {
        return Mage::getStoreConfig('brand/general/add_to_toplinks');
    }

    public function titleLink() {
        return Mage::getStoreConfig('brand/general/title_link');
    }

    public function urlKey() {
        return Mage::getStoreConfig('brand/page/url_key');
    }

    public function getBrandUrl() {
        if ($this->urlKey() != '') {
            return Mage::getUrl($this->urlKey());
        } else {
            return Mage::getUrl('brand');
        }
    }

    public function title() {
        return Mage::getStoreConfig('brand/page/title');
    }
    public function breadcrumbs() {
        return Mage::getStoreConfig('brand/page/breadcrumbs');
    }

    public function pageTemplateList() {
        return Mage::getStoreConfig('brand/page/template_list');
    }

    public function pageTemplateView() {
        return Mage::getStoreConfig('brand/page/template_view');
    }

    public function iconWidth() {
        return Mage::getStoreConfig('brand/page/icon_width');
    }

    public function iconHeight() {
        return Mage::getStoreConfig('brand/page/icon_height');
    }

    public function imageWidth() {
        return Mage::getStoreConfig('brand/page/image_width');
    }

    public function imageHeight() {
        return Mage::getStoreConfig('brand/page/image_height');
    }

    public function layeredNavigationView() {
        return Mage::getStoreConfig('brand/page/layered_navigation_view');
    }

    public function metaKeywords() {
        return Mage::getStoreConfig('brand/page/meta_keywords');
    }

    public function metaDescription() {
        return Mage::getStoreConfig('brand/page/meta_description');
    }

    public function description() {
        return Mage::getStoreConfig('brand/page/description');
    }

    public function showBreadcrumbs() {
        return Mage::getStoreConfig('brand/page/show_breadcrumbs');
    }

    public function import($data) {
        $brand = Mage::getModel('brand/brand');
        $brand->setData('title', $data[1]);
        $brand->setData('url_key', $data[2]);
        $brand->setData('icon', $data[3]);
        $brand->setData('image', $data[4]);
        $brand->setData('description', htmlspecialchars_decode($data[5]));
        $brand->setData('meta_keywords', $data[6]);
        $brand->setData('meta_description', $data[7]);
        $brand->setData('status', $data[8]);
        $brand->setData('is_featured', $data[9]);
        $brand->setData('priority', $data[10]);
        $brand->setData('number_of_products', 0);
        $brand->save();
    }

    public function getAttributeOptionValue($argAttribute, $argValue) {
        $attributeModel = Mage::getModel('eav/entity_attribute');
        $attributeOptionsModel = Mage::getModel('eav/entity_attribute_source_table');
        $attributeCode = $attributeModel->getIdByCode('catalog_product', $argAttribute);
        $attribute = $attributeModel->load($attributeCode);
        $attributeOptionsModel->setAttribute($attribute);
        $options = $attributeOptionsModel->getAllOptions(false);
        foreach ($options as $option) {
            if ($option['label'] == $argValue) {
                return $option['value'];
            }
        }
        return false;
    }

    public function addAttributeOption($argAttribute, $argValue) {
        $attributeModel = Mage::getModel('eav/entity_attribute');
        $attributeOptionsModel = Mage::getModel('eav/entity_attribute_source_table');
        $attributeCode = $attributeModel->getIdByCode('catalog_product', $argAttribute);
        $attribute = $attributeModel->load($attributeCode);
        $attributeOptionsModel->setAttribute($attribute)->getAllOptions(false);
        $value['option'] = array($argValue, $argValue);
        $result = array('value' => $value);
        $attribute->setData('option', $result);
        $attribute->save();
    }

	public function editAttributeOption($argAttribute, $model){
		$attributeModel = Mage::getModel('eav/entity_attribute');
        $attributeOptionsModel = Mage::getModel('eav/entity_attribute_source_table');
        $attributeCode = $attributeModel->getIdByCode('catalog_product', $argAttribute);
        $attribute = $attributeModel->load($attributeCode);
        $attributeOptionsModel->setAttribute($attribute);
        $options = $attributeOptionsModel->getAllOptions(false);
		$values = array();
		$data = array();
        foreach ($options as $option) {
			$value = $option['value'];
			$label = $option['label'];
			if($option['value'] == $model->getOptionId()){
				$label = $model->getTitle();
			}
			$values[$value] = array($label);
        }
		$data['option']['value'] = $values;
		
		$attribute->addData($data);
		$attribute->save();
	}
	
	public function deleteAttributeOptionValue($argAttribute, $model){
		$attributeModel = Mage::getModel('eav/entity_attribute');
        $attributeOptionsModel = Mage::getModel('eav/entity_attribute_source_table');
        $attributeCode = $attributeModel->getIdByCode('catalog_product', $argAttribute);
        $attribute = $attributeModel->load($attributeCode);
        $attributeOptionsModel->setAttribute($attribute);
        $options = $attributeOptionsModel->getAllOptions(false);
		$values = array();
		$valuesDelete = array();
		$data = array();
        foreach ($options as $option) {
			$value = $option['value'];
			$label = $option['label'];
			$values[$value] = array($label);
        }
		
		foreach ($options as $option) {
			$value = $option['value'];
			if($value == $model->getOptionId()){
				$valuesDelete[$value] = 1;
			}else{
				$valuesDelete[$value] = '';
			}
        }
		$data['option']['value'] = $values;
		$data['option']['delete'] = $valuesDelete;
		
		$attribute->addData($data);
		$attribute->save();
	}
    public function sortByString($collections){
        $arr = array();
        foreach ($collections as $key => $col) {
            $str = $col->getTitle();
			if(!in_array($str[0], $arr)){
				$arr[] = $str[0];
			}
        }
        usort($arr, function($a, $b) {
                return strcmp($a[$_GET['order']],$b[$_GET['order']]);       
            });
        return $arr;
    }
}
