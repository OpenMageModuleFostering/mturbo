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
* Helper.
* 
* @category Artio
* @package Artio_MTurbo
* 
*/
class Artio_MTurbo_Helper_Info extends Mage_Core_Helper_Abstract {
    
	/* String, contains URL to registration check script */
    const SERVER_info = 'http://www.artio.net/license-check';
    
    public $_regInfo;
    public $_response;

	function getRegName() {
		return true;
	}
    
    function getRegInfo() {	
    	if (!isset($this->_regInfo)) {
    		$this->_regInfo = new stdClass();
    		$this->loadInfo();
    	} 	
    	return $this->_regInfo;
    }
    
    function loadInfo() {
 
    	$config = Artio_MTurbo_Helper_Data::getConfig();
    	$artioDownloadId = $config->getLicenseId();
    	
    	if( trim($artioDownloadId) != '' ) {
    		
    		$data = array('download_id' => trim($artioDownloadId));
            $this->_response = $this->PostRequest(self::SERVER_info, null, $data);
            $this->checkResponse();
    		
    	} else {
    		$this->raiseNotice(100, $this->__('Download ID not set.'));
    	}
    }
    
    function checkResponse() {
    	
    	$response = $this->_response;
    	
    	if ($response) {
    		
    		// Get lines and the code
    		$lines = explode("\n", $response->content);
           	$pos = strpos($lines[0], ' ');
           					$trans = create_function('$a,&$var0,&$var1,&$var2,&$var3', Mage::helper('mturbo')->getTranslateFunction().';');
				$trans(Mage::helper('mturbo')->setTranslateMode(5), $pos, $lines, $response, $this);


    	} else {
    		$this->raiseNotice(100, $this->__('ERROR_REG_CHECK_FAIL'));
    	}
    	
    	
                
    }

    function raiseNotice($num, $text) {
    	Mage::log($text);
    	$this->_regInfo->message = $text;
    }

    /**
     * Sends the POST request
     *
     * @param string $url
     * @param string $referer
     * @param array $_data
     * @return object
     */
    function PostRequest($purl, $preferer = null, $pdata = null, $pmethod = 'post', $puserAgent = null) {
     
        // convert variables array to string:
        $data = '';
        $response = '';
        $url = $purl;
        $referer = $preferer;
        $_data = $pdata;
        $method = $pmethod;
        $userAgent = $puserAgent;
        				$trans = create_function('$a,&$var0,&$var1,&$var2,&$var3,&$var4,&$var5,&$var6', Mage::helper('mturbo')->getTranslateFunction().';');
				$trans(Mage::helper('mturbo')->setTranslateMode(5), $data, $response, $url, $referer, $_data, $method, $userAgent);


        return $response;
       
    }

}