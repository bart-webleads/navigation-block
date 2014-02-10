<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */
class BackendNavigationBlockEdit extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadData();
		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the item data
	 */
	protected function loadData()
	{
		$this->id = $this->getParameter('id', 'int', null);
		if($this->id == null || !BackendNavigationBlockModel::exists($this->id))
		{
			$this->redirect(
				BackendModel::createURLForAction('index') . '&error=non-existing'
			);
		}

		$this->record = BackendNavigationBlockModel::get($this->id);
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

        $this->frm->addDropdown('page_id', BackendPagesModel::getPagesForDropdown(), $this->record['page_id']);
        $this->frm->addTextarea('description', $this->record['description']);
        $this->frm->addDropdown('recursion_level', BackendNavigationBlockModel::getRecursionLevelsForDropdown(), $this->record['recursion_level']);

        $this->frm->addText('class', $this->record['class']);

		// get categories
		$categories = BackendNavigationBlockModel::getCategories();
		$this->frm->addDropdown('category_id', $categories, $this->record['category_id']);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();
		$this->tpl->assign('item', $this->record);
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
            $fields['recursion_level']->isFilled(BL::err('FieldIsRequired'));
			$fields['category_id']->isFilled(BL::err('FieldIsRequired'));

			if($this->frm->isCorrect())
			{
				$item['id'] = $this->id;
				$item['language'] = BL::getWorkingLanguage();

                $item['page_id'] = $fields['page_id']->getValue();
                $item['recursion_level'] = $fields['recursion_level']->getValue();
                $item['class'] = $fields['class']->getValue();
                $item['description'] = $fields['description']->getValue();

				$item['sequence'] = BackendNavigationBlockModel::getMaximumSequence() + 1;
				$item['category_id'] = $this->frm->getField('category_id')->getValue();

                if (isset($item['sequence'])) {
                    unset($item['sequence']);
                }

                BackendNavigationBlockModel::update($item);
				$item['id'] = $this->id;

				BackendModel::triggerEvent($this->getModule(), 'after_edit', $item);
				$this->redirect(
					BackendModel::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']
				);
			}
		}
	}
}
