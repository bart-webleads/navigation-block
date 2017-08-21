<?php

namespace Backend\Modules\NavigationBlock\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Form\Type\DeleteType;
use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;

/**
 * This action will delete a category
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @author Wouter Verstuyf <info@webflow.be>
 */
class DeleteCategory extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            null,
            ['module' => $this->getModule(), 'action' => 'DeleteCategory']
        );
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createUrlForAction(
                'Categories',
                null,
                null,
                ['error' => 'something-went-wrong']
            ));

            return;
        }
        $deleteFormData = $deleteForm->getData();

        $this->id = $deleteFormData['id'];

        // does the item exist
        if ($this->id === 0 || !BackendNavigationBlockModel::existsCategory($this->id)) {
            $this->redirect(BackendModel::createUrlForAction('Categories', null, null, ['error' => 'non-existing']));

            return;
        }

        $this->record = (array) BackendNavigationBlockModel::getCategory($this->id);

        if (!BackendNavigationBlockModel::deleteCategoryAllowed($this->id)) {
            $this->redirect(BackendModel::createUrlForAction(
                'Categories',
                null,
                null,
                ['error' => 'delete-category-not-allowed', 'var' => $this->record['title']]
            ));

            return;
        }

        parent::execute();

        BackendNavigationBlockModel::deleteCategory($this->id);

        $this->redirect(BackendModel::createUrlForAction(
            'Categories',
            null,
            null,
            ['report' => 'deleted-category', 'var' => $this->record['title']]
        ));
	}
}
