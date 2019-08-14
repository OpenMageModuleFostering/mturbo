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
 * Adminhtml cms block edit form
 *
 * @category   Artio
 * @package    Artio_MTurbo
 * @author     Artio <info@artio.net>
 */
class Artio_MTurbo_Block_Adminhtml_Welcome_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm() {

    	$form = new Varien_Data_Form(array(
    		'name'=>'welcome_form', 
    		'id' => 'welcome_form', 
    		'action' =>  Mage::helper('adminhtml')->getUrl('*/*/install'),
    		'method' => 'post'));
    	
        $form->setUseContainer(true);
      
        $layoutFieldset = $form->addFieldset('general_fieldset', array(
            'legend' => Mage::helper('mturbo')->__('Your first options'),
            'class'  => 'fieldset',
        ));
        
        $layoutFieldset->addField('turbopath', 'text', array(
            'name'      => 'turbopath',
            'label'     => Mage::helper('mturbo')->__('Cache Path').':',
        	'value'		=> 'var/turbocache'
        ));
        
        $form->addType('widget_button', Artio_MTurbo_Helper_Data::FORM_WIDGET_BUTTON);
        $form->addField('install_button', 'widget_button', array(
        	'name'		=> 'install_button',
        	'label'		=> Mage::helper('mturbo')->__('Save and Install'),
        	'onclick'	=> "welcome_form.submit()",
        	'style'		=> "text-align:right;"
        ));
        
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    protected function _afterToHtml($html) {
    	
    	if (Mage::helper('mturbo/info')->getRegName()) {
    	
    		if (Artio_MTurbo_Helper_Checker::checkPermission()) {
    			$html = $this->_getOkText() . $html;
    		} else {
    			$html = $this->_wrapErrorDiv(Mage::helper('mturbo')->__("I can't write to .htaccess, please change permission."));
    		}
    		
    	} else {
    		$html = $this->_wrapErrorDiv( Mage::helper('mturbo')->__('No file is decoded. Probably, your licence is not loaded on server.') );
    	}
    	
    	return $html;
    }
    
    private function _getOkText() {
    	$text = Mage::helper('mturbo')->__('Welcome to M-Turbo Cache developed by Artio.
To complete installation, we need to have entered the path to your directory where you want to store cached pages.
This path is then entered into a .htaccess file, which is a function of the components necessary.');
    	return $this->_wrapInfoDiv($text);
    }
    
    private function _wrapErrorDiv($error) {
    	return '<div style="margin-bottom:10px;padding:10px;background:#E06060;border:1px solid #802020">'.$error.'</div>';
    }
    
    private function _wrapInfoDiv($text) {
    	return '<div style="margin-bottom:10px;padding:10px;">'.$text.'</div>';
    }
    
    
}
