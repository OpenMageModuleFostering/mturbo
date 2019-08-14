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
class Artio_MTurbo_AdminhtmlController extends Mage_Adminhtml_Controller_Action
{
	
	public function saveAction() {
		
		$request = $this->getRequest();
		
		$newData = array();
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_PREVIEW_CATEGORIES ] = $request->getPost('preview_categories');
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_PRODUCT_CATEGORIES ] = $request->getPost('product_categories');
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_INCLUDED_HOMEPAGE ] = $request->getPost('homepage');
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_TURBOPATH ] = $request->getPost('turbopath');
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_MULTISTOREVIEW ] = $request->getPost('multistoreview');
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_REFRESH_AFTER_SAVE ] = $request->getPost('refreshsave');
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_ENABLE_AUTOMATIC_DOWNLOAD ] = $request->getPost('automaticdownload');
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_DOWNLOAD_TIME ] = $request->getPost('downloadtime');
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_LICENSEID ] = $request->getPost('licenseid');
		
		try {
			$config = Mage::getSingleton('mturbo/config');
			$config->saveAttributes($newData);
			$this->_getSession()->addSuccess(Mage::helper('mturbo')->__('Configuration was successfully saved'));
		} catch (Exception  $e) {
			$this->_getSession()->addError(Mage::helper('mturbo')->__('Configuration error').' : '.$e->getMessage());
		}
		
		$activeTab='';
		foreach ($request->getPost() as $key=>$value) {
			if (strpos($key, 'section')>0) { 
				$activeTab = $key;
				break;
			}
		}
		
		$this->_redirect('mturbo/adminhtml_mturbo/index', array('activeTab'=>$request->getPost('activeTab')));

	}
	
}