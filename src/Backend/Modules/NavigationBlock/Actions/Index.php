<?php

namespace Backend\Modules\NavigationBlock\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of Navigation Block posts
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;

use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;

class Index extends BackendBaseActionIndex
{
    private $categoryCount;

    /**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrid();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the dataGrid
	 */
	private function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(
			BackendNavigationBlockModel::QRY_DATAGRID_BROWSE,
            array('active', BL::getWorkingLanguage())
		);

        // reform date
		$this->dataGrid->setColumnFunction(
			array(new BackendDataGridFunctions(), 'getLongDate'),
			array('[created_on]'), 'created_on', true
		);

		// drag and drop sequencing
		$this->dataGrid->enableSequenceByDragAndDrop();

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			$this->dataGrid->addColumn(
				'edit', null, BL::lbl('Edit'),
				BackendModel::createURLForAction('edit') . '&amp;id=[id]',
				BL::lbl('Edit')
			);
			$this->dataGrid->setColumnURL(
				'page', BackendModel::createURLForAction('edit') . '&amp;id=[id]'
			);
		}

        BackendNavigationBlockModel::getCategories();

        $this->categoryCount = BackendNavigationBlockModel::getCategoryCount();
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		// parse the dataGrid if there are results
		$this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());
		$this->tpl->assign('hasCategories', (bool) $this->categoryCount);
	}
}
