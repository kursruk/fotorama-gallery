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

use Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  1.6
 */
class Fotomoto_galleryViewGalleryform extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

	protected $canSave;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app  = Factory::getApplication();
		$user = Factory::getUser();

		$this->state   = $this->get('State');
		$this->item    = $this->get('Item');
		$this->params  = $app->getParams('com_fotomoto_gallery');
		$this->canSave = $this->get('CanSave');
		

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		

		$this->_prepareDocument();

		parent::display($tpl);
	}

	function retResizedImage($imagePath, $new_width=300)
	{   $fileName = pathinfo($imagePath, PATHINFO_FILENAME);
		$fullPath = pathinfo($imagePath, PATHINFO_DIRNAME) . "/" . $fileName . "_w${new_width}.jpg";

		if (file_exists($fullPath)) {
			return $fullPath;
		}

		$image = imagecreatefromjpeg($imagePath);
		if ($image == false) {
			return null;
		}

		$width = imagesx($image);
		$height = imagesy($image);

		$imageResized = imagecreatetruecolor($width, $height);
		if ($imageResized == false) {
			return null;
		}

		// $image = imagecreatetruecolor($width, $height);
		$imageResized = imagescale($image, $new_width, -1, IMG_BICUBIC);
		touch($fullPath);
		$write = imagejpeg($imageResized, $fullPath);
		if (!$write) {
			imagedestroy($imageResized);
			return null;
		}
		imagedestroy($imageResized);
		return $fullPath;
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_FOTOMOTO_GALLERY_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
      
		$fotomoto =  $this->params->get('fotomotoscripturl');
		if (trim($fotomoto!==''))  $this->document->addScript($fotomoto.'?api=true');
		else throw new Exception('Set Fotomoto URL in the component settings',122);

		$base_path = substr(JPATH_COMPONENT, strlen(JPATH_BASE));
		$this->document->addScript("$base_path/js/com_fotomoto_gallery.js?v5");

		$category = $this->params->get('categories');
		if (trim($category=='')) 
		throw new Exception('Set Fotomoto categorie in the component settings',126);

		$this->document->addStyleSheet("$base_path/js/com_fotomoto_gallery.css?v5");

		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
			->select( ['a.id','a.title','a.introtext', 'a.images', 'a.catid'] )
			->from($db->quoteName('#__content', 'a'))
			// ->join('INNER', $db->quoteName('#__users', 'b') . ' ON ' . $db->quoteName('a.created_by') . ' = ' . $db->quoteName('b.id'))
			->where($db->quoteName('a.catid') . ' = ' . $db->quote($category))
			->order($db->quoteName('a.created') . ' DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$this->items = $db->loadObjectList();
		foreach($this->items as $img)
		{ 	$im = json_decode( $img->images );
			$imf = $im->image_intro;
			if ($imf=='') $imf = $im->image_fulltext;
			if ($imf!=='')
			{	$img->src = $imf;
				$img->resized = $this->retResizedImage($imf);
				$img->height = getimagesize($img->resized)[1];
			}
		}

		usort($this->items, function($a, $b) { 
			return $a->height-$b->height;
		});
	}
}
