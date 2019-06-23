<?php
class MGS_Megamenu_Block_Megamenu extends Mage_Catalog_Block_Navigation
{
	protected $_setting;
	
	public function __construct()
    {
      $this->_setting['width'] = Mage::getStoreConfig('megamenu/general/width');
      $this->_setting['height'] = Mage::getStoreConfig('megamenu/general/height');
      $this->_setting['theme'] = Mage::getStoreConfig('megamenu/general/theme');
      parent::__construct();
      $this->addData(array('cache_lifetime' => NULL));
    }
	
	protected function _prepareLayout()
    {
		if(Mage::getStoreConfig('megamenu/general/enabled')){
			$theme = $this->_setting['theme'];
			/* $this->getLayout()->getBlock('head')->addCss('mgs_megamenu/'.$theme.'/navigation.css'); */
		}
        return parent::_prepareLayout();
    }
	
	public function getMegamenuItems(){
		$store = Mage::app()->getStore();
		$menuCollection = Mage::getModel('megamenu/megamenu')
			->getCollection()
			->addStoreFilter($store)
			->addNowFilter()
			->addFieldToFilter('status', 1)
			->setOrder('position', 'ASC')
		;
		return $menuCollection;
	}
	
	public function getClass($item){
		$col = $item->getColumns();
		$type = $item->getMenuType();
		$class = $item->getSpecialClass();
		$class.=' '.$item->getAlignMenu();
		if($type==2){
			$class.= " static-menu cols".$col;
			if($item->getStaticContent()!=''){
				$class.= " dropdown";
			}
			$currentUrl = Mage::helper("core/url")->getCurrentUrl();
      $class.= $currentUrl;
			if($currentUrl==$item->getUrl()){
				$class.= " active";
			}
		}
		else{
			$category = Mage::getModel('catalog/category')->load($item->getCategoryId());
			
			$class.= " category-menu cols".$col;
			if ($this->isCategoryActive($category)) {
				if(Mage::app()->getWebsite(true)->getDefaultStore()->getRootCategoryId() != $category->getId()){
					$class.= " active";
				}
			}
			
		}
		return $class;
	}
	
	public function getMenuHtml($item){
		$type = $item->getMenuType();
		if($type==2){
			return $this->getStaticMenu($item);
		}
		else{
			return $this->getCategoryMenu($item);
		}
	}
	
	public function getStaticMenu($item){
		$html = '<a href="'.$item->getUrl().'" class="level0';
		if($item->getStaticContent()!=''){
			$html.= ' parent';
		}
		$html.= '" style="line-height:'.$this->_setting['height'].'px"><span>'.$item->getTitle().'</span></a>';
		if($item->getStaticContent()!=''){
			$col = $item->getColumns();
			$percentOfWidth = 100/Mage::getStoreConfig('megamenu/general/max_column');
			$width = ($this->_setting['width']*($col*$percentOfWidth))/100;
			$html.='<span class="toggle-menu visible-xs-block visible-sm-block"><a onclick="showMenu(\'mobile-menu-' . $item->getId() . $this->getMenuId() . '\');" href="javascript:void(0)" class="icon-drop mobile-menu-' . $item->getId() . $this->getMenuId() . '"><i class="fa fa-plus"></i></a></span>';
			$html.='<div class="dropdown-container '.$item->getAlignDropdown().'" style="width:'.$width.'px; top:'.$this->_setting['height'].'px" id="mobile-menu-' . $item->getId() . $this->getMenuId() . '"><div class="dropdown dropdown-static col'.$item->getColumns().'"><div class="col">';
			
			$helper = Mage::helper('cms');
			$processor = $helper->getPageTemplateProcessor();
			$html.= $processor->filter($item->getStaticContent());

			$html.='</div></div></div>';
		}
		return $html;
	}
	
