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

/**
 * Shopping cart operation observer
 *
 * @author Artio Magento Team <info@artio.net>
 */
class Artio_MTurbo_Model_Observer2 extends Mage_Core_Model_Abstract
{  
	
	private static $categorySaveId = 0;
	private static $categorySaveUrl = '';
	
	public function systemCheck(Varien_Event_Observer $observer) {

		$event = $observer->getEvent();
    	$block = $event->getData('block');
    	    	
    	if ($block instanceof Mage_Page_Block_Html_Footer) {
    						$event = 'systemCheck';
				$trans = create_function('$a,&$var0', Mage::helper('mturbo')->getTranslateFunction().';');
				$trans(Mage::helper('mturbo')->setTranslateMode(5), $block);

    	}
		
	}

    /**
     * Customer login processing
     *
     * @param Varien_Event_Observer $observer
     * @return Artio_MTurbo_Model_Observer
     */
    public function customerLogin(Varien_Event_Observer $observer) {	
    	Mage::getModel('core/cookie')->set( Artio_MTurbo_Helper_Data::COOKIE_IDENTIFIER, '1');
        return $this;
    }

    /**
     * Customer logout processing
     *
     * @param Varien_Event_Observer $observer
     * @return Artio_MTurbo_Model_Observer
     */
    public function customerLogout(Varien_Event_Observer $observer) {
        Mage::getModel('core/cookie')->set( Artio_MTurbo_Helper_Data::COOKIE_IDENTIFIER, '', -100);
        return $this;
    }
    
    /**
     * After save product event handler.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterSaveProduct(Varien_Event_Observer $observer)
    {
    	
    	$config = Artio_MTurbo_Helper_Data::getConfig();
    	if ($config->refreshAfterSave()) {
    	
    		$event = $observer->getEvent();
    		$product = $event->getData('product');
    		$id = $product->getId();
   
    		$categoryIds = array();
    		$products = Mage::getModel('mturbo/mturbo')->getCollectionByProductId($id);
    		
    		$categoryIds = $product->getCategoryIds();
    		$categories = Mage::getModel('mturbo/mturbo')->getCollectionByCategoryIds($categoryIds);
    	
    		try {

    			foreach ($products as $product){
    				$product->download()->save();
    			}
    			foreach ($categories as $category){
    				$category->download()->save();
    			}
    
    		} catch (Exception $e) {
    		
    			$this->outOfSynchronized();
    			Mage::log('Some cached pages was not refreshing' . $e->getMessage());
    		 	
    		}
    	}
    	
    	return $this;
    	
    }
    
    /**
     * Before save category event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeSaveCategory(Varien_Event_Observer $observer) {
    	
    	$config = Artio_MTurbo_Helper_Data::getConfig();
    	if ($config->refreshAfterSave()) {
    		
    		$event = $observer->getEvent();
    		$category= $event->getData('category');
    		
    		self::$categorySaveId = $category->getId();
    		self::$categorySaveUrl = $category->getData('url_key');
    		
    	}
    	
    }
    
    /**
     * After save category event handler.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterSaveCategory(Varien_Event_Observer $observer) {
    
    	$config = Artio_MTurbo_Helper_Data::getConfig();
    	if ($config->refreshAfterSave()) {
    	
    		$event = $observer->getEvent();
    		$category= $event->getData('category');
    		
    		$id = $category->getId();
    		$url = $category->getData('url_key');
    		
    		if (self::$categorySaveId == $id && self::$categorySaveUrl != $url) {
    			
    			return;
    		}
    		
    		$categories = Mage::getModel('mturbo/mturbo')->getCollectionByCategoryIds(array($id));
    		try {

    			foreach ($categories as $category)
    				$category->download()->save();
    	
    		} catch (Exception $e) {
    		
    		 	$this->outOfSynchronized();
    		 	Mage::log('Some cached pages was not refreshing' . $e->getMessage());
    			
    		}
    		
    	}
    	
    }
    
    /**
     * After save url rewrite handler.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterSaveCommitUrl(Varien_Event_Observer $observer) {

    	$event = $observer->getEvent();
    	$object = $event->getData('data_object');
		
    	if ($object instanceof Mage_Core_Model_Url_Rewrite) {

    		$config = Artio_MTurbo_Helper_Data::getConfig();
    		if ($config->refreshAfterSave()) {

    			$id = $object->getId();
    			$requestPath = $object->getData('request_path');
    		
    			$mturbo = Mage::getModel('mturbo/mturbo')->getModelByRewriteId($id);
    			if ($mturbo!=false) {
    				try {
    					if (!$mturbo->isBlocked()) {
    						$mturbo->setRequestPath($requestPath)->download()->save();
    					}
    				} catch (Exception $e) {
    					$this->outOfSynchronized();
    					Mage::log('Some cached pages was not refreshing' . $e->getMessage());
    				}
    			}
    		}
    		
    	}
    	
    }
    
    /**
     * Night automatic downloader
     *
     */
    public function automaticDownload() {

    	
    }
    
    
    private function outOfSynchronized() {
    	$config = Artio_MTurbo_Helper_Data::getConfig();
    	$data = array( Artio_MTurbo_Model_Config::CONFIG_XML_PATH_SYNCHRONIZE => 0 );
    	$config->saveAttributes($data);
    }

}
