<?php

namespace Backend\Modules\NavigationBlock\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Form\Type\DeleteType;
use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;

/**
 * This is the edit category action, it will display a form to edit an existing category.
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @author Wouter Verstuyf <wouter@webflow.be>
 */
class EditCategory extends BackendBaseActionEdit
{
    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exists?
        if ($this->id !== null && BackendNavigationBlockModel::existsCategory($this->id)) {
            parent::execute();

            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();

            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createURLForAction('Categories') . '&error=non-existing');
        }
    }

    private function getData(): void
    {
        $this->record = BackendNavigationBlockModel::getCategory($this->id);
    }


    private function loadForm(): void
    {
        $templates = BackendNavigationBlockModel::getTemplates();

        // create form
        $this->form = new BackendForm('edit_category');

        $this->form->addText('title', $this->record['title']);
        $this->form->addDropdown('template', $templates, $this->record['template']);
    }


    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('item', $this->record);
    }

    /**
     * Validate the form
     *
     * @return  void
     */
    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            $fields = $this->form->getFields();

            // validate fields
            $fields['title']->isFilled(BL::err('TitleIsRequired'));
            $fields['template']->isFilled(BL::err('FieldIsRequired'));

            if ($this->form->isCorrect()) {
                // build item
                $item = [];
                $item['id'] = $this->id;
                $item['language'] = $this->record['language'];
                $item['title'] = $fields['title']->getValue();
                $item['template'] = $fields['template']->getValue();
                $item['created_on'] = BackendModel::getUTCDate();
                $item['extra_id'] = $this->record['extra_id'];

                // update the item
                BackendNavigationBlockModel::updateCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Categories') . '&report=edited-category&var=' .
                    rawurlencode($item['title']) . '&highlight=' . $item['id']
                );
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule(), 'action' => 'DeleteCategory']
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
