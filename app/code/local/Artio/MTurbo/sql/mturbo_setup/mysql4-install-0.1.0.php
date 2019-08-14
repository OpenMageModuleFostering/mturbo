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

	$notification = Mage::getModel('adminNotification/inbox');
	$notf = array( 'is_read'=>0, 'date_added'=>date('Y-m-d H:i:s'), 'severity'=>4, 'url'=>'http://www.artio.net/magento-extensions/m-turbo-accelerator' );

	try {

		$installer = $this;

		$installer->startSetup();

		$installer->run("

		CREATE TABLE IF NOT EXISTS `mturbo` (
  			`mturbo_id` int(11) unsigned NOT NULL auto_increment,
  			`url_rewrite_id` int(10) default NULL,
  			`store_id` smallint(5) unsigned default NULL,
  			`category_id` int(10) unsigned default NULL,
  			`product_id` int(10) unsigned default NULL,
  			`request_path` varchar(100) default NULL,
  			`last_refresh` datetime default NULL,
  			`blocked` tinyint(1) NOT NULL default '0',
  			PRIMARY KEY  (`mturbo_id`),
  			KEY `fk_url_rewrite` (`url_rewrite_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
		
		INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
			('default', 0, 'mturbo/previewcats', ''),
			('default', 0, 'mturbo/productcats', ''),
			('default', 0, 'mturbo/homepage', '1'),
			('default', 0, 'mturbo/multistoreview', '0'),
			('default', 0, 'mturbo/refreshsave', '1'),
			('default', 0, 'mturbo/automaticdownload', '0'),
			('default', 0, 'crontab/jobs/mturbo_mturbo/schedule/cron_expr', '0 3 * * *'),
			('default', 0, 'crontab/jobs/mturbo_mturbo/run/model', 'mturbo/observer::automaticDownload'),
			('default', 0, 'mturbo/firstconfig', '1');
		");
		
		$notf['title'] = Mage::helper('mturbo')->__('M-Turbo instalation succesfull. Please see into System/Turbo Cache Management');
		$notf['description'] = Mage::helper('mturbo')->__("Installation succesfull. Now will be your Magento faster than other.");
		
		$notification->parse(array($notf));

		$installer->endSetup();

	} catch (Exception $e) {
		
		$notf['title'] = Mage::helper('mturbo')->__('M-Turbo installation problem');
		$notf['description'] = $e->getMessage();
		$notification->parse(array($notf));
		
	}