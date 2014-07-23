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
    public static function getPagesByCategoryId($categoryId)
    {
        /** @var $database SpoonDatabase */
        $database = FrontendModel::getContainer()->get('database');
        $items = (array)$database->getRecords(
            'SELECT nb.page_id, nb.class, nb.description, nb.recursion_level
             FROM navigation_block AS nb
             INNER JOIN pages AS p ON (p.id=nb.page_id AND p.language = nb.language AND p.status = ?)
             WHERE nb.category_id = ? AND nb.language = ?
             ORDER BY nb.sequence ASC, nb.id DESC',
            array('active', $categoryId, FRONTEND_LANGUAGE));

        if (empty($items)) {
            return array();
        }

        $result = self::getSubPagesAndInfo($items);

        return $result;
    }

    /**
     * @param $alias
     * @return array
     */
    public static function getPagesByCategoryAlias($alias)
    {
        /** @var $database SpoonDatabase */
        $database = FrontendModel::getContainer()->get('database');
        $items = (array)$database->getRecords(
            'SELECT nb.page_id, nb.class, nb.description, nb.recursion_level
             FROM navigation_block AS nb
             INNER JOIN navigation_block_categories AS nbc ON nbc.id = nb.category_id
             INNER JOIN pages AS p ON (p.id=nb.page_id AND p.language = nb.language AND p.status = ?)
             WHERE nbc.alias = ? AND nb.language = ?
             ORDER BY nb.sequence ASC, nb.id DESC',
            array('active', $alias, FRONTEND_LANGUAGE));

        if (empty($items)) {
            return array();
        }

        $result = self::getSubPagesAndInfo($items);

        return $result;
    }

    /**
     * @param $items
     * @return array
     */
    private static function getSubPagesAndInfo($items)
    {
        $result = array();
        foreach ($items as $item) {
            $info = FrontendNavigation::getPageInfo($item['page_id']);
            $item['title'] = $info['title'];
            $item['navigation_title'] = $info['navigation_title'];
            if (isset($info['tree_type'], $info['redirect_url']) && $info['tree_type'] == 'redirect') {
                $item['full_url'] = $info['redirect_url'];
            } else {
                $item['full_url'] = $info['full_url'];
            }
            if ($item['recursion_level'] !== 0) {
                $depth = $item['recursion_level'] == -1 ? null : $item['recursion_level'];
                if (($depth || $depth == null) && FrontendNavigation::getFirstChildId($item['page_id'])) {
                    $childrenHtml = (string) FrontendNavigation::getNavigationHtml('page', $item['page_id'], $depth, array(), '/modules/navigation_block/layout/templates/navigation.tpl');
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

    /**
     * @param $alias
     * @return array
     */
    public static function getCategoryAlias($alias)
    {
        /** @var $database SpoonDatabase */
        $database = FrontendModel::getContainer()->get('database');
        $category = (array)$database->getRecord(
            'SELECT nbc.*
             FROM navigation_block_categories AS nbc
             WHERE nbc.alias = ? AND nbc.language = ?',
            array($alias, FRONTEND_LANGUAGE));

        if (empty($category)) {
            return array();
        }

        $category['url'] = SpoonFilter::urlise($category['title']);

        return $category;
    }

}
