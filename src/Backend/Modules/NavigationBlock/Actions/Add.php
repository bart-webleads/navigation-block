<?php

namespace Backend\Modules\NavigationBlock\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @author Wouter Verstuyf <info@webflow.be>
 */
class Add extends BackendBaseActionAdd
{
	public function execute(): void
	{
		parent::execute();

		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	protected function loadForm(): void
	{
        $categories = BackendNavigationBlockModel::getCategories();

		$this->form = new BackendForm('add');

        $this->form->addText('class');
        $this->form->addDropdown('recursion_level', BackendNavigationBlockModel::getRecursionLevelsForDropDown());
        $this->form->addDropdown(
            'page_id', BackendPagesModel::getPagesForDropdown(
                BL::getWorkingLanguage()
            ), null
        );
        $this->form->addTextarea('description');
		$this->form->addImage('image');
		$this->form->addText('image_caption');
		$this->form->addDropdown('category_id', $categories);
	}

	protected function parse(): void
	{
		parent::parse();
	}

	protected function validateForm(): void
	{
		if($this->form->isSubmitted())
		{
			$this->form->cleanupFields();

			// validation
			$fields = $this->form->getFields();

			$fields['page_id']->isFilled(BL::err('FieldIsRequired'));
			$fields['category_id']->isFilled(BL::err('FieldIsRequired'));

			if($this->form->isCorrect()) {
				// build the item
				$item['language'] = BL::getWorkingLanguage();
				$item['page_id'] = $fields['page_id']->getValue();
                $item['class'] = $fields['class']->getValue();
                $item['description'] = $fields['description']->getValue();
                $item['recursion_level'] = $fields['recursion_level']->getValue();

				$item['sequence'] = BackendNavigationBlockModel::getMaximumSequence() + 1;
				$item['category_id'] = $this->form->getField('category_id')->getValue();

				// insert it
				$item['id'] = BackendNavigationBlockModel::insert($item);

				$this->redirect(
					BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']
				);
			}
		}
	}
}
