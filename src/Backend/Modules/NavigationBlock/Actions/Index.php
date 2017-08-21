<?php

namespace Backend\Modules\NavigationBlock\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;

/**
 * This is the index-action (default), it will display the overview of Navigation Block posts
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @author Wouter Verstuyf <info@webflow.be>
 */

class Index extends BackendBaseActionIndex
{
    private $categoryCount;

	public function execute(): void
	{
		parent::execute();
		$this->loadDataGrid();

		$this->parse();
		$this->display();
	}

	private function loadDataGrid(): void
	{
		$this->dataGrid = new BackendDataGridDatabase(
			BackendNavigationBlockModel::QUERY_DATAGRID_BROWSE,
            ['active', BL::getWorkingLanguage()]
		);

        // reform date
        $this->dataGrid->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[created_on]'],
            'created_on',
            true
        );

		// drag and drop sequencing
		$this->dataGrid->enableSequenceByDragAndDrop();

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('Edit'))
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

	protected function parse(): void
	{
		// parse datagrids
		$this->template->assign('dataGrid', (string) $this->dataGrid->getContent());
		$this->template->assign('hasCategories', (bool) $this->categoryCount);
	}
}
