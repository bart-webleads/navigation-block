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
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @author Wouter Verstuyf <info@webflow.be>
 */
class Edit extends BackendBaseActionEdit
{
	public function execute(): void
	{
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist?
        if ($this->id !== 0 && BackendNavigationBlockModel::exists($this->id)) {
            parent::execute();

            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();

            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
	}

	protected function getData(): void
	{
	   $this->record = BackendNavigationBlockModel::get($this->id);
	}

	protected function loadForm(): void
	{
        $categories = BackendNavigationBlockModel::getCategories();

		// create form
		$this->form = new BackendForm('edit');

        $this->form->addDropdown('page_id', BackendPagesModel::getPagesForDropdown(), $this->record['page_id']);
        $this->form->addTextarea('description', $this->record['description']);
        $this->form->addDropdown('recursion_level', BackendNavigationBlockModel::getRecursionLevelsForDropdown(), $this->record['recursion_level']);
        $this->form->addText('class', $this->record['class']);
		$this->form->addDropdown('category_id', $categories, $this->record['category_id']);
	}

	protected function parse(): void
	{
		parent::parse();
		$this->template->assign('item', $this->record);
	}

	protected function validateForm(): void
	{
		if($this->form->isSubmitted())
		{
			$this->form->cleanupFields();

			// validation
			$fields = $this->form->getFields();

            $fields['page_id']->isFilled(BL::err('FieldIsRequired'));
            $fields['recursion_level']->isFilled(BL::err('FieldIsRequired'));
			$fields['category_id']->isFilled(BL::err('FieldIsRequired'));

			if($this->form->isCorrect())
			{
                $item = [];
				$item['id'] = $this->id;
				$item['language'] = BL::getWorkingLanguage();
                $item['page_id'] = $fields['page_id']->getValue();
                $item['recursion_level'] = $fields['recursion_level']->getValue();
                $item['class'] = $fields['class']->getValue();
                $item['description'] = $fields['description']->getValue();
				$item['sequence'] = BackendNavigationBlockModel::getMaximumSequence() + 1;
				$item['category_id'] = $this->form->getField('category_id')->getValue();

                if (isset($item['sequence'])) {
                    unset($item['sequence']);
                }

                BackendNavigationBlockModel::update($item);
				$item['id'] = $this->id;

				$this->redirect(
					BackendModel::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']
				);
			}
		}
	}

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule(), 'action' => 'Delete']
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
