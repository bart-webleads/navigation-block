<?php

namespace Backend\Modules\NavigationBlock\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a category
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;

use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;

class DeleteCategory extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id == null || !BackendNavigationBlockModel::existsCategory($this->id))
		{
			$this->redirect(
				BackendModel::createURLForAction('categories') . '&error=non-existing'
			);
		}

		// fetch the category
		$this->record = (array) BackendNavigationBlockModel::getCategory($this->id);

		// delete item
		BackendNavigationBlockModel::deleteCategory($this->id);
		BackendModel::triggerEvent($this->getModule(), 'after_delete_category', array('item' => $this->record));

		// category was deleted, so redirect
		$this->redirect(
			BackendModel::createURLForAction('categories') . '&report=deleted-category&var=' .
			urlencode($this->record['title'])
		);
	}
}
