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

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;

/*

use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_fotomoto_gallery', JPATH_SITE);
$doc = Factory::getDocument();
$doc->addScript(Uri::base() . '/media/com_fotomoto_gallery/js/form.js');

$user    = Factory::getUser();
$canEdit = Fotomoto_galleryHelpersFotomoto_gallery::canUserEdit($this->item, $user);

*/

// $heading = $this->params->get('show_page_heading',1);
echo  '<h1 class="item_title">'.JFactory::getApplication()->getMenu()->getActive()->title.'</h1>';
echo  '<div class="item_title">'.$this->params->get('fotomotodescription').'</div>';

$user = Factory::getUser();
$canEdit = $user->authorise('core.editor');
$htmedit = '';
$isedit = '';
if ($canEdit)
{	?>
	<button class="btn btn-primary b-save" type="button">Save</button>
	<?php
	$htmedit = ' contenteditable="true"';
	$isedit = ' w-is-edit';
}

?>

<div class="img-listbox<?=$isedit?>">
<?php
    foreach ($this->items as $img)
    {  	?>			
				<div class="img-tumbnail"> 
					<div class="thumbnail">
						<?php							
							if ($img->src!=='')
							{					
								echo '<img  data-src="'.$img->src.'" id="fimgx'.$img->id
								.'" src="'.$img->resized.'" alt="'
								.htmlspecialchars($img->title).'" />';							
								echo '<div data-id="'.$img->id.'" class="w-title" '.$htmedit.'>'.$img->introtext.'</div>';
	
							}
						?>
					</div>
				</div>
			<?php
    }
?>
	<div style="clear:both"></div>
</div>




