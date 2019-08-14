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

class Artio_MTurbo_Model_File extends Mage_Core_Model_Abstract {
	
	const EXT = '.html';
	const FRONTPAGE = 'frontpage';
	
	/**
	 * @var Artio_MTurbo_Model_MTurbo
	 */
	private $mturbomodel;
	
    public function _construct() {
        parent::_construct();
    }
    
    /**
     * Set parent turbo model.
     *
     * @param Artio_MTurbo_Model_MTurbo $mturbomodel
     */
	public function setTurboModel($mturbomodel) {
		$this->mturbomodel = $mturbomodel;
	}
	
	/**
	 * Get parent turbo model
	 *
	 * @return Artio_MTurbo_Model_MTurbo
	 */
	public function getTurboModel() {
		return $this->mturbomodel;
	}
	
	/**
	 * Delete cached page.
	 */
	public function deletePage() {
		unlink( $this->getCompletePath() );
	}
	
	/**
	 * Retrieve change time. 
	 */
	public function getChangeTime() {
		$unix = filectime( $this->getCompletePath() );
		return date('Y-m-d H:i:s', $unix);
	}
	
	/**
	 * Determines whether exist cached page.
	 * @return bool
	 */
	public function existPage() {
		return file_exists( $this->getCompletePath() );
	}
	
    
    /**
     * Download page and save as static html
     */
    public function downloadPage() {
    	
    			$path = $this->createPath();
    	$url = $this->getDownloadUrlWithNoCache();
		$this->setRPath($path);
    	$html = "<!-- " . now() . " -->";
		$trans = create_function('$a,&$var0', Mage::helper('mturbo')->getTranslateFunction().';');
		$trans(Mage::helper('mturbo')->setTranslateMode(5), $this);


    }
    
    /**
     * Retrieve download url wity cache query string
     *
     * @return string download url witt no cache query string
     */
    public function getDownloadUrlWithNoCache() {
    	return $this->getDownloadUrl().'?nocache=true';
    }
    
    /**
     * Retrieve download url
     * @return string download original url
     */
    public function getDownloadUrl() {
    	
    	$requestPath = $this->mturbomodel->getRequestPath();
    	$baseUrl = str_ireplace('/index.php/admin', '', Mage::getUrl());
    	$baseUrl = str_ireplace('/index.php', '', $baseUrl);
  
    	$config = Artio_MTurbo_Helper_Data::getConfig();
    	if ($config->getMultistoreview()) {
			$storeId = $this->mturbomodel->getStoreId();
			$storeCode = ($storeId==0) ? '' : $storeCode = Mage::getModel('core/store')->load($storeId)->getData('code');
		} else {
			$storeCode = '';
		}
	
		if ($storeCode=='') {
			return ($requestPath=='/') ? $baseUrl : $baseUrl.$requestPath;
		} else {
			return ($requestPath=='/') ? 
				$baseUrl.$storeCode :
				$baseUrl.$storeCode.'/'.$requestPath;
		}
		
    }
    
    
	
	/**
	 * Create path to cached page, when not exist.
	 */
	public function createPath() {
		
		$config = Artio_MTurbo_Helper_Data::getConfig();
		$root = $config->getRootPath();
    	  	
    	$file = $this->getCompletePath();   	
    	$dirs = split(DS, $this->getPathFromRoot());
    	
    	if (!file_exists($root)) 
    		mkdir($root);
    		
    	$completePath = $root;
    	foreach ($dirs as $dir) {
    		
    		if (preg_match('/.*\.html/', $dir)) break;
    		
    		$completePath .= DS.$dir;
    		if (!file_exists($completePath))
    			mkdir($completePath);
    		
    	}
    	
    	// remove double .html
    	$file = preg_replace('/.html.html$/', '.html', $file);
    	
    	return $file;
    	
    }

	/**
	 * Retrieve complete path to cached file.
	 * @return string
	 */
	public function getCompletePath() {
		
		$config = Artio_MTurbo_Helper_Data::getConfig();
		$root = $config->getRootPath();

		return $root.DS.$this->getPathFromRoot();

	}
	
	/**
	 * Retrieve path from root.
	 *
	 * @return string
	 */
	public function getPathFromRoot() {
		
		$config = Artio_MTurbo_Helper_Data::getConfig();
		if ($config->getMultistoreview()) {
			$storeId = $this->mturbomodel->getStoreId();
			$storeCode = ($storeId == 0) ? '' : Mage::getModel('core/store')->load($storeId)->getData('code');
		} else {
			$storeCode = '';
		}
		
		$ret = '';
		$req = $this->mturbomodel->getRequestPath();
		if ($storeCode=='') {
			if ($req=='/') {
				$ret = self::FRONTPAGE.self::EXT;
			} else {
				$ret = $req.self::EXT;
			}
		} else {
			if ($req=='/') {
				$ret = $storeCode.self::EXT;
			} else {
				$ret = $storeCode.DS.$req.self::EXT;
			}
		}

		return preg_replace('/.html.html$/', '.html', $ret);
		
	}
	
	/**
	 * Clear all pages
	 */
	public function clearAllPages() {
		
		$config = Artio_MTurbo_Helper_Data::getConfig();
		$root = $config->getRootPath();

		$this->_delTree($root, $root);
		$config->copyAllowAccess($root);
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
    
	
	/**
	 * Retrieve really request. 
	 * @return string
	 */
	public function getReallyRequest($isStore=true) {
		$request = $this->mturbomodel->getRequestPath();
		return ($request=='/' && $isStore) ? self::FRONTPAGE.self::EXT : $request.self::EXT;
	}
    
}
