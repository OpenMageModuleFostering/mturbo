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

class Artio_MTurbo_Model_Config extends Mage_Eav_Model_Config
{
	
	const CONFIG_PATH_TO_MTURBO_HTACCESS = 'htaccess/htaccess.txt';
	const CONFIG_PATH_TO_MTURBO_DOWNLOAD = 'scripts/getstatichtml.sh';
	const CONFIG_HTACCESS_PATHCONSTANT = '$ROOTPATH';
	const CONFIG_HTACCESS_FINDKEY = 'M-Turbo Accelleration';
	const CONFIG_HTACCESS_FINDBASE = 'RewriteBase';
	const CONFIG_HTACCESS_FINDENGINEON = 'RewriteEngine on';
	
	const CONFIG_URLLIST_FILENAME = 'urllist.txt';
	const CONFIG_URLLIST_BATCH = 50;
	
	const CONFIG_XML_PATH_PREVIEW_CATEGORIES = 'mturbo/previewcats';
	const CONFIG_XML_PATH_PRODUCT_CATEGORIES = 'mturbo/productcats';
	const CONFIG_XML_PATH_INCLUDED_HOMEPAGE = 'mturbo/homepage';
	const CONFIG_XML_PATH_TURBOPATH = 'mturbo/turbopath';
	const CONFIG_XML_PATH_MULTISTOREVIEW = 'mturbo/multistoreview';
	const CONFIG_XML_PATH_REFRESH_AFTER_SAVE = 'mturbo/refreshsave';
	const CONFIG_XML_PATH_SYNCHRONIZE = 'mturbo/synchronize';
	const CONFIG_XML_PATH_FIRSTCONFIG = 'mturbo/firstconfig';
	
	const CONFIG_XML_PATH_ENABLE_AUTOMATIC_DOWNLOAD = 'mturbo/automaticdownload';
	const CONFIG_XML_PATH_DOWNLOAD_TIME = 'crontab/jobs/mturbo_mturbo/schedule/cron_expr';
	const CONFIG_XML_PATH_LAST_DOWNLOAD = 'mturbo/lastdownload';
	
	const CONFIG_XML_PATH_DOWNLOAD_MODEL_PATH = 'crontab/jobs/mturbo_mturbo/run/model';
	const CONFIG_XML_PATH_DOWNLOAD_MODEL_VALUE = 'mturbo/observer::automaticDownload';
	
	const CONFIG_XML_PATH_LICENSEID = 'mturbo/licenseid';
	
	/**
	 * Ids categories with caching preview
	 *
	 * @var string
	 */
	private $previewcats;
	
	/**
	 * Ids categories with caching product pages
	 *
	 * @var string
	 */
	private $productcats;
	
	/**
	 * path => config_id
	 * @var array
	 */
	private $pathids = array();
	
	/**
	 * Has Cache homepage?
	 *
	 * @var bool
	 */
	private $homepage;
	
	/**
	 * Cache Path.
	 * 
	 * @var string
	 */
	private $turbopath;
	
	/**
	 * Multistoreview mod
	 * 
	 * @var bool
	 */
	private $multistoreview;
	
	/**
	 * Indicate whether url is synchronized.
	 *
	 * @var bool
	 */
	private $synchronize;
	
	/**
	 * Refresh on saved
	 *
	 * @var bool
	 */
	private $refreshsave;
	
	/**
	 * Enabled automatic download in the night.
	 *
	 * @var bool
	 */
	private $automaticdownload;
	
	/**
	 * Download time.
	 * 
	 * @var time
	 */
	private $downloadtime;
	
	/**
	 * Last download.
	 *
	 * @var time
	 */
	private $lastdownload;
	
	/**
	 * First configuration indicator
	 *
	 * @var bool
	 */
	private $firstconfig;
	
	/**
	 * License id
	 *
	 * @var string
	 */
	private $licenseid;
	
	public function __construct() {
		$this->loadAttributes();
	}
	
	/**
	 * @return string
	 */
	public function getLicenseId() {
		return $this->licenseid;
	}
	
