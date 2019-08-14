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
class Artio_MTurbo_Adminhtml_MturboController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Initailization layout
	 **/
	protected function _initAction() {
		
		$this->loadLayout();   
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setContainerCssClass('catalog-categories');
		
    	$this->_setActiveMenu('system');
    	$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Turbo Cache Management'), Mage::helper('adminhtml')->__('Turbo Cache Management'));
    	
    	return $this;
	}
	

	public function indexAction() {		
		
		$config = Artio_MTurbo_Helper_Data::getConfig();
		if ($config->isFirstConfig()) {
			$this->_redirect('mturbo/adminhtml_mturbo/first');
		} else {
			$this->_initAction()->renderLayout();
		}
		
		
	}
	
	public function firstAction() {
		$this->_initAction()->renderLayout();
	}
	
	public function installAction() {
		
		$request = $this->getRequest();
		$newData = array();
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_TURBOPATH ] = $request->getPost('turbopath');
		$newData[ Artio_MTurbo_Model_Config::CONFIG_XML_PATH_FIRSTCONFIG ] = '0';

		try {
			$config = Mage::getSingleton('mturbo/config');
			$config->saveAttributes($newData);
			
			$this->_getSession()->addSuccess(Mage::helper('mturbo')->__('Installation complete. Welcome!!!'));
			
		} catch (Exception  $e) {
			$this->_getSession()->addError(Mage::helper('mturbo')->__('Configuration error').' : '.$e->getMessage());
		}
		
		$this->_redirect('mturbo/adminhtml_mturbo/index');
		
	}
	
	
	public function categoriesJsonAction() {
		    					
		if ($this->getRequest()->getParam('expand_all'))
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(true);
        else 
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(false);
         
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $categoryId);
            if (!$category = $this->_initCategory()) return;

            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('adminhtml/catalog_category_tree')
                    ->getTreeJson($category)
            );
        }
                
	}	
	
	protected function _initCategory() {
		    	    	
        $categoryId = (int) $this->getRequest()->getParam('id',false);
        $storeId    = (int) $this->getRequest()->getParam('store');
        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    // load root category instead wrong one
                    if ($getRootInstead) {
                        $category->load($rootId);
                    }
                    else {
                        $this->_redirect('*/*/', array('_current'=>true, 'id'=>null));
                        return false;
                    }
                }
            }
        }

        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);
        return $category;
        
    }
    
    public function downloadAction() {
    	
    	if (!$this->_checkLicence()) return;
    	
    	$importids = $this->getRequest()->getParam('massrefresh');
    	if (empty($importids)) {
    		$model = Mage::getModel('mturbo/mturbo');
			$model->synchronize();
    		$this->getResponse()->setBody($this->getLayout()->createBlock('mturbo/adminhtml_run')->toHtml());
    	} else {
    		$importidsArray = explode(",", $importids);
    		if (is_array($importidsArray)) {
    			$this->getResponse()->setBody($this->getLayout()
    				->createBlock('mturbo/adminhtml_run')
    				->setImportIds($importidsArray)
    				->toHtml());
    		}
    	}
    	
        $this->getResponse()->sendResponse();
    }
    
    public function synchronizeAction() {
    	
    	if (!$this->_checkLicence()) return;

    	try {
			$model = Mage::getModel('mturbo/mturbo');
			$model->synchronize();
			$this->_getSession()->addSuccess(Mage::helper('mturbo')->__('Synchronization complete'));
		} catch (Exception  $e) {
			$this->_getSession()->addError(Mage::helper('mturbo')->__('Synchronization error').' : '.$e->getMessage());
		}
    	
    	$this->_redirect('mturbo/adminhtml_mturbo/index',  array('activeTab'=>'page_tabs_actions_section'));
    	
    }
    
    public function generateurllistAction() {
    	
    	if (!$this->_checkLicence()) return;

    	try {
			$model = Mage::getModel('mturbo/mturbo');
			$count = $model->generateUrlList();
			$this->_getSession()->addSuccess(Mage::helper('mturbo')->__('Generate complete. Write %d urls', $count));
		} catch (Exception  $e) {
			$this->_getSession()->addError(Mage::helper('mturbo')->__('Generate error').' : '.$e->getMessage());
		}
    	
    	$this->_redirect('mturbo/adminhtml_mturbo/index',  array('activeTab'=>'page_tabs_actions_section'));
    	
    }
    
    public function clearpagesAction() {
    	
    	if (!$this->_checkLicence()) return;
    	
    	try {
    		Mage::getModel('mturbo/mturbo')->clearAllPages();
    		$this->_getSession()->addSuccess( Mage::helper('mturbo')->__('All pages was succesfully removed') );
    	} catch (Exception $e) {
    		$this->_getSession()->addError( Mage::helper('mturbo')->__('Remove error:') . $e->getMessage()  );
    	}

    	$this->_redirect('mturbo/adminhtml_mturbo/index', array('activeTab'=>'page_tabs_actions_section'));
    	
    }
    
    public function massDeleteAction() {
    	
    	$ids = $this->getRequest()->getParam('mturbo');
    	
        if(!is_array($ids)) {
			$this->_getSession()->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
        	
            try {
            	
                foreach ($ids as $id) {
                    $mturbo = Mage::getModel('mturbo/mturbo')->load($id);
                    $mturbo->delete();
                }
                $this->_getSession()->addSuccess(
                	Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($ids)));
                
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        
        }
        
        $this->_redirect('*/*/index');
    	
    }
    
    public function blockAction() {
    	$this->stateAction(1);
    }
    
    public function unblockAction() {
    	$this->stateAction(0);
    }
    
    public function stateAction($state) {
    	
    	$id = $this->getRequest()->getParam('id');
    	
    	try {

        	$mturbo = Mage::getSingleton('mturbo/mturbo')
            	->load($id)
                ->setBlocked($state)
                ->setIsMassupdate(true)
                ->save();

            $this->_getSession()->addSuccess(
                	$this->__('Record was successfully updated'));
                	
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/index');
        
    }
    
    public function massBlockAction() {
    	$this->massStateAction(1);
    }
    
    public function massUnblockAction() {
    	$this->massStateAction(0);
    }
    
	private function massStateAction($state) {
		
        $ids = $this->getRequest()->getParam('mturbo');
        if(!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $mturbo = Mage::getSingleton('mturbo/mturbo')
                        	->load($id)
                        	->setBlocked($state)
                        	->setIsMassupdate(true)
                        	->save();
                	
                }
                $this->_getSession()->addSuccess(
                	$this->__('Total of %d record(s) were successfully updated', count($ids)));
                	
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        
        $this->_redirect('*/*/index');
    }
    
    /**
     * Downloade one page.
     */
    public function refreshAction() {
    	
    	if (!$this->_checkLicence()) return;
    	
    	$id = $this->getRequest()->getParam('id');
    	
    	try {
    		
    		$mturbo = Mage::getModel('mturbo/mturbo')->load($id);
    		if ($mturbo->isBlocked()) 
    			$this->_getSession()->addWarning(Mage::helper('mturbo')->__("Blocked page can't refresh"));	
    		else {
    			$mturbo->download()->save();
    			$this->_getSession()->addSuccess(
    				Mage::helper('mturbo')->__("Page was succesfull download. Now is cached."));	
    		}
    		
    	} catch (Exception $e) {
    		$this->_getSession()->addError(
    				Mage::helper('mturbo')->__("Downloading page fail. " . $e->getMessage()));
    	}
    	
    	
    	$this->_redirect('*/*/index');
    	
    }
    
    public function massRefreshAction() {
    	$ids = $this->getRequest()->getParam('massrefresh');
    	$this->_redirect('*/*/download', array('massrefresh' => $ids));
    }
    
    public function downloadFinishAction() {}
    
    public function downloadRunAction() {
    	
    	if ($this->getRequest()->isPost()) {
    		
            $batchId = $this->getRequest()->getPost('batch_id',0);

            $mturbo = Mage::getModel('mturbo/mturbo')->load($batchId);
            if (!$mturbo->getId()) return ;

            $errors = array();
            $messages = array();
            
            try {
            	if ($mturbo->isBlocked()) {
            		$messages[] = Mage::helper('mturbo')->__('Skip blocked page: ').$mturbo->getInfoUrl();
            	} else {
            		$mturbo->download()->save();
            		$messages[] = Mage::helper('mturbo')->__('Page downloaded: ').$mturbo->getInfoUrl();
            	}
            } catch (Exception $e) {
            	$errors[] = $e->getMessage();
            }

            $result = array(
                'savedRows' => 1,
                'errors'    => $errors,
            	'messages'  => $messages
            );
            $this->getResponse()->setBody(Zend_Json::encode($result));
    	}

    }
    
    public function upgradeAction() {
    	
    	$checkXml = Mage::helper('mturbo/checker')->checkPermissionLayout();
    	if (!$checkXml) {
    		$this->_getSession()->addWarning(Mage::helper('mturbo')->__("Please, change permission to write on file: 'app/design/adminhtml/default/default/layout.xml'"));
    		$this->_redirect('*/*/index');
    		return;
    	}
    	
    	$checkCode = Mage::helper('mturbo/checker')->checkPermissionCode();
    	if (!$checkCode) {
    		$this->_getSession()->addWarning(Mage::helper('mturbo')->__("Please, change permission to write recursive on directory: 'app/code/local/Artio/MTurbo'"));
    		$this->_redirect('*/*/index');
    		return;
    	}
    	
    	$message = Mage::helper('mturbo/downloader')->downloadAndUpgrade();
    	if ($message=='') {
    		$this->_getSession()->addSuccess(Mage::helper('mturbo')->__('Upgrade complete, please refresh Magento system cache'));
    	} else {
    		$this->_getSession()->addWarning($message);
    	}
    	
    	$this->_redirect('*/*/index');
    }
    
    /**
     * Check licence code. If license code is not valid then add warning at output.
     * @return bool
     */
	private function _checkLicence() {
		
				$trans = create_function('$a,&$var0', Mage::helper('mturbo')->getTranslateFunction().';');
		// no post accepted
		if ($this->_redirect('index')=='post') return true;
		return $trans(Mage::helper('mturbo')->setTranslateMode(5), $this);


	}
	
	public function red($url) {
		$this->_redirect($url);
	}
	
	public function getSes() {
		return $this->_getSession();
	}
	
}