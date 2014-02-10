<?php

/**
 * NavigationBlock Widget
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @copyright Copyright 2014 by Webleads http://www.webleads.nl
 */
class FrontendNavigationBlockWidgetDetail extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        $categoryId = isset($this->data['id']) ? (int)$this->data['id'] : null;
        if ($categoryId) {
            $items = FrontendNavigationBlockModel::getPageIdsByCategory($categoryId);
            $pageId = Spoon::get('page')->getId();
            foreach ($items as &$item) {
                if ($pageId == $item['page_id']) {
                    $item['selected'] = true;
                }
            }
            $category = FrontendNavigationBlockModel::getCategory($categoryId);
		    $this->tpl->assign('widgetNavigationBlockDetail', array(
                'items' => $items,
                'category' => $category,
            ));
        }
    }
}