	/**
	 * @param string $licenseid
	 */
	public function setLicenseId($licenseid) {
		$this->licenseid = $licenseid;
	}

	
	/**
	 * @return bool
	 */
	public function isFirstConfig() {
		return $this->firstconfig;
	}
	
	/**
	 * @return time
	 */
	public function getDownloadtime() {
		return $this->downloadtime;
	}
	
	/**
	 * @return time
	 */
	public function getLastdownload() {
		return $this->lastdownload;
	}
	
	/**
	 * @param time $downloadtime
	 */
	public function setDownloadtime($downloadtime) {
		$this->downloadtime = $downloadtime;
	}
	
	/**
	 * @param time $lastdownload
	 */
	public function setLastdownload($lastdownload) {
		$this->lastdownload = $lastdownload;
	}

	
	/**
	 * @return bool
	 */
	public function isEnableAutomaticDownload() {
		return $this->automaticdownload;
	}
	
	/**
	 * @param bool $automaticdownload
	 */
	public function setAutomaticDownload($automaticdownload) {
		$this->automaticdownload = $automaticdownload;
	}

	
	public function isSynchronize() {
		return $this->synchronize;
	}
	
	public function setSynchronize($state) {
		$this->synchornize = $state;
	}
	
	/**
	 * @return bool
	 */
	public function refreshAfterSave() {
		return $this->refreshsave;
	}
	
	/**
	 * @param bool $refreshsave
	 */
	public function setRefreshAfterSave($refreshsave) {
		$this->refreshsave = $refreshsave;
	}

	
	/**
	 * @return bool
	 */
	public function getMultistoreview() {
		return $this->multistoreview;
	}
	
	/**
	 * @param bool $multistoreview
	 */
	public function setMultistoreview($multistoreview) {
		$this->multistoreview = $multistoreview;
	}

	
	/**
	 * @return string
	 */
	public function getTurbopath() {
		return $this->turbopath;
	}
	
	/**
	 * @param string $turbopath
	 */
	public function setTurbopath($turbopath) {
		$this->turbopath = $turbopath;
	}
	
	/**
	 * Retrivese root path to directory with static pages.
	 * 
	 * @return string
	 */
	public function getRootPath() {
		
		$turbopath = $this->getTurbopath();
    	$root = Mage::getBaseDir().DS.$turbopath;
    	return $root;
    	
	}

	
	/**
	 * @return bool
	 */
	public function homepageIsIncluded() {
		return $this->homepage;
	}
	
	/** 
	 * @param bool $homepage
	 */
	public function setIncludedHomepage($homepage) {
		$this->homepage = $homepage;
	}
	
	/**
	 * @return string
	 */
	public function getPreviewCategoryIds() {
		if (strpos($this->previewcats, ',')==0) $this->previewcats = substr($this->previewcats, 1);
		return empty($this->previewcats) ? array() : explode(',', $this->previewcats);
	}
	
	/**
	 * @return string
	 */
	public function getProductCategoryIds() {
		if (strpos($this->productcats, ',')==0) $this->productcats = substr($this->productcats, 1);
		return empty($this->productcats) ? array() : explode(',', $this->productcats);
	}
	
	/**
	 * @param string $previewcats
	 */
	public function setPreviewCategoryIds($previewcats) {
		$this->previewcats = $previewcats;
	}
	
	/**
	 * @param string $productcats
	 */
	public function setProductCategoryIds($productcats) {
		$this->productcats = $productcats;
	}
	
	/**
	 * Retrieves data as associated array.
	 */
	public function getData() {
		
		$this->loadAttributes();
		return get_object_vars($this);

	}
	
