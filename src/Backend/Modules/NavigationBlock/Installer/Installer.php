<?php

namespace Backend\Modules\NavigationBlock\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Common\ModuleExtraType;

/**
 * Installer for the NavigationBlock module
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @author Wouter Verstuyf <info@webflow.be>
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('NavigationBlock');
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureFrontendExtras();
    }

    private function configureBackendNavigation(): void
    {
        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationNavigationBlockId = $this->setNavigation($navigationModulesId, $this->getModule());
        $this->setNavigation(
            $navigationNavigationBlockId,
            'Categories',
            'navigation_block/categories',
            ['navigation_block/add_category', 'navigation_block/edit_category']
        );
        $this->setNavigation(
            $navigationNavigationBlockId,
            $this->getModule(),
            'navigation_block/index',
            ['navigation_block/add','navigation_block/edit']
        );

    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        // Configure backend rights for entities
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Sequence');
        $this->setActionRights(1, $this->getModule(), 'Categories');
        $this->setActionRights(1, $this->getModule(), 'AddCategory');
        $this->setActionRights(1, $this->getModule(), 'EditCategory');
        $this->setActionRights(1, $this->getModule(), 'DeleteCategory');
        $this->setActionRights(1, $this->getModule(), 'SequenceCategories');

    }

    private function configureFrontendExtras(): void
    {
    }
}
