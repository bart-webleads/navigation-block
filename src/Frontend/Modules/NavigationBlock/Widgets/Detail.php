<?php

namespace Frontend\Modules\NavigationBlock\Widgets;

/**
 * NavigationBlock Widget
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @copyright Copyright 2014 by Webleads http://www.webleads.nl
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\NavigationBlock\Engine\Model as FrontendNavigationBlockModel;
use Frontend\Core\Engine\Model as FrontendModel;
use SpoonFilter;

/**
 * Class Detail
 * @package Frontend\Modules\NavigationBlock\Widget
 */
class Detail extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        $templateFile = null;
        $categoryAlias = !empty($this->data['extra_label']) ? SpoonFilter::toCamelCase($this->data['extra_label']) : (!empty($this->data['id']) ?  SpoonFilter::toCamelCase($this->data['id']) : null);
        if ($categoryAlias) {
            $templateFile = $this->getMyTemplate($categoryAlias);
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
            $pageId = $this->getContainer()->get('page')->getId();
            foreach ($items as &$item) {
                if ($pageId == $item['page_id']) {
                    $item['selected'] = true;
                }
            }
            $this->tpl->assign(
                'widgetNavigationBlockDetail',
                array(
                    'items' => $items,
                    'category' => $category,
                )
            );
        }
    }

    /**
     * @param $categoryAlias
     * @return null|string
     */
    private function getMyTemplate($categoryAlias)
    {
        $currentTheme = FrontendModel::getModuleSetting(
            'Core',
            'theme'
        );
        if ($currentTheme) {
            $templateFile = FRONTEND_PATH . '/Themes/' . $currentTheme . '/Modules/NavigationBlock/Layout/Widgets/' . $categoryAlias . '.tpl';
            if (is_file($templateFile)) {
                return $templateFile;
            }
        }
        return null;
    }
}
