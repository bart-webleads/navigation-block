<?php

namespace Backend\Modules\NavigationBlock\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;

/**
 * This is the add category-action, it will display a form to create a new category
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @author Wouter Verstuyf <wouter@webflow.be>
 */
class AddCategory extends BackendBaseActionAdd
{
    public function execute(): void
    {
        parent::execute();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        $templates = BackendNavigationBlockModel::getTemplates();

        // create form
        $this->form = new BackendForm('addCategory');
        $this->form->addText('title');
        $this->form->addDropdown('template', $templates);
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            $fields = $this->form->getFields();

            // validate fields
            $fields['title']->isFilled(BL::err('TitleIsRequired'));
            $fields['template']->isFilled(BL::err('FieldIsRequired'));

            // no errors?
            if ($this->form->isCorrect()) {
                $fields = $this->form->getFields();

                // build item
                $item = [];
                $item['title'] = $fields['title']->getValue();
                $item['template'] = $fields['template']->getValue();
                $item['language'] = BL::getWorkingLanguage();
                $item['sequence'] = BackendNavigationBlockModel::getMaximumCategorySequence() + 1;
                $item['edited_on'] = BackendModel::getUTCDate();
                $item['created_on'] = BackendModel::getUTCDate();

                // save the data
                $item['id'] = BackendNavigationBlockModel::insertCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('Categories') .
                    '&report=added-category&var=' .
                    urlencode($item['title']) .
                    '&highlight=' .
                    $item['id']
                );
            }
        }
    }
}
