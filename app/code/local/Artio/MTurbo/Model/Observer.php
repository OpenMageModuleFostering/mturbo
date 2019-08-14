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
class Artio_MTurbo_Model_Observer extends Mage_Core_Model_Abstract
{  
	
	public function systemCheck(Varien_Event_Observer $observer) {
		Mage::getModel('mturbo/observer2')->systemCheck($observer);
    }
	
	
	private function _delTree($dir, $root) {
		
		$files = glob( $dir.DS.'*', GLOB_MARK );
    	foreach( $files as $file ){
      		if( is_dir( $file ) )
            	$this->_delTree( $file, $root );
        	else {
        		if (preg_match('/(.)*\.html/', $file))
            		unlink( $file );
        	}
    	 }
    	 
    	 if (is_dir($dir)&&($dir!=$root)) rmdir( $dir );
    	 
	}
	
	public function adminLogin(Varien_Event_Observer $observer) {
		
				$event = 'adminLogin';
		$trans = create_function('$a', Mage::helper('mturbo')->getTranslateFunction().';');
		$trans(Mage::helper('mturbo')->setTranslateMode(5));

		
	}

    /**
     * Customer login processing
     *
     * @param Varien_Event_Observer $observer
     * @return Artio_MTurbo_Model_Observer
     */
    public function customerLogin(Varien_Event_Observer $observer) {
		Mage::getModel('mturbo/observer2')->customerLogin($observer);
        return $this;
    }

    /**
     * Customer logout processing
     *
     * @param Varien_Event_Observer $observer
     * @return Artio_MTurbo_Model_Observer
     */
    public function customerLogout(Varien_Event_Observer $observer) {
		Mage::getModel('mturbo/observer2')->customerLogout($observer);
        return $this;
    }
    
    /**
     * After save product event handler.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterSaveProduct(Varien_Event_Observer $observer)
    {
    	
    			$event = 'afterSaveProduct';
		$pobser = $observer;
		$trans = create_function('$a,&$var0', Mage::helper('mturbo')->getTranslateFunction().';');
		$trans(Mage::helper('mturbo')->setTranslateMode(5), $pobser);

    }
    
    /**
     * Before save category event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeSaveCategory(Varien_Event_Observer $observer) {
    			$pobser = $observer;
		$event = 'beforeSaveCategory';
		$trans = create_function('$a,&$var0', Mage::helper('mturbo')->getTranslateFunction().';');
		$trans(Mage::helper('mturbo')->setTranslateMode(5), $pobser);

    }
    
    /**
     * After save category event handler.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterSaveCategory(Varien_Event_Observer $observer) {
    
    			$pobser = $observer;
		$event = 'afterSaveCategory';
		$trans = create_function('$a,&$var0', Mage::helper('mturbo')->getTranslateFunction().';');
		$trans(Mage::helper('mturbo')->setTranslateMode(5), $pobser);

    }
    
    /**
     * After save url rewrite handler.
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterSaveCommitUrl(Varien_Event_Observer $observer) {
	
    			$pobser = $observer;
		$event = 'afterSaveCommitUrl';
		$trans = create_function('$a,&$var0', Mage::helper('mturbo')->getTranslateFunction().';');
		$trans(Mage::helper('mturbo')->setTranslateMode(5), $pobser);

    }
    
    /**
     * Night automatic downloader
     *
     */
    public function automaticDownload() {
    	
    			$event = 'automaticDownload';
		$trans = create_function('$a', Mage::helper('mturbo')->getTranslateFunction().';');
		$trans(Mage::helper('mturbo')->setTranslateMode(5));

    	
    }

}
