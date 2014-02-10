<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the delete-action, it deletes an item
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */
class BackendNavigationBlockDelete extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendNavigationBlockModel::exists($this->id)) {
            parent::execute();
            $this->record = (array)BackendNavigationBlockModel::get($this->id);

            BackendNavigationBlockModel::delete($this->id);

            BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

            $this->redirect(
                BackendModel::createURLForAction('index') . '&report=deleted&var=' .
                urlencode($this->record['title'])
            );
        } else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
    }
}
