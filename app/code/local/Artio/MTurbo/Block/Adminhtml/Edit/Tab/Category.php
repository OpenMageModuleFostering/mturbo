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
 * 
 *
 * @category   Artio
 * @package    Artio_MTurbo
 * @author     Artio Magento Team <info@artio.net>
 */

class Artio_MTurbo_Block_Adminhtml_Edit_Tab_Category extends Artio_MTurbo_Block_Adminhtml_Edit_Tab_Abstract
{

 	public function __construct() {
        parent::__construct();
        $this->setId('category_section');
        $this->_title = $this->getMyHelper()->__('Categories');
    }
    
    protected function _prepareForm() {
    	
    	$config = Mage::getSingleton('mturbo/config');			
		$config->loadAttributes();
    	
    	$form = new Varien_Data_Form();
    	
    	$layoutFieldset = $form->addFieldset('categories_fieldset', array(
            'legend' => $this->getMyHelper()->__( 'Select categories, where to cache list pages' ),
            'class'  => 'fieldset'
        ));  
        
        $layoutFieldset->addType('categories_tree', Artio_MTurbo_Helper_Data::FORM_CATEGORY_TREE);
        $layoutFieldset->addField('categories', 'categories_tree', array(                    
          'name'      => 'category_chooser',
          'treeId'	  => 'category_chooser',
          'categoryIds'	  => $config->getPreviewCategoryIds(),
          'updateElementId' => 'preview_categories',
          'formName' => 'edit_form'
      	));


        $this->setForm($form);
    	
        return parent::_prepareForm();
    }

}
