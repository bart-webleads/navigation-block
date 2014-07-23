<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add category-action, it will display a form to create a new category
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */
class BackendNavigationBlockAddCategory extends BackendBaseActionAdd
{
    /**
     * Execute the action
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
    private function loadForm()
    {
        $this->frm = new BackendForm('addCategory');
        $this->frm->addText('title');
        $this->frm->addText('alias');
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();
            $this->validateFields();

            if ($this->frm->isCorrect()) {
                // build item
                $item['language'] = BL::getWorkingLanguage();
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['alias'] = $this->frm->getField('alias')->getValue();
                $item['sequence'] = BackendNavigationBlockModel::getMaximumCategorySequence() + 1;

                $item = $this->insertData($item);
                $this->redirectToOverview($item);
            }
        }
    }

    /**
     * @param $item
     * @return mixed
     */
    private function insertData($item)
    {
        $item['id'] = BackendNavigationBlockModel::insertCategory($item);
        BackendModel::triggerEvent($this->getModule(), 'after_add_category', array('item' => $item));
        return $item;
    }

    /**
     * @param $item
     */
    private function redirectToOverview($item)
    {
        $this->redirect(
            BackendModel::createURLForAction('categories') .
            '&report=added-category&var=' . urlencode($item['title']) .
            '&highlight=row-' . $item['id']
        );
    }

    /**
     *
     */
    private function validateFields()
    {
        $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
        if ($this->frm->getField('alias')->isFilled(BL::err('IsRequired'))) {
            $aliasValue = $this->frm->getField('alias')->getValue();
            if (ctype_digit($aliasValue)) {
                $this->frm->getField('alias')->addError(BL::err('AliasCannotBeNumeric'));
            }
        }
    }
}
