<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Fotomoto_gallery
 * @author     kursruk <kursruk@gmail.com>
 * @copyright  2019 (c) Richard Hughes
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */
defined('_JEXEC') or die;

JLoader::register('Fotomoto_galleryHelper', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_fotomoto_gallery' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'fotomoto_gallery.php');

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Class Fotomoto_galleryFrontendHelper
 *
 * @since  1.6
 */
class Fotomoto_galleryHelpersFotomoto_gallery
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_fotomoto_gallery/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_fotomoto_gallery/models/' . strtolower($name) . '.php';
			$model = BaseDatabaseModel::getInstance($name, 'Fotomoto_galleryModel');
		}

		return $model;
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

    /**
     * Gets the edit permission for an user
     *
     * @param   mixed  $item  The item
     *
     * @return  bool
     */
    public static function canUserEdit($item)
    {
        $permission = false;
        $user       = Factory::getUser();

        if ($user->authorise('core.edit', 'com_fotomoto_gallery'))
        {
            $permission = true;
        }
        else
        {
            if (isset($item->created_by))
            {
                if ($user->authorise('core.edit.own', 'com_fotomoto_gallery') && $item->created_by == $user->id)
                {
                    $permission = true;
                }
            }
            else
            {
                $permission = true;
            }
        }

        return $permission;
    }
}
