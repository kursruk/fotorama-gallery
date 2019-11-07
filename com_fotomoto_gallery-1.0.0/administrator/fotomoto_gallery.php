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

use \Joomla\CMS\MVC\Controller\BaseController;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_fotomoto_gallery'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Fotomoto_gallery', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('Fotomoto_galleryHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'fotomoto_gallery.php');

$controller = BaseController::getInstance('Fotomoto_gallery');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
