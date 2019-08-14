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
class Artio_MTurbo_Block_Adminhtml_Edit_Tab_Main extends Artio_MTurbo_Block_Adminhtml_Edit_Tab_Abstract
{
	
	/**
	 * @var Varien_Data_Form
	 */
	private $form;

    public function __construct() {
        parent::__construct();
        $this->setId('main_section');
        $this->_title = $this->getMyHelper()->__('Options');
    }

    protected function _prepareForm() {

    	$config = Mage::getSingleton('mturbo/config');

        $this->form = new Varien_Data_Form();
        $this->_addGeneralFieldset();
        $this->_addHomepageFieldset();
        //$this->_addAutomaticDownloadFieldset();

        $this->form->setValues($config->getData());
        $this->setForm($this->form);

        return parent::_prepareForm();
        
    }
    
    private function _addGeneralFieldset() {
    	
    	$layoutFieldset = $this->form->addFieldset('general_fieldset', array(
            'legend' => $this->getMyHelper()->__('General Options'),
            'class'  => 'fieldset'
        ));
        
        $layoutFieldset->addField('turbopath', 'text', array(
            'name'      => 'turbopath',
            'label'     => $this->getMyHelper()->__('Cache Path').':',
        	'value'		=> 'var/turbocache'
        ));
        
        $layoutFieldset->addField('multistoreview', 'select', array(
            'name'      => 'multistoreview',
            'label'     => $this->getMyHelper()->__('Enable multi-storeview').':',
        	'options'	=> array(
        					0 => $this->getMyHelper()->__('No'),
        					1 => $this->getMyHelper()->__('Yes'))
        ));
        
        $layoutFieldset->addField('refreshsave', 'select', array(
            'name'      => 'refreshsave',
            'label'     => $this->getMyHelper()->__('Refresh cache after product or category save').':',
        	'options'	=> array(
        					0 => $this->getMyHelper()->__('No'),
        					1 => $this->getMyHelper()->__('Yes'))
        ));
    	
    }
    
    private function _addAutomaticDownloadFieldset() {
    	
    	$layoutFieldset = $this->form->addFieldset('download_fieldset', array(
            'legend' => $this->getMyHelper()->__('Automatic cache management'),
            'class'  => 'fieldset'
        ));
        
        $layoutFieldset->addField('automaticdownload', 'select', array(
            'name'      => 'automaticdownload',
            'label'     => $this->getMyHelper()->__('Enable automatic cache refresh').':',
        	'options'	=> array(
        					0 => $this->getMyHelper()->__('No'),
        					1 => $this->getMyHelper()->__('Yes'))
        ));
        
        $layoutFieldset->addType('crontime', Artio_MTurbo_Helper_Data::FORM_CRON_HOUR_TIME);
        $layoutFieldset->addField('downloadtime', 'crontime', array(
            'name'      => 'downloadtime',
            'label'     => $this->getMyHelper()->__('Download time').':',
        	'style'		=> 'display:inline;width:40px;'
        ));
        
        $layoutFieldset->addField('lastdownload', 'label', array(
            'name'      => 'lastdownload',
            'label'     => $this->getMyHelper()->__('Last downlaod').':',
            'style'     => 'height:24em;',
            'disabled'  => true
        ));
        
       
    	
    }
    
   
    private function _addHomepageFieldset() {
    	
    	$layoutFieldset = $this->form->addFieldset('homepage_fieldset', array(
            'legend' => $this->getMyHelper()->__('Homepage Cache Options'),
            'class'  => 'fieldset'
        ));
        
        $layoutFieldset->addField('homepage', 'select', array(
            'name'      => 'homepage',
            'label'     => $this->getMyHelper()->__('Cache homepage').':',
        	'options'	=> array(
        					0 => $this->getMyHelper()->__('No'),
        					1 => $this->getMyHelper()->__('Yes'))
        ));
    	
    }
    
}