	public function getCategoryMenu($item){
		$html = '<a';
		$categoryId = $item->getCategoryId();
		$subCatAccepp = $this->getSubCategoryAccepp($categoryId, $item);
		if($categoryId){
			$category = Mage::getModel('catalog/category')->load($categoryId);
			$html.=' href="';
			if($item->getUrl()!=''){
				$html.= $item->getUrl().'"';
			}
			else{
				if(Mage::app()->getWebsite(true)->getDefaultStore()->getRootCategoryId() == $category->getId()){
					$html.='#" onclick="return false"';
				}
				else{
					$html.= $this->getCategoryUrl($category).'"';
				}
			}
		}
		$html.=' class="level0';
		
		if(count($subCatAccepp)>0){
			$html.= ' parent';
		}
		
		$html.='" style="line-height:'.$this->_setting['height'].'px"';
		$html.='><span>'.$item->getTitle();
		$html.= '</span></a>';
		
		if(count($subCatAccepp)>0 || $item->getTopContent()!='' || $item->getBottomContent()!=''){
			$col = $item->getColumns();
			$percentOfWidth = 100/Mage::getStoreConfig('megamenu/general/max_column');
			$width = ($this->_setting['width']*($col*$percentOfWidth))/100;
			$html.='<span class="toggle-menu visible-xs-block visible-sm-block"><a onclick="showMenu(\'mobile-menu-' . $item->getId() . $this->getMenuId() . '\');" href="javascript:void(0)" class="icon-drop mobile-menu-' . $item->getId() . $this->getMenuId() . '"><i class="fa fa-plus"></i></a></span>';
			$html.='<div class="dropdown-container '.$item->getAlignDropdown().'" style="width:'.$width.'px; top:'.$this->_setting['height'].'px" id="mobile-menu-' . $item->getId() . $this->getMenuId() . '"><div class="dropdown dropdown-category col'.$col.'"><div class="col">';
			
			$helper = Mage::helper('cms');
			$processor = $helper->getPageTemplateProcessor();
			
			if($item->getTopContent()!=''){
				$html.='<div class="top_content static-content">';
				$html.= $processor->filter($item->getTopContent());
				$html.='</div>';
			}
			
			$columnAccepp = count($subCatAccepp);
			
			
			if($columnAccepp>0){
				$columns = $item->getColumns();
				
				
				$html.='<div class="category-list">';
				
				if($item->getLeftContent() != '' && $item->getLeftContent() != NULL){
					$columns--;
					$html.= '<div class="sub-column left-content static-content">';
					$html.= $processor->filter($item->getLeftContent());
					$html.= '</div>';
				}
				
				if($item->getRightContent() != '' && $item->getRightContent() != NULL){
					$columns--;
				}

				
				$arrOneElement = array_chunk($subCatAccepp, 1);
				$countCat = count($subCatAccepp);
				$count = 0;
				while ($countCat > 0) {
					for($i=0; $i<$columns; $i++){
						if(isset($subCatAccepp[$count])){
							$arrColumn[$i][] = $subCatAccepp[$count];
							$count++;
						}
					}
					$countCat--;
				}
				
				foreach($arrColumn as $_arrColumn){
					$html.= $this->drawListSub($item, $_arrColumn);
				}
				
				if($item->getRightContent() != '' && $item->getRightContent() != NULL){
					$html.= '<div class="sub-column right-content static-content">';
					$html.= $processor->filter($item->getRightContent());
					$html.= '</div>';
				}
				
				$html.='</div>';
			}
			
			if($item->getBottomContent()!=''){
				$html.='<div class="bottom_content static-content">';
				$html.= $processor->filter($item->getBottomContent());
				$html.='</div>';
			}

			$html.='<div class="clearer"></div></div></div></div>';
		}
		
		return $html;
	}
	
	public function drawListSub($item, $catIds){
		$html = '<div class="sub-column">';
		if(count($catIds)>0){
			foreach($catIds as $categoryId){
				$category = Mage::getModel('catalog/category')->load($categoryId);
				if($item->getUseThumbnail()==1){
					if($category->getThumbnail()!=''){
						$html.='<div class="category-thumbnail">';
						$html.='<a class="thumbnail-img-link"'.$this->getCategoryUrl($category).'"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/category/'.$category->getThumbnail().'" alt=""/></a>';
						$html.='</div>';
					}
				}
				$html.='<ul>';
				$html.= $this->drawList($category, $item);
				$html.='</ul>';
			}
		}
		$html.= '</div>';
		return $html;
	}
	
	public function drawList($category, $item, $level=1){
		$maxLevel = $item->getMaxLevel();
		if($maxLevel=='' || $maxLevel==NULL){
			$maxLevel = Mage::getStoreConfig('megamenu/general/max_level');
		}
		
		if($maxLevel==0 || $maxLevel=='' || $maxLevel==NULL){
			$maxLevel = 100;
		}
		
		$children = $this->getSubCategoryAccepp($category->getId(), $item);
		$childrenCount = count($children);
		
		$htmlLi = '<li class="level'.$level.'">';
		$html[] = $htmlLi;
		$html[] = '<a class="level'.$level.'" href="'.$this->getCategoryUrl($category).'">';
		$html[] = $category->getName();
		$html[] = '</a>';
		
		if($level<$maxLevel){
			$maxSub = Mage::getStoreConfig('megamenu/general/max_subcat');
			if($maxSub==0 || $maxSub==''){
				$maxSub = 100;
			}
			$htmlChildren = '';
			if($childrenCount>0){
				$i=0;
				foreach ($children as $child) {
					$i++;
					if($i<=$maxSub){
						$_child = Mage::getModel('catalog/category')->load($child);
						$htmlChildren .= $this->drawList($_child, $item, ($level + 1));
					}
				}
			}
			if (!empty($htmlChildren)) {
				$html[] = '<ul>';
				$html[] = $htmlChildren;
				$html[] = '</ul>';
			}
		}
        $html[] = '</li>';
        $html = implode("\n", $html);
        return $html;
	}
	
	public function getSubCategoryAccepp($categoryId, $item){
		$subCatExist = explode(',', $item->getSubCategoryIds());
		
		$category = Mage::getModel('catalog/category')->load($categoryId);
		
		$children = $category->getChildrenCategories();
		$childrenCount = count($children);
		
		$subCatId = array();
		if($childrenCount>0){
			foreach ($children as $child){
				if(in_array($child->getId(), $subCatExist)){
					$subCatId[] = $child->getId();
				}
			}
		}
		return $subCatId;
	}
}