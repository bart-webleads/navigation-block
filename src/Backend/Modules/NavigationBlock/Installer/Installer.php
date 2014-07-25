<?php

namespace Backend\Modules\NavigationBlock\Installer;

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

use Backend\Core\Installer\ModuleInstaller;

class Installer extends ModuleInstaller
{
    public function install()
    {
        // import the sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // install the module in the database
        $this->addModule('NavigationBlock');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, 'NavigationBlock');

        $this->setActionRights(1, 'NavigationBlock', 'Index');
        $this->setActionRights(1, 'NavigationBlock', 'Add');
        $this->setActionRights(1, 'NavigationBlock', 'Edit');
        $this->setActionRights(1, 'NavigationBlock', 'Delete');
        $this->setActionRights(1, 'NavigationBlock', 'Sequence');
        $this->setActionRights(1, 'NavigationBlock', 'Categories');
        $this->setActionRights(1, 'NavigationBlock', 'AddCategory');
        $this->setActionRights(1, 'NavigationBlock', 'EditCategory');
        $this->setActionRights(1, 'NavigationBlock', 'DeleteCategory');
        $this->setActionRights(1, 'NavigationBlock', 'SequenceCategories');

        // add extra's
        $subnameID = $this->insertExtra('NavigationBlock', 'block', 'NavigationBlock', null, null, 'N', 1000);
        $this->insertExtra('NavigationBlock', 'block', 'NavigationBlockDetail', 'Detail', null, 'N', 1001);

        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationNavigationBlockId = $this->setNavigation($navigationModulesId, 'NavigationBlock');
        $this->setNavigation(
            $navigationNavigationBlockId,
            'Categories',
            'navigation_block/categories',
            array('navigation_block/add_category', 'navigation_block/edit_category')
        );
        $this->setNavigation(
            $navigationNavigationBlockId,
            'NavigationBlock',
            'navigation_block/index',
            array(
                'navigation_block/add',
                'navigation_block/edit'
            )
        );
    }
}
