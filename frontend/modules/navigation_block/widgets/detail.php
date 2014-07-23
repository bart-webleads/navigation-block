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

        $templateFile = null;
        $otherTemplate = !empty($this->data['extra_label']) ? strtolower($this->data['extra_label']) : null;
        if ($otherTemplate) {
            $templateFile = FRONTEND_PATH . '/themes/' . FrontendModel::getModuleSetting('core', 'theme', 'default') . '/modules/navigation_block/layout/widgets/' . $otherTemplate . '.tpl';
            if (!is_file($templateFile)) {
                $templateFile = null;
            }
        }
        $this->loadTemplate($templateFile);
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        $idOrAlias = isset($this->data['id']) ? $this->data['id'] : null;
        if (is_numeric($idOrAlias)) {
            $items = FrontendNavigationBlockModel::getPagesByCategoryId($idOrAlias);
            $category = FrontendNavigationBlockModel::getCategory($idOrAlias);
        } else {
            $items = FrontendNavigationBlockModel::getPagesByCategoryAlias($idOrAlias);
            $category = FrontendNavigationBlockModel::getCategoryAlias($idOrAlias);
        }

        if (!empty($items)) {
            $pageId = Spoon::get('page')->getId();
            foreach ($items as &$item) {
                if ($pageId == $item['page_id']) {
                    $item['selected'] = true;
                }
            }
            $this->tpl->assign('widgetNavigationBlockDetail', array(
                'items' => $items,
                'category' => $category,
            ));
        }
    }
}
