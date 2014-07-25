<?php

namespace Backend\Modules\NavigationBlock\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit category action, it will display a form to edit an existing category.
 *
 * @property mixed record
 * @author Bart Lagerweij <bart@webleads.nl>
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;

class EditCategory extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->getData();
        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->id = $this->getParameter('id', 'int');
        if ($this->id == null || !BackendNavigationBlockModel::existsCategory($this->id)) {
            $this->redirect(
                BackendModel::createURLForAction('categories') . '&error=non-existing'
            );
        }

        $this->record = BackendNavigationBlockModel::getCategory($this->id);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('editCategory');
        $this->frm->addText('title', $this->record['title']);
        $this->frm->addText('alias', $this->record['alias']);
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('item', $this->record);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();
            $this->validateFields();

            if ($this->frm->isCorrect()) {
                // build item
                $item['id'] = $this->id;
                $item['language'] = $this->record['language'];
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['alias'] = $this->frm->getField('alias')->getValue();
                $item['extra_id'] = $this->record['extra_id'];

                $item = $this->saveData($item);
                $this->redirectToOverview($item);
            }
        }
    }

    /**
     * @param $item
     * @return mixed
     */
    private function saveData($item)
    {
        if (isset($item['sequence'])) {
            unset($item['sequence']);
        }
        BackendNavigationBlockModel::updateCategory($item);
        BackendModel::triggerEvent($this->getModule(), 'after_edit_category', array('item' => $item));
        return $item;
    }

    /**
     * @param $item
     */
    private function redirectToOverview($item)
    {
        $this->redirect(
            BackendModel::createURLForAction('categories') . '&report=edited-category&var=' .
            urlencode($item['title']) . '&highlight=row-' . $item['id']
        );
    }

    /**
     *
     */
    private function validateFields()
    {
        $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
        if ($this->frm->getField('alias')->isFilled(BL::err('IsRequired'))) {
            $aliasValue = $this->frm->getField('alias')->getValue();
            $this->frm->getField('alias')->isValidAgainstRegexp('/^[-_a-z0-9]+$/', BL::err('AliasInvalid'));
            if (ctype_digit($aliasValue)) {
                $this->frm->getField('alias')->addError(BL::err('AliasCannotBeNumeric'));
            }
        }
    }
}
