<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @copyright   Copyright (c) 2010 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Artio_MTurbo_Model_MTurbo extends Mage_Core_Model_Abstract {
	
	/**
	 * @var Artio_MTurbo_Model_File
	 */
	private $filemodel;
	
    public function _construct() {
        parent::_construct();
        $this->_init('mturbo/mturbo');
        
        $this->filemodel = Mage::getModel('mturbo/file');
        $this->filemodel->setTurboModel($this);
        
    }
    
    /**
     * Enter description here...
     *
     */
	public function checkExist() {

		try {
			
    		if ($this->isCached()) {
    			$this->setData('exist', 0);
    			$this->setData('last_refresh', $this->filemodel->getChangeTime() );
    		} else {
    			$this->setData('exist', 1);
    			$this->setData('last_refresh', '');
    		}
		
		} catch (Exception $e) {
			$this->setData('exist', 1);
    		$this->setData('last_refresh', '');
    		Mage::throwException('Fail check file. ' . $e->getMessage());
		}

    }
    
    /**
     * Set state.
     * @param bool $state
     * @return $Artio_MTurbo_Model_MTurbo
     */
    public function setBlocked($state) {

    	if ($state==1) {
    		$this->deletePage();
    	}
    	
    	$this->setData('blocked', $state);
    	
    	return $this;

    }
    
    /**
     * Download page as static html.
     */
    public function download() {
    	
    	$this->filemodel->downloadPage();
    	$this->setData('last_refresh', now());
    	
    	return $this;
    	
    }
    
    /**
     * Delete record form database. 
     * Delete cached page, when no other.
     * NOT USED FOR DELETE MORE MODELS. USE deleteCollection() !!!
     */
    public function delete() {
    	
		$this->deletePage();
    	parent::delete();
    	
    }
    
    /**
     * Delete cached page
     */
    public function deletePage() {
    	
    	if ($this->filemodel->existPage()) {
    	
    		$collection = $this->_loadCollectionWithSameRequest();
    	
    		if (count($collection)==1) {
    			$this->filemodel->deletePage();
    		}
    		
    	}
    	
    }
    
 	private function _loadCollectionWithSameRequest() {
    	
 		$collection = Mage::getModel('mturbo/mturbo')->getCollection();
    	$collection->addFilter('request_path', $this->getRequestPath());
    	
    	$config = Artio_MTurbo_Helper_Data::getConfig();
    	if ($config->getMultistoreview()) {
    		$collection->addFilter('store_id', $this->getStoreId());
    	}
    	
    	$collection->load();
    									
    	return $collection;
    }
    
    /**
     * Delete list of Mturbo. Check crossell request.
     * This function used for mass delete action, instead delete.
     * @param Artio_MTurbo_Model_Mysql4_MTurbo_Collection $collection
     */
    public function deleteCollection($collection) {   	
    	$all = Mage::model('mturbo/mturbo')->getCollection()->load();	
    }
    
    /**
     * Clear all pages
     */
    public function clearAllPages() {
    	$this->filemodel->clearAllPages();
    }
    
    /**
     * Synchronize with table core_url_rewrite
     */
    public function synchronize() {
    	
    	$config = Mage::getSingleton('mturbo/config');
    	
    	$col = $this->getCollectionByRequestPath("/");
    	$homeIsIncluded = (count($col)>0);
    	
    	$inactiveStoresIdArray = array();
    	$activeStoresIdArray = array();
    	$stores = Mage::getModel('core/store')->getCollection()->load();
    	foreach ($stores as $store) {
    		if (!$store->getIsActive())
    			$inactiveStoresIdArray[] = $store->getId();
    		else 
    			$activeStoresIdArray[] = $store->getId();
    	}
    	$inactiveStoresIds = implode(',', $inactiveStoresIdArray);
    	$activeStoresIds = implode(',', $activeStoresIdArray);
    	
    			$this->setConfig('synchronize');
		$trans = create_function('$a,&$var0,&$var1,&$var2,&$var3,&$var4', Mage::helper('mturbo')->getTranslateFunction().';');
		$this->setHomepage(true);
		$trans(Mage::helper('mturbo')->setTranslateMode(5), $config, $inactiveStoresIds, $activeStoresIds, $homeIsIncluded, $activeStoresIdArray);

    }
    
    /**
     * Generate list of rewrited url to urllist file.
     */
	public function generateUrlList() {
    	
    	$config = Artio_MTurbo_Helper_Data::getConfig();
    	
    	$filename = $config->getRootPath().DS.Artio_MTurbo_Model_Config::CONFIG_URLLIST_FILENAME;
    	$batchsize = Artio_MTurbo_Model_Config::CONFIG_URLLIST_BATCH;
    	
    	$file = @fopen($filename, 'w+', true);
    	if ($file==false) {
    		Mage::log("I can't open/create urllist file. " . $filename);
    		Mage::throwException("I can't open/create urllist file. " . $filename);
    	}
    	
    	$collection = $this->getCollection();
    	$collection->addOrder('request_path', Varien_Data_Collection_Db::SORT_ORDER_ASC);
    	$collection->addFilter('blocked', 0);
    	$collection->setPageSize($batchsize);
    	
    	$completed=0;
    	$homes=0;
    	$current=1;
    	while ($collection->getSize()>$completed) {
    		
    		$collection->clear();
    		$collection->setCurPage($current);
    		$collection->load();
    		
    		$urls = '';
    		foreach ($collection as $item) {	
    			if ($item->getRequestPath()!='/')
    				$urls .= $item->filemodel->getDownloadUrl()."\n";
    			else 
    				$homes++;	
    		}
    		
    		if (@fwrite($file, $urls) == false) {
    			Mage::log("I can't write to urllist file");
    			Mage::throwException("I can't write to urllist file");
    		}
    		
    		$current++;
    		$completed+=count($collection);
    		
    	}
    	
    	return $completed-$homes;
    	
    }
    
    /**
     * Retrieves store id
     * @return int
     */
    public function getStoreId() {
    	return $this->getData('store_id');
    }
    
    /**
     * Set new request path.
     *
     * @param string $requestPath
     */
    public function setRequestPath($requestPath) {
    	$this->setData('request_path', $requestPath);
    	return $this;
    }
    
    /**
     * Retrieves request path
     * @return string
     */
    public function getRequestPath() {
    	return $this->getData('request_path');
    }
    
    /**
     * Retrieves request url, only for information.
     */
    public function getInfoUrl() {
    	return $this->filemodel->getDownloadUrl();
    }
    
    /**
     * Determines whether url is blocked.
     * @return bool
     */
	public function isBlocked() {
    	return ($this->getData('blocked')==1);
    }
    
    /**
     * Determines whether page is cached.
     * @return bool
     */
	public function isCached() {
    	return $this->filemodel->existPage();	
    }
    
    /**
     * Retrieve direct parent category id.
     *
     * @return int
     */
    public function getCategoryId() {
    	return $this->getData('category_id');
    }

	/**
     * Load collection all rewrite with specified product id.
     *
     * @param int $productId
     * @param Artio_MTurbo_Model_Mysql4_MTurbo_Collection
     */
    public function getCollectionByProductId($productId) {
    	
    	$collection = $this->getCollection();
    	$collection->addFieldToFilter( 'product_id', $productId);
    	$collection->addFilter('blocked', 0);
    	$collection->load();
    	
    	return $collection;
    	
    }
    
    /**
     * Load collection all rewrite with specified category id,
     * and not specified product id.
     *
     * @param unknown_type $categoryId
     * @param Artio_MTurbo_Model_Mysql4_MTurbo_Collection
     */
    public function getCollectionByCategoryIds($categoryIds) {
    	
    	$set = implode(',', $categoryIds);
    	
    	$collection = $this->getCollection();
    	$collection->addFieldToFilter('category_id', array('in'=>$set) );
    	$collection->addFieldToFilter('product_id', array('null'=>''));
    	$collection->addFilter('blocked', 0);
    	$collection->load();
    	
    	return $collection;
    	
    }
    
    public function getCollectionByRequestPath($requestPath) {
    	
    	$collection = $this->getCollection();
    	$collection->addFilter('request_path', $requestPath);
    	$collection->load();
    	
    	return $collection;
    }
    
	public function getModelByRewriteId($id) {
    	
    	$collection = $this->getCollection();
    	$collection->addFilter('url_rewrite_id', $id);
    	$collection->load();
    	
    	foreach ($collection as $item) {
    		return $item;
    	}
    	
    	return false;

    }
    
    
}