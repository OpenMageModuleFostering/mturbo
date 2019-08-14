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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * 
 *
 * @category   Artio
 * @package    Artio_MTurbo
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Artio_MTurbo_Block_Adminhtml_Edit_Tab_Url 
	extends Mage_Adminhtml_Block_Widget_Grid
	implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('url_section');
      $this->setDefaultSort('mturbo_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection() {
      
      $collection = Mage::getModel('mturbo/mturbo')->getCollection();
      $this->setCollection($collection);
  	  
      return parent::_prepareCollection();
      
  }
  
  protected function _afterLoadCollection() {
  	
  	foreach ($this->getCollection() as $model) {
      	$model->checkExist();
    }
      
  }

  protected function _prepareColumns() {

      $this->addColumn('mturbo_id', array(
          'header'    => Mage::helper('mturbo')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'mturbo_id',
      ));
      
  	  if (!Mage::app()->isSingleStoreMode()) {
      $this->addColumn('store_id', array(
            'header'    => $this->__('Store View'),
            'width'     => '200px',
            'index'     => 'store_id',
            'type'      => 'store',
            'store_view' => true,
        ));
      }

      $this->addColumn('url', array(
          'header'    => Mage::helper('mturbo')->__('Request path'),
          'align'     =>'left',
          'index'     => 'request_path',
      ));
      
      $this->addColumn('exist', array(
          'header'    => Mage::helper('mturbo')->__('Cached'),
          'align'     =>'left',
      	  'type' 	  => 'select',
          'index'     => 'exist',
          'filter'    => false,
          'sortable'  => false,
      	  'renderer'  => new Artio_MTurbo_Block_Data_Grid_Column_Blocked(),
      	  'options'   => array(
              1 => Mage::helper('mturbo')->__('Not cached'),
              0 => Mage::helper('mturbo')->__('Cached')
          )
      ));
      
      $this->addColumn('last_refresh', array(
          'header'    => Mage::helper('mturbo')->__('Last refresh'),
          'align'     =>'left',
      	  'type' 	  => 'datetime',
          'index'     => 'last_refresh',
          'filter'	  => false,
      	  'sortable'  => false
      ));

      $this->addColumn('blocked', array(
          'header'    => Mage::helper('mturbo')->__('Status'),
          'align'     => 'center',
          'width'     => '80px',
          'index'     => 'blocked',
          'type'	  => 'options',
      	  'renderer'  => new Artio_MTurbo_Block_Data_Grid_Column_Blocked(),
          'options'   => array(
              0 => Mage::helper('mturbo')->__('Not blocked'),
              1 => Mage::helper('mturbo')->__('Blocked')
          ),
      ));
	   
      $this->addColumn('action2',
            array(
                'header'    => Mage::helper('mturbo')->__('Cache'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('mturbo')->__('Cache'),
                        'url'     => array(
                            'base'=>'*/*/refresh',
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
       ));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction() {

        $this->setMassactionIdField('mturbo_id');
        $this->getMassactionBlock()->setTemplate('mturbo/massaction.phtml');
        $this->getMassactionBlock()->setFormFieldName('mturbo');
        
       /* $this->getMassactionBlock()->addItem('refresh', array(
             'label'    => Mage::helper('mturbo')->__('Refresh'),
             'url'      => $this->getUrl('*//**//*massRefresh')
        ));*/

        $this->getMassactionBlock()->addItem('block', array(
             'label'    => Mage::helper('mturbo')->__('Block'),
             'url'      => $this->getUrl('*/*/massBlock')
        ));
        
        $this->getMassactionBlock()->addItem('unblock', array(
             'label'    => Mage::helper('mturbo')->__('Unblock'),
             'url'      => $this->getUrl('*/*/massUnblock')
        ));
        
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('mturbo')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('mturbo')->__('Are you sure?')
        ));

        return $this;
    }
    
	public function getMainButtonsHtml()
    {
        $html = '';
        if($this->getFilterVisibility()){
        	//$html.= '<span>'.Mage::helper('mturbo')->__('Before synchronization must be configuration saved').'</span>';
        	$html.= Mage::getSingleton('core/layout')
                		->createBlock('adminhtml/widget_button', '', array(
                    		'label'   => Mage::helper('mturbo')->__('Cache selected pages'),
                    		'type'    => 'button',
                    		'onclick' => $this->_getOnClickScript()
                			))->toHtml();
            $html.= '<input type="hidden" name="massrefresh" id="massrefresh" value="" />';
            $html.= $this->getResetFilterButtonHtml();
            $html.= $this->getSearchButtonHtml();
        }
        return $html;
    }
    
    private function _getOnClickScript() {
    	return "this.form.target='_blank';
    		    $('massrefresh').value = url_section_massactionJsObject.checkedString;
    			this.form.action = '".Mage::helper('adminhtml')->getUrl('*/*/massRefresh')."';
    		    this.form.submit();
    		    this.form.target='_self';
    		    this.form.action = '".Mage::helper('adminhtml')->getUrl('*/*/index')."';
    		    this.form.submit();";
    }

  public function getRowUrl($row)
  {
      //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  
	/**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('mturbo')->__('Url');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('mturbo')->__('Url');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

}