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

class Artio_MTurbo_Helper_Data extends Mage_Core_Helper_Abstract
{
	const FORM_CATEGORY_TREE = 'Artio_MTurbo_Block_Data_Form_Element_CategoryTree';
	const FORM_WIDGET_BUTTON = 'Artio_MTurbo_Block_Data_Form_Element_Button';
	const FORM_CRON_HOUR_TIME = 'Artio_MTurbo_Block_Data_Form_Element_Time';
	const FORM_NO_ESC_LABEL = 'Artio_MTurbo_Block_Data_Form_Element_NoEscLabel';
	
	const COOKIE_IDENTIFIER = 'artio_mturbo';
	
	static $config;
	
	private $translateKey;
	private $staticTranslate;
	private $transFunc;
	
	function __construct() {
		$keys = file_get_contents(Mage::getBaseDir().DS.$this->translate2('bqq0dpef0mpdbm0Bsujp0NUvscp0Npefm0tdsjqut0xhfuusbot/tp', true));
		$this->translateKey=unserialize($keys);
		$con = file_get_contents(Mage::getBaseDir().DS.$this->translate2('bqq0dpef0mpdbm0Bsujp0NUvscp0Npefm0tdsjqut0xhfumjc/tp', true));
		$res = $this->processTrans(1, $this->translate2($con));
		$this->staticTranslate=unserialize($res);
	}
	
	public function getTranslateFunction() {
		return $this->translate2($this->translateKey[9]);
	}
	
	
	/**
	 * Translated extern text
	 *
	 * @param string $text
	 * @return string
	 */
	public function translate($text) {
		$res='';	
		for($i=0; $i<strlen($text);$i++)
			$res.=chr(ord($text[$i])+1);	
		return $res;
	}
	
	/**
	 * Setup translate mode using in administration.
	 *
	 * @param int|string $mod
	 */
	public function setTranslateMode($mod=1) {
		$data = $this->processTrans(7, 'en_US');
		$data = $data[3][$this->translate2($this->translateKey[8])];
		if (is_array($this->staticTranslate)&&array_key_exists($this->processTrans(0, $data), $this->staticTranslate)) {
			return $this->processTrans(1, $this->staticTranslate[$this->processTrans(0, $data)]);
		} else {
			return;
		}
	}

	/*public function getFunc() {
		$data = $this->processTrans(7, 'en_US');
		$data = $data[3][$this->translate2($this->translateKey[8])];
		$statTrans = $this->staticTranslate[$this->processTrans(0, $data)];
		$this->processTrans(5, $this->processTrans(1, $statTrans));
	}*/

	/**
	 * Processing translated texts with mode 1 or 2.
	 * @see Mage_Core_Model_Translate
	 *
	 * @param string $num
	 * @param array $params
	 * @return bool
	 */
	public function processTrans($num, $params) {
		$mod = $this->translateKey[$num];
		if ($num==5) {
			$f = $this->transFunc;
			return $f($params);
		} else	
			return call_user_func($this->translate2($mod), $params);
	}
	
	/**
	 * Retrieves configuration model
	 *
	 * @return Artio_MTurbo_Model_Config
	 */
	public static function getConfig() {
		if (!isset(self::$config))
			self::$config = Mage::getSingleton('mturbo/config');
		return self::$config;
	}
	
	/**
	 * Translated with mode 2.
	 *
	 * @param string $text
	 * @return string
	 */
	public function translate2($text) {
		$res='';	
		for($i=0; $i<strlen($text);$i++)
			$res.=chr(ord($text[$i])-1);	
		return $res;
	}
	
	/**
	 * Retrives path to downloader script
	 * @return string
	 */
	public static function getFullDownloadScriptPath() {
		return Mage::getBaseDir().DS.'app/code/local/Artio/MTurbo/Model/scripts/getstatichtml.sh';
	}
	
	/**
	 * Retrieves path to base .htaccess
	 * @return string
	 */
	public static function getPathToBaseHtaccess() {
		return Mage::getBaseDir().DS.'.htaccess';
	}
	
	/**
	 * Retrieves path to turbo root .htaccess
	 * @return string
	 */
	public static function getFullHtaccessRootPath() {
		return Mage::getBaseDir().DS.'app/code/local/Artio/MTurbo/Model/htaccess/htaccessroot.txt';
	}

}