<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the Navigation Block module
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */
class NavigationBlockInstaller extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('navigation_block');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'navigation_block');

		$this->setActionRights(1, 'navigation_block', 'index');
		$this->setActionRights(1, 'navigation_block', 'add');
		$this->setActionRights(1, 'navigation_block', 'edit');
		$this->setActionRights(1, 'navigation_block', 'delete');
		$this->setActionRights(1, 'navigation_block', 'sequence');
		$this->setActionRights(1, 'navigation_block', 'categories');
		$this->setActionRights(1, 'navigation_block', 'add_category');
		$this->setActionRights(1, 'navigation_block', 'edit_category');
		$this->setActionRights(1, 'navigation_block', 'delete_category');
		$this->setActionRights(1, 'navigation_block', 'sequence_categories');

		// add extra's
		$subnameID = $this->insertExtra('navigation_block', 'block', 'NavigationBlock', null, null, 'N', 1000);
		$this->insertExtra('navigation_block', 'block', 'NavigationBlockDetail', 'detail', null, 'N', 1001);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationNavigationBlockId = $this->setNavigation($navigationModulesId, 'NavigationBlock');
		$this->setNavigation(
			$navigationNavigationBlockId, 'Categories', 'navigation_block/categories',
			array('navigation_block/add_category', 'navigation_block/edit_category')
		);
        $this->setNavigation(
            $navigationNavigationBlockId, 'NavigationBlock', 'navigation_block/index',
            array('navigation_block/add', 'navigation_block/edit')
        );
	}
}