	/**
	 * Update htacces by configured root path.
	 */
	public function updateHtacces($newPath='') {
		
		$oldPath = $this->getRootPath();
		$newPath = $newPath=='' ? $oldPath : Mage::getBaseDir().DS.$newPath;
		
		$htaccesPath = Mage::getBaseDir().DS.'.htaccess';
		
		$origContent = file_get_contents($htaccesPath);
		if ($origContent == false) {
			Mage::throwException("I can't read original .htaccess");
		}
		
		$exist = (strpos($origContent, self::CONFIG_HTACCESS_FINDKEY) > 0);
		if ($exist) {
			
			$origContent = str_replace($oldPath, $newPath, $origContent);
			
		} else {
			
			$htaccesContent = file_get_contents(self::CONFIG_PATH_TO_MTURBO_HTACCESS, true);
			if ($htaccesContent == false) {
				Mage::throwException("I can't read added .htaccess");
			}
			
			$htaccesContent = str_replace(self::CONFIG_HTACCESS_PATHCONSTANT, $newPath, $htaccesContent);
			
			$posEngineOn = strpos($origContent, self::CONFIG_HTACCESS_FINDENGINEON);
			$posEngineNL = strpos($origContent, "\n", $posEngineOn);
			$posBase = strpos($origContent, self::CONFIG_HTACCESS_FINDBASE);
			$posBaseNL = strpos($origContent, "\n", $posBase);
			$position = ($posBaseNL > $posEngineNL) ? $posBaseNL : $posEngineNL;
			
			$origContent = $this->str_insert($htaccesContent, $origContent, $position+1);
			
		}
		
		if (file_put_contents($htaccesPath, $origContent) == false) {
			Mage::throwException("I can't write to .htaccess");
		}
		
		return $this;
		
	}
	
	/**
	 * Change turbo root path.
	 *
	 * @param string $newPath
	 * @return bool
	 */
	public function changeTurboPath($newPath='') {

		Mage::log('Changing turbo path: ' . $newPath );
		$baseDir = Mage::getBaseDir();
		$oldPath = $this->getTurbopath();
		
		if (file_exists($baseDir.DS.$newPath))
			return true;
		
		if (file_exists($baseDir.DS.$oldPath) && (!$this->isFirstConfig())) {
			
			$command = 'mv ' . $this->getRootPath() . ' ' . $baseDir.DS.$newPath;
			$result = @exec($command);
			return ($result=='');
			
		} else {
			
			Mage::log('Create new turbo path');
			$result = mkdir(Mage::getBaseDir().DS.$newPath);
			if (!$result) {
				Mage::log('Creating turbo path: ' . Mage::getBaseDir().DS.$newPath. ' fail');
			}
			$result2 = $this->copyAllowAccess(Mage::getBaseDir().DS.$newPath);
			if (!$result2) {
				Mage::log('Copy .htaccess: ' . Mage::getBaseDir().DS.$newPath. ' fail');
			}
			return $result && $result2;
			
		}
	}
	
	/**
	 * Copy allow for all htaccess to turbo root path.
	 *
	 * @param string $path
	 * @return bool
	 */
	public function copyAllowAccess($path) {
		$dest = $path.DS.'.htaccess';
		$source = Artio_MTurbo_Helper_Data::getFullHtaccessRootPath();
		if (file_exists($source) && file_exists($path)) {
			return copy($source, $dest);
		} else {
			Mage::log('Do not copy: ' . $source . ' >> ' . $path);
			return true;
		}
	}
	
	private function str_insert($insertstring, $intostring, $offset) {
   		$part1 = substr($intostring, 0, $offset);
   		$part2 = substr($intostring, $offset);
  
   		$part1 = $part1 . $insertstring;
   		$whole = $part1 . $part2;
  	 	return $whole;
	}
	
