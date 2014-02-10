<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */
class BackendNavigationBlockAdd extends BackendBaseActionAdd
{
	/**
	 * Execute the actions
	 */
	public function execute()
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
	protected function loadForm()
	{
		$this->frm = new BackendForm('add');

        $this->frm->addText('class');
        $this->frm->addDropdown('recursion_level', BackendNavigationBlockModel::getRecursionLevelsForDropdown());
        $this->frm->addDropdown(
            'page_id', BackendPagesModel::getPagesForDropdown(
                BL::getWorkingLanguage()
            ), null
        );
        $this->frm->addTextarea('description');
		$this->frm->addImage('image');
		$this->frm->addText('image_caption');

		// get categories
		$categories = BackendNavigationBlockModel::getCategories();
		$this->frm->addDropdown('category_id', $categories);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();
	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// validation
			$fields = $this->frm->getFields();

			$fields['page_id']->isFilled(BL::err('FieldIsRequired'));
			$fields['category_id']->isFilled(BL::err('FieldIsRequired'));

			if($this->frm->isCorrect()) {
				// build the item
				$item['language'] = BL::getWorkingLanguage();
				$item['page_id'] = $fields['page_id']->getValue();
                $item['class'] = $fields['class']->getValue();
                $item['description'] = $fields['description']->getValue();
                $item['recursion_level'] = $fields['recursion_level']->getValue();

				$item['sequence'] = BackendNavigationBlockModel::getMaximumSequence() + 1;
				$item['category_id'] = $this->frm->getField('category_id')->getValue();

				// insert it
				$item['id'] = BackendNavigationBlockModel::insert($item);

				BackendModel::triggerEvent($this->getModule(), 'after_add', $item);

				$this->redirect(
					BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']
				);
			}
		}
	}
}
