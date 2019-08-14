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

class Artio_MTurbo_Helper_Checker extends Mage_Core_Helper_Abstract
{
	const FORM_CATEGORY_TREE = 'Artio_MTurbo_Block_Data_Form_Element_CategoryTree';
	const FORM_WIDGET_BUTTON = 'Artio_MTurbo_Block_Data_Form_Element_Button';
	const FORM_CRON_HOUR_TIME = 'Artio_MTurbo_Block_Data_Form_Element_Time';
	
	const UPGRADE_XML = 'app/design/adminhtml/default/default/layout/mturbo.xml';
	const UPGRADE_CODE = 'app/code/local/Artio/MTurbo';
	
	const COOKIE_IDENTIFIER = 'artio_mturbo';
	
	
	/**
	 * Check permission on htaccess
	 *
	 * @param unknown_type $permission
	 * @return unknown
	 */
	public static function checkPermission() {
		$htaccess = Artio_MTurbo_Helper_Data::getPathToBaseHtaccess();
		$permission = fileperms($htaccess);
		return self::canWrite($permission);
	}
	
	public static function checkPermissionLayout() {
		$perm = fileperms(Mage::getBaseDir().DS.self::UPGRADE_XML);
		return self::canWrite($perm);
	}
	
	public static function checkPermissionCode() {
		$perm = fileperms(Mage::getBaseDir().DS.self::UPGRADE_CODE);
		return self::canWrite($perm);
	}
	
	public static function canWrite($perm) {
		return ($perm) ? (($perm & 0x0080) && ($perm & 0x0010)) : false;
	}
	
	/**
	 * Check all prerequisites.
	 *
	 */
	public static function checkAll() {
		
		if (!self::checkPermission())
			return Mage::helper('mturbo')->__("I can't write to .htaccess, please change permission.");


		return '';
		
	}
	
}
