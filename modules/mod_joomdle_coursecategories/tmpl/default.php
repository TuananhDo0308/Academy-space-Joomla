<?php 
/**
* @version		1.0
* @package		Joomdle - Mod Course Categories Listing
* @copyright	Qontori Pte Ltd
* @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
	<ul class="joomdlecategories<?php echo $moduleclass_sfx; ?>">
<?php
	$i = 0;
	if (is_array($cats))
	foreach ($cats as $id => $cat) {
		$id = $cat['id'];

		$url = JRoute::_("index.php?option=com_joomdle&view=coursecategory&cat_id=".$cat['id']."&Itemid=$joomdle_itemid"); 

		echo "<li><a href=\"".$url."\">".$cat['name']."</a></li>";
	}

?>
	</ul>
