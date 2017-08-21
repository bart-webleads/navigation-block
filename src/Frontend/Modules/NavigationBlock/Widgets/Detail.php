<?php

namespace Frontend\Modules\NavigationBlock\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Theme as FrontendTheme;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\NavigationBlock\Engine\Model as FrontendNavigationBlockModel;
use Frontend\Core\Engine\Model as FrontendModel;
use SpoonFilter;

/**
 * NavigationBlock Widget
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @author Wouter Verstuyf <info@webflow.be>
 * @copyright Copyright 2014 by Webleads http://www.webleads.nl
 */
class Detail extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();

        $this->getData();
        $template = $this->assignTemplate();
        $this->loadTemplate($template);
        $this->parse();
    }

    private function assignTemplate(): string
    {
        $template = FrontendTheme::getPath(FRONTEND_MODULES_PATH . '/NavigationBlock/Layout/Widgets/Default.html.twig');

        if (!empty($this->category) && !empty($this->category['template'])) {
            try {
                $template = FrontendTheme::getPath(
                    FRONTEND_MODULES_PATH . '/NavigationBlock/Layout/Widgets/' . $this->category['template']
                );
            } catch (FrontendException $e) {
                // do nothing
            }
        }

        return $template;
    }

    private function getData(): void
    {
        $this->category = FrontendNavigationBlockModel::getCategory($this->data['id']);
    }

    private function parse(): void
    {
        $items = FrontendNavigationBlockModel::getPagesByCategoryId($this->data['id']);

        if (!empty($items)) {
            $pageId = $this->getContainer()->get('page')->getId();
            foreach ($items as &$item) {
                if ($pageId == $item['page_id']) {
                    $item['selected'] = true;
                }
            }
            $this->template->assign(
                'widgetNavigationBlockDetail',
                array(
                    'items' => $items,
                    'category' => $this->category,
                )
            );
        }
    }
}
