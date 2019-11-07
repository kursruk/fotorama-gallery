<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Fotomoto_gallery
 * @author     kursruk <kursruk@gmail.com>
 * @copyright  2019 (c) Richard Hughes
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Controller\BaseController;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Fotomoto_gallery', JPATH_COMPONENT);
JLoader::register('Fotomoto_galleryController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = BaseController::getInstance('Fotomoto_gallery');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
