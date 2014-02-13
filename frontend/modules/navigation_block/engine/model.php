<?php

/**
 * NavigationBlock Model
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @copyright Copyright 2014 by Webleads http://www.webleads.nl
 */
class FrontendNavigationBlockModel
{
    /**
     * @param $categoryId
     * @return array
     */
    public static function getPageIdsByCategory($categoryId)
    {
        /** @var $database SpoonDatabase */
        $database = FrontendModel::getContainer()->get('database');
        $items = (array)$database->getRecords(
            'SELECT nb.page_id, nb.class, nb.description, nb.recursion_level
             FROM navigation_block AS nb
             WHERE nb.category_id = ? AND nb.language = ?
             ORDER BY nb.sequence ASC, nb.id DESC',
            array($categoryId, FRONTEND_LANGUAGE));

        if (empty($items)) {
            return array();
        }

        $result = array();
        foreach ($items as $item) {
            $info = FrontendNavigation::getPageInfo($item['page_id']);
            $item['title'] = $info['title'];
            $item['navigation_title'] = $info['navigation_title'];
            if (isset($info['tree_type']) && $info['tree_type'] == 'redirect') {
                $item['full_url'] = $info['redirect_url'];
            } else {
                $item['full_url'] = $info['full_url'];
            }
            if ($item['recursion_level'] !== 0) {
                $depth = $item['recursion_level'] == -1 ? null : $item['recursion_level'];
                if (($depth || $depth == null) && FrontendNavigation::getFirstChildId($item['page_id'])) {
                    $childrenHtml = (string)FrontendNavigation::getNavigationHtml('page', $item['page_id'], $depth, array(), '/modules/navigation_block/layout/templates/navigation.tpl');
                    $item['childrenHtml'] = $childrenHtml;
                }
            }
            $result[] = $item;
        }

        return $result;
    }

    /**
     * @param $categoryId
     * @return array
     */
    public static function getCategory($categoryId)
    {
        /** @var $database SpoonDatabase */
        $database = FrontendModel::getContainer()->get('database');
        $category = (array)$database->getRecord(
            'SELECT nbc.*
             FROM navigation_block_categories AS nbc
             WHERE nbc.id = ? AND nbc.language = ?',
            array($categoryId, FRONTEND_LANGUAGE));

        if (empty($category)) {
            return array();
        }

        $category['url'] = SpoonFilter::urlise($category['title']);

        return $category;
    }
}