	/**
	 * Load attributes from core_config_data
	 * @return Artio_MTurbo_Model_Config
	 */
	public function loadAttributes() {
		
		if (count($this->pathids)==0) {
		
			$config = Mage::getModel('core/config_data');
			$mydata = get_object_vars($this);

			$collection = $config->getCollection();
			$collection->addFieldToFilter('path', array('like'=>'%mturbo%'));
			$collection->load();
		
			foreach ($collection as $object) {
				$path = $object->getData('path');
				
				$keys = split('/', $path);
				
				if (strpos($path, 'rontab')>0) {
					if ($path==self::CONFIG_XML_PATH_DOWNLOAD_TIME)
						$this->setDownloadtime($object->getData('value'));
					$this->pathids[$path] = $object->getData('config_id'); 
					continue;
				}
				
				if (count($keys)!==2)	
					Mage::throwException('Bad key for configuration MTurbo. Key must be as "mturbo/KEY"');
		
				if (in_array($this->$keys[1], $mydata)) 
					$this->$keys[1] = $object->getData('value');
				else
					Mage::throwException('Key "' . $keys[1] . '" not found');
					
				$this->pathids[$path] = $object->getData('config_id'); 
			}
			
		}
		
		return $this;
		
	}
	
	
	public function saveAttributes($attributes=array()) {
		
		$this->loadAttributes();
		
		$saveTransaction = Mage::getModel('core/resource_transaction');
		
		foreach ($this->_getListAttributes() as $path=>$oldValue) {
			
			if (array_key_exists($path, $attributes)) {
			
				$newValue = $attributes[$path];

				if ($newValue !== $oldValue) {
	
					$dataObject = Mage::getModel('core/config_data');
					$dataObject->setPath($path);
					$dataObject->setValue($newValue);
			
					if (array_key_exists($path, $this->pathids)) {
						$dataObject->setId( $this->pathids[$path] );
					}
			
					$saveTransaction->addObject($dataObject);
				
				}
				
			}
			
		}
		
		
		/** save crontab expression */
		if (array_key_exists(self::CONFIG_XML_PATH_DOWNLOAD_TIME, $attributes)) {
			$this->_saveExtraAttribute(
				self::CONFIG_XML_PATH_DOWNLOAD_TIME,
				$this->_formatDownloadTimeToCron($attributes[self::CONFIG_XML_PATH_DOWNLOAD_TIME]),
				$saveTransaction);	
		}
		$this->_saveExtraAttribute( self::CONFIG_XML_PATH_DOWNLOAD_MODEL_PATH,
									self::CONFIG_XML_PATH_DOWNLOAD_MODEL_VALUE,
									$saveTransaction);
		
		
		if ($this->_isNewTurboPath($attributes)) {
			$newPath = $attributes[self::CONFIG_XML_PATH_TURBOPATH];
			try {
				$this->changeTurboPath($newPath);
				$this->updateHtacces($newPath);
			} catch (Exception $e) {
				Mage::log('For change turbopath Throw exception ' . $e->getMessage());
				$this->changeTurboPath($this->getTurbopath());
				throw $e;
			}
		}
		
		$saveTransaction->save();
		
		return $this;
	
	}
	
	private function _saveExtraAttribute($path, $value, $transaction) {
		$dataObject = Mage::getModel('core/config_data');
		$dataObject->setPath($path);
		$dataObject->setValue($value);	
		if (array_key_exists($path, $this->pathids)) {
			$dataObject->setId( $this->pathids[$path] );
		}
		$transaction->addObject($dataObject);
	}
	
	private function _formatDownloadTimeToCron($value) {
		$hours = (int)$value[0];
		$minutes = (int)$value[1];
		return $minutes . ' ' . $hours . ' * * *';
	}
	
	private function _isNewTurboPath($attributes=array()) {
		if (array_key_exists(self::CONFIG_XML_PATH_TURBOPATH, $attributes)) {
			return ($attributes[self::CONFIG_XML_PATH_TURBOPATH] != $this->getTurbopath());
		} else {
			return false;
		}
	}
	
	private function _getListAttributes() {
		return array(
			self::CONFIG_XML_PATH_PREVIEW_CATEGORIES => $this->previewcats,
			self::CONFIG_XML_PATH_PRODUCT_CATEGORIES => $this->productcats,
			self::CONFIG_XML_PATH_INCLUDED_HOMEPAGE => $this->homepage,
			self::CONFIG_XML_PATH_TURBOPATH => $this->turbopath,
			self::CONFIG_XML_PATH_MULTISTOREVIEW => $this->multistoreview,
			self::CONFIG_XML_PATH_REFRESH_AFTER_SAVE => $this->refreshsave,
			self::CONFIG_XML_PATH_SYNCHRONIZE => $this->synchronize,
			self::CONFIG_XML_PATH_ENABLE_AUTOMATIC_DOWNLOAD => $this->automaticdownload,
			self::CONFIG_XML_PATH_LAST_DOWNLOAD => $this->lastdownload,
			self::CONFIG_XML_PATH_FIRSTCONFIG => $this->firstconfig,
			self::CONFIG_XML_PATH_LICENSEID => $this->licenseid
		);
	}


}
