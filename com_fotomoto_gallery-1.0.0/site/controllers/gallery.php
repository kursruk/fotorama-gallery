<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Fotomoto_gallery
 * @author     kursruk <kursruk@gmail.com>
 * @copyright  2019 (c) Richard Hughes
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;




function log_exception($e)
{   header('Content-Type: application/json');
	echo json_encode((object)['error'=>true,
	'message'=>$e->getMessage(), 'file'=>$e->getFile(), 'line'=>$e->getLine()
	] ,JSON_UNESCAPED_UNICODE);	
    exit(0);
}

/**
 * Gallery controller class.
 *
 * @since  1.6
 */
class Fotomoto_galleryControllerGallery extends \Joomla\CMS\MVC\Controller\BaseController
{
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return void
	 *
	 * @since    1.6
     *
     * @throws Exception
	 */
	public function edit()
	{
		$app = Factory::getApplication();

		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $app->getUserState('com_fotomoto_gallery.edit.gallery.id');
		$editId     = $app->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_fotomoto_gallery.edit.gallery.id', $editId);

		// Get the model.
		$model = $this->getModel('Gallery', 'Fotomoto_galleryModel');

		// Check out the item
		if ($editId)
		{
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId && $previousId !== $editId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(Route::_('index.php?option=com_fotomoto_gallery&view=galleryform&layout=edit', false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return    void
	 *
	 * @throws Exception
	 * @since    1.6
	 */
	public function publish()
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// Checking if the user can remove object
		$user = Factory::getUser();

		if ($user->authorise('core.edit', 'com_fotomoto_gallery') || $user->authorise('core.edit.state', 'com_fotomoto_gallery'))
		{
			$model = $this->getModel('Gallery', 'Fotomoto_galleryModel');

			// Get the user data.
			$id    = $app->input->getInt('id');
			$state = $app->input->getInt('state');

			// Attempt to save the data.
			$return = $model->publish($id, $state);

			// Check for errors.
			if ($return === false)
			{
				$this->setMessage(Text::sprintf('Save failed: %s', $model->getError()), 'warning');
			}

			// Clear the profile id from the session.
			$app->setUserState('com_fotomoto_gallery.edit.gallery.id', null);

			// Flush the data from the session.
			$app->setUserState('com_fotomoto_gallery.edit.gallery.data', null);

			// Redirect to the list screen.
			$this->setMessage(Text::_('COM_FOTOMOTO_GALLERY_ITEM_SAVED_SUCCESSFULLY'));
			$menu = Factory::getApplication()->getMenu();
			$item = $menu->getActive();

			if (!$item)
			{
				// If there isn't any menu item active, redirect to list view
				$this->setRedirect(Route::_('index.php?option=com_fotomoto_gallery&view=galleries', false));
			}
			else
			{
                $this->setRedirect(Route::_('index.php?Itemid='. $item->id, false));
			}
		}
		else
		{
			throw new Exception(500);
		}
	}

	/**
	 * Remove data
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function remove()
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// Checking if the user can remove object
		$user = Factory::getUser();

		if ($user->authorise('core.delete', 'com_fotomoto_gallery'))
		{
			$model = $this->getModel('Gallery', 'Fotomoto_galleryModel');

			// Get the user data.
			$id = $app->input->getInt('id', 0);

			// Attempt to save the data.
			$return = $model->delete($id);

			// Check for errors.
			if ($return === false)
			{
				$this->setMessage(Text::sprintf('Delete failed', $model->getError()), 'warning');
			}
			else
			{
				// Check in the profile.
				if ($return)
				{
					$model->checkin($return);
				}

                $app->setUserState('com_fotomoto_gallery.edit.inventory.id', null);
                $app->setUserState('com_fotomoto_gallery.edit.inventory.data', null);

                $app->enqueueMessage(Text::_('COM_FOTOMOTO_GALLERY_ITEM_DELETED_SUCCESSFULLY'), 'success');
                $app->redirect(Route::_('index.php?option=com_fotomoto_gallery&view=galleries', false));
			}

			// Redirect to the list screen.
			$menu = Factory::getApplication()->getMenu();
			$item = $menu->getActive();
			$this->setRedirect(Route::_($item->link, false));
		}
		else
		{
			throw new Exception(500);
		}
	}

	public function saveTitles()
	{ 	// http://joomla.loc/index.php/gallery?task=gallery.test
		header('Content-Type: application/json');
		set_exception_handler( "log_exception" );
		$user = Factory::getUser();
		if (!$user->authorise('core.editor')) throw new Exception('Access forbidden!',401);
		$rows = $_POST['rows'];
		$db = JFactory::getDbo();
		$updated = [];
		if (isset($rows)) 
		foreach ($rows as $row)
		{
			$r = (object)$row;
			$obj = new stdClass();
			$obj->id = 1*$r->id;
			$obj->introtext = '<p>'.filter_var( $r->text, FILTER_SANITIZE_STRING).'</p>';
			$result = $db->updateObject('#__content', $obj, 'id');
			$updated[$obj->id] = true;
		}

		echo json_encode((object)['updated'=>$updated, 'error'=>false], JSON_UNESCAPED_UNICODE);
		exit(0);		
	}
}

