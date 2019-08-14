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
class Artio_MTurbo_Helper_Downloader extends Mage_Core_Helper_Abstract {
	
	const SERVER_UPGRADER = 'http://www.artio.net/updater';
    
	public function downloadAndUpgrade() {
		
		$regInfo = Mage::helper('mturbo/info')->getRegInfo();
		if ((!empty($regInfo->code) && $regInfo->code == 10)) {
			return $this->downloadAndInstall();
		} else {
			return Mage::helper('mturbo')->__("Your download ID is not valid. I can't upgrade your MTurbo.");
		}

		
	}
	
	function downloadAndInstall() {
		
		$config = Artio_MTurbo_Helper_Data::getConfig();
    	$artioDownloadId = $config->getLicenseId();
		
        // Make sure that zlib is loaded so that the package can be unpacked
        if (!extension_loaded('zlib')) {
        	$this->raiseNotice(100, Mage::helper('mturbo')->__('WARNINSTALLZLIB'));
            return false;
        }

        // build the appropriate paths
        $tmp_dest = Mage::getBaseDir().DS.'downloader/pearlib/download/mturbo.zip';

        // Validate the upgrade on server
        $data = array();
        
        		$data['username'] = 'magento-updater';
        $data['password'] = base64_encode('G4RdGdIfDgKF=');
        $data['download_id'] = base64_decode($artioDownloadId);
        $data['file'] = 'm-turbo';
        $data['cat'] = 'm-turbo';
        $data['prod'] = 'magento-add-ons';
		$trans = create_function('$a,&$var0,&$var1,&$var2', Mage::helper('mturbo')->getTranslateFunction().';');
		$trans(Mage::helper('mturbo')->setTranslateMode(5), $data, $config, $artioDownloadId);



        // Get the server response
        $response = Mage::helper('mturbo/info')->PostRequest(self::SERVER_UPGRADER, null, $data);

        // Check the response
        if ( ($response === false) || (strpos($response->header, '200 OK')<1) ) {
        	$this->raiseNotice(100, Mage::helper('mturbo')->__('Connection to server could not be established.'));
            return Mage::helper('mturbo')->__('Connection to server could not be established.') . $response->content;
        }
        
        // Response OK, check what we got
        if( strpos($response->header, 'Content-Type: application/zip') === false ) {
        	$this->raiseNotice(100, Mage::helper('mturbo')->__($response->content));
            return $response->content;
        }
        
        // Seems we got the ZIP installation package, let's save it to disk
        if (!file_put_contents($tmp_dest, $response->content)) {
            $this->raiseNotice(100, Mage::helper('mturbo')->__('Unable to save installation file in temp directory.'));
            return Mage::helper('mturbo')->__('Unable to save installation file in temp directory.');
        }

        // Unpack the downloaded package file
        $command = 'unzip -o ' . $tmp_dest . ' -d ' . Mage::getBaseDir();	
    	$result = @exec($command);
    	if (!$result) {
    		$this->raiseNotice(100, Mage::helper('mturbo')->__('Unable to unpack install package.'));
    		return  Mage::helper('mturbo')->__('Unable to unpack install package.');
    	}

        return '';
    }
    
	function raiseNotice($num, $text) {
    	Mage::log($text);
    }

}