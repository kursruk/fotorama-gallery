<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Fotomoto_gallery
 * @author     kursruk <kursruk@gmail.com>
 * @copyright  2019 (c) Richard Hughes
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Galleries list controller class.
 *
 * @since  1.6
 */
class Fotomoto_galleryControllerGalleries extends Fotomoto_galleryController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Galleries', $prefix = 'Fotomoto_galleryModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}
