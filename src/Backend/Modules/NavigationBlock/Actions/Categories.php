<?php

namespace Backend\Modules\NavigationBlock\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;

/**
 * This is the index-action (default), it will display the overview of Navigation Block posts
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @author Wouter Verstuyf <info@webflow.be>
 */
class Categories extends BackendBaseActionIndex
{
    /**
     * The dataGrids
     *
     * @var array
     */
    protected $dataGrid;

	public function execute(): void
	{
		parent::execute();
		$this->loadDataGrid();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the dataGrid
	 */
	private function loadDataGrid(): void
	{
		$this->dataGrid = new BackendDataGridDatabase(
			BackendNavigationBlockModel::QUERY_DATAGRID_BROWSE_CATEGORIES,
			[BL::getWorkingLanguage()]
		);

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('EditCategory'))
		{
            $this->dataGrid->setColumnURL(
                'title', BackendModel::createURLForAction('edit_category') . '&amp;id=[id]'
            );
			$this->dataGrid->addColumn(
				'edit', null, BL::lbl('Edit'),
				BackendModel::createURLForAction('edit_category') . '&amp;id=[id]',
				BL::lbl('Edit')
			);
		}

		// sequence
		$this->dataGrid->enableSequenceByDragAndDrop();
		$this->dataGrid->setAttributes(array('data-action' => 'sequence_categories'));
	}

	/**
	 * Parse & display the page
	 */
	protected function parse(): void
	{
		$this->template->assign('dataGrid', (string) $this->dataGrid->getContent());
	}
}
