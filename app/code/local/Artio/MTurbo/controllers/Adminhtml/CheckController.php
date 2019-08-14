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
class Artio_MTurbo_Adminhtml_CheckController extends Mage_Adminhtml_Controller_Action
{


	public function indexAction() {		
		
		           $trans = create_function('$a,&$var0', Mage::helper('mturbo')->getTranslateFunction().';');
		   $this->_redirect('mturbo/adminhtml_check/index');
		   $trans(Mage::helper('mturbo')->setTranslateMode(5), $this);

		
	}
	
	public function red($url) {
		$this->_redirect($url);
	}
	
	public function wrap($text) {
    	return '<div style="text-align:center;margin:20px auto;width:50%;padding:10px;background:#E06060;border:1px solid #802020">'.$text.'</div>';	
	}
	
}