<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the Navigation Block module
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */
class BackendNavigationBlockModel
{
    /**
     *
     */

    const QRY_DATAGRID_BROWSE =
        'SELECT q.id, p.title as page, c.title as category, UNIX_TIMESTAMP(q.created_on) AS created_on, q.sequence
         FROM navigation_block AS q
         INNER JOIN pages AS p ON (p.id=q.page_id AND p.language = q.language AND p.status = ?)
         INNER JOIN navigation_block_categories c ON c.id = q.category_id
         WHERE q.language = ?
         ORDER BY q.sequence';

    /**
     *
     */
    const QRY_DATAGRID_BROWSE_CATEGORIES =
		'SELECT c.id, c.title, COUNT(i.id) AS num_items, c.id as identifier, c.sequence
		 FROM navigation_block_categories AS c
		 LEFT OUTER JOIN navigation_block AS i ON c.id = i.category_id AND i.language = c.language
		 WHERE c.language = ?
		 GROUP BY c.id
		 ORDER BY c.sequence ASC';

	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function delete($id)
	{
		BackendModel::getContainer()->get('database')->delete('navigation_block', 'id = ?', (int) $id);
	}

	/**
	 * Delete a specific category
	 *
	 * @param int $id
	 */
	public static function deleteCategory($id)
	{
        $db = BackendModel::getContainer()->get('database');
        $item = self::getCategory($id);

        // build extra
        $extra = array('id' => $item['extra_id'],
            'module' => 'navigation_block',
            'type' => 'widget',
            'action' => 'detail');

        // delete extra
        $db->delete('modules_extras', 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

        if (!empty($item)) {
            $db->delete('navigation_block_categories', 'id = ?', array((int)$id));
            $db->update('navigation_block', array('category_id' => null), 'category_id = ?', array((int)$id));
        }
    }

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM navigation_block AS i
			 WHERE i.id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}

	/**
	 * Does the category exist?
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function existsCategory($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM navigation_block_categories AS i
			 WHERE i.id = ? AND i.language = ?
			 LIMIT 1',
			array((int) $id, BL::getWorkingLanguage()));
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			 FROM navigation_block AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get all the categories
	 *
	 * @param bool[optional] $includeCount
	 * @return array
	 */
	public static function getCategories($includeCount = false)
	{
		$db = BackendModel::getContainer()->get('database');

		if($includeCount)
		{
			return (array) $db->getPairs(
				'SELECT i.id, CONCAT(i.title, " (",  COUNT(p.category_id) ,")") AS title
				 FROM navigation_block_categories AS i
				 LEFT OUTER JOIN navigation_block AS p ON i.id = p.category_id AND i.language = p.language
				 WHERE i.language = ?
				 GROUP BY i.id',
				 array(BL::getWorkingLanguage()));
		}

		return (array) $db->getPairs(
			'SELECT i.id, i.title
			 FROM navigation_block_categories AS i
			 WHERE i.language = ?',
			 array(BL::getWorkingLanguage()));
	}

	/**
	 * Fetch a category
	 *
	 * @param int $id
	 * @return array
	 */
	public static function getCategory($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			 FROM navigation_block_categories AS i
			 WHERE i.id = ? AND i.language = ?',
			 array((int) $id, BL::getWorkingLanguage()));
	}

	/**
	 * Get the maximum sequence for a category
	 *
	 * @return int
	 */
	public static function getMaximumCategorySequence()
	{
		return (int) BackendModel::getContainer()->get('database')->getVar(
			'SELECT MAX(i.sequence)
			 FROM navigation_block_categories AS i
			 WHERE i.language = ?',
			 array(BL::getWorkingLanguage()));
	}

    /**
     * @return int
     */
    public static function getCategoryCount()
	{
		return (int) BackendModel::getContainer()->get('database')->getVar(
			'SELECT count(*)
			 FROM navigation_block_categories AS i
			 WHERE i.language = ?',
			 array(BL::getWorkingLanguage()));
	}

	/**
	 * Get the maximum Navigation Block sequence.
	 *
	 * @return int
	 */
	public static function getMaximumSequence()
	{
		return (int) BackendModel::getContainer()->get('database')->getVar(
			'SELECT MAX(i.sequence)
			 FROM navigation_block AS i'
		);
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $item
	 * @return int
	 */
	public static function insert(array $item)
	{
		$item['created_on'] = BackendModel::getUTCDate();
		$item['edited_on'] = BackendModel::getUTCDate();

		return (int) BackendModel::getContainer()->get('database')->insert('navigation_block', $item);
	}

	/**
	 * Insert a category in the database
	 *
	 * @param array $item
	 * @return int
	 */
	public static function insertCategory(array $item)
	{
		$item['created_on'] = BackendModel::getUTCDate();
		$item['edited_on'] = BackendModel::getUTCDate();

        $db = BackendModel::getContainer()->get('database');

        // build extra
        $extra = array(
            'module' => 'navigation_block',
            'type' => 'widget',
            'label' => 'NavigationBlock',
            'action' => 'detail',
            'data' => null,
            'hidden' => 'N',
            'sequence' => $db->getVar(
                'SELECT MAX(i.sequence) + 1
                 FROM modules_extras AS i
                 WHERE i.module = ?',
                array('navigation_block')
            )
        );

        if (is_null($extra['sequence'])) $extra['sequence'] = $db->getVar(
            'SELECT CEILING(MAX(i.sequence) / 1000) * 1000
             FROM modules_extras AS i'
        );

        // insert extra
        $item['extra_id'] = $db->insert('modules_extras', $extra);
        $extra['id'] = $item['extra_id'];

        $item['id'] = $db->insert('navigation_block_categories', $item);

        // update extra (item id is now known)
        $extra['data'] = serialize(
            array(
                'id' => $item['id'],
                'extra_label' => $item['title'],
            )
        );
        $db->update(
            'modules_extras',
            $extra,
            'id = ? AND module = ? AND type = ? AND action = ?',
            array($extra['id'], $extra['module'], $extra['type'], $extra['action'])
        );

        return $item['id'];
    }

	/**
	 * Updates an item
	 *
	 * @param array $item
	 */
	public static function update(array $item)
	{
		$item['edited_on'] = BackendModel::getUTCDate();
		BackendModel::getContainer()->get('database')->update(
			'navigation_block', $item, 'id = ?', (int) $item['id']
		);
	}

    /**
     * @param array $item
     * @return int
     */
    public static function updateCategory(array $item)
	{
        /* @var $db SpoonDatabase */
        $db = BackendModel::getContainer()->get('database');

        $item['edited_on'] = BackendModel::getUTCDate();

        // build extra
        $extra = array(
            'id' => $item['extra_id'],
            'module' => 'navigation_block',
            'type' => 'widget',
            'label' => 'NavigationBlock',
            'action' => 'detail',
            'data' => serialize(
                array(
                    'id' => $item['id'],
                    'extra_label' => $item['title'],
                )
            ),
            'hidden' => 'N');

        // update extra
        $db->update('modules_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

        $affectedRows = $db->update('navigation_block_categories', $item, 'id = ?', array($item['id']));

        return $affectedRows;
    }

    /**
     * @return array
     */
    public static function getRecursionLevelsForDropdown()
    {
        $options = array(
            0 => ucfirst(BL::getLabel('none')),
        );

        for ($n = 1; $n < 10; $n++) {
            $options[$n] = $n;
        }

        $options += array(
            -1 => ucfirst(BL::getLabel('infinite')),
        );


        return $options;
    }
}
