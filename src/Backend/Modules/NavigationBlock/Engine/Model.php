<?php

namespace Backend\Modules\NavigationBlock\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Finder\Finder;
use Common\ModuleExtraType;
use Common\Uri as CommonUri;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the Navigation Block module
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 * @author Wouter Verstuyf <info@webflow.be>
 */
class Model
{
    const QUERY_DATAGRID_BROWSE =
        'SELECT q.id, p.title as page, c.title as category, UNIX_TIMESTAMP(q.created_on) AS created_on, q.sequence
         FROM navigation_block AS q
         INNER JOIN pages AS p ON (p.id=q.page_id AND p.language = q.language AND p.status = ?)
         INNER JOIN navigation_block_categories c ON c.id = q.category_id
         WHERE q.language = ?
         ORDER BY q.sequence';

    const QUERY_DATAGRID_BROWSE_CATEGORIES =
		'SELECT c.id, c.title, c.template, COUNT(i.id) AS num_items, c.sequence
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
	public static function delete(int $id): void
	{
		BackendModel::getContainer()->get('database')->delete('navigation_block', 'id = ?', [$id]);
	}

    /**
     * Checks if it is allowed to delete the a category
     *
     * @param int $id
     *
     * @return bool
     */
    public static function deleteCategoryAllowed(int $id): bool
    {
        return !(bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM navigation_block AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            [$id, BL::getWorkingLanguage()]
        );
    }

	/**
	 * Delete a specific category
	 *
	 * @param int $id
	 */
	public static function deleteCategory(int $id): void
	{
        $id = (int) $id;
        $db = BackendModel::getContainer()->get('database');

        // get item
        $item = self::getCategory($id);

        if (!empty($item)) {
            // delete extra
            $db->delete('modules_extras', 'id = ?', [$item['extra_id']]);

            // delete category
            $db->delete('navigation_block_categories', 'id = ?', [$id]);

            // update category for the posts that might be in this category
            $db->update('navigation_block', array('category_id' => null), 'category_id = ?', [$id]);
        }
    }

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function exists(int $id): bool
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM navigation_block AS i
			 WHERE i.id = ?
			 LIMIT 1',
			[(int) $id]
		);
	}

	/**
	 * Does the category exist?
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function existsCategory(int $id): bool
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM navigation_block_categories AS i
			 WHERE i.id = ? AND i.language = ?
			 LIMIT 1',
			[(int) $id, BL::getWorkingLanguage()]
        );
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function get(int $id): array
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			 FROM navigation_block AS i
			 WHERE i.id = ?',
			[(int) $id]
		);
	}

	/**
	 * Get all the categories
	 *
	 * @param bool[optional] $includeCount
	 * @return array
	 */
	public static function getCategories(bool $includeCount = false): array
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
				 [BL::getWorkingLanguage()]
            );
		}

		return (array) $db->getPairs(
			'SELECT i.id, i.title
			 FROM navigation_block_categories AS i
			 WHERE i.language = ?',
			 [BL::getWorkingLanguage()]
        );
	}

	/**
	 * Fetch a category
	 *
	 * @param int $id
	 * @return array
	 */
	public static function getCategory(int $id): array
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			 FROM navigation_block_categories AS i
			 WHERE i.id = ? AND i.language = ?',
			 [(int) $id, BL::getWorkingLanguage()]
        );
	}

    /**
     * @return int
     */
    public static function getCategoryCount(): int
    {
        return (int) BackendModel::getContainer()->get('database')->getVar(
            'SELECT count(*)
             FROM navigation_block_categories AS i
             WHERE i.language = ?',
             [BL::getWorkingLanguage()]
        );
    }

	/**
	 * Get the maximum sequence for a category
	 *
	 * @return int
	 */
	public static function getMaximumCategorySequence(): int
	{
		return (int) BackendModel::getContainer()->get('database')->getVar(
			'SELECT MAX(i.sequence)
			 FROM navigation_block_categories AS i
			 WHERE i.language = ?',
			 [BL::getWorkingLanguage()]
        );
	}

    /**
     * Get templates.
     *
     * @return array
     */
    public static function getTemplates()
    {
        $templates = [];
        $theme = BackendModel::get('fork.settings')->get('Core', 'theme', 'Fork');
        $finder = new Finder();
        $finder->name('*.html.twig');
        $finder->in(FRONTEND_MODULES_PATH . '/NavigationBlock/Layout/Widgets');

        // if there is a custom theme we should include the templates there also
        if ($theme !== 'Core') {
            $path = FRONTEND_PATH . '/Themes/' . $theme . '/Modules/NavigationBlock/Layout/Widgets';
            if (is_dir($path)) {
                $finder->in($path);
            }
        }

        foreach ($finder->files() as $file) {
            $templates[] = $file->getBasename();
        }

        $templates = array_unique($templates);

        return array_combine($templates, $templates);
    }


	/**
	 * Get the maximum Navigation Block sequence.
	 *
	 * @return int
	 */
	public static function getMaximumSequence(): int
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
	public static function insert(array $item): int
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
	public static function insertCategory(array $item): int
	{
        $db = BackendModel::getContainer()->get('database');

        // insert extra
        $item['extra_id'] = BackendModel::insertExtra(
            ModuleExtraType::widget(),
            'NavigationBlock',
            'Detail'
        );

        $item['id'] = $db->insert('navigation_block_categories', $item);

        // update extra (item id is now known)
        BackendModel::updateExtra(
            $item['extra_id'],
            'data',
            [
                'id' => $item['id'],
                'extra_label' => $item['title'],
                'language' => $item['language'],
                'edit_url' => BackendModel::createUrlForAction(
                    'EditCategory',
                    'NavigationBlock',
                    $item['language']
                ) . '&id=' . $item['id'],
            ]
        );

        return (int) $item['id'];
    }

	/**
	 * Updates an item
	 *
	 * @param array $item
	 */
	public static function update(array $item): void
	{
		$item['edited_on'] = BackendModel::getUTCDate();
		BackendModel::getContainer()->get('database')->update(
			'navigation_block',
            $item,
            'id = ?',
            [(int) $item['id']]
		);
	}

    /**
     * @param array $item
     * @return int
     */
    public static function updateCategory(array $item): void
	{
        $db = BackendModel::getContainer()->get('database');

        // update the category
        $db->update('navigation_block_categories', $item, 'id = ?', [(int) $item['id']]);

        // update extra
        BackendModel::updateExtra(
            $item['extra_id'],
            'data',
            [
                'id' => $item['id'],
                'extra_label' => $item['title'],
                'language' => $item['language'],
                'edit_url' => BackendModel::createUrlForAction('EditCategory') . '&id=' . $item['id'],
            ]
        );
    }

    public static function getRecursionLevelsForDropDown(): array
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
