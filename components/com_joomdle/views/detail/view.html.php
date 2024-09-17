<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/shop.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/system.php' );


/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewDetail extends JViewLegacy {
    function display($tpl = null) {

        $app                = JFactory::getApplication();
        $pathway = $app->getPathWay();

        $menus      = $app->getMenu();

        $menu  = $menus->getActive();

        $this->params = $app->getParams();

        $id = $this->params->get( 'course_id' );
        if (!$id)
            $id = $app->input->get('course_id', '', 'STRING');

        // Remove slug to get ID
        $id_parts = explode ('-', $id);
        $id = (int) $id_parts[0];

        if (!$id)
        {
            echo JText::_('COM_JOOMDLE_NO_COURSE_SELECTED');
            return;
        }

        $user = JFactory::getUser();
        $username = $user->username;
        
        $this->course_info = JoomdleHelperContent::getCourseInfo($id, $username);

        /* pathway */
        $unicodeslugs = JFactory::getConfig()->get('unicodeslugs');
        if ($unicodeslugs == 1)
        {
            $slug = JFilterOutput::stringURLUnicodeSlug($this->course_info['cat_name']);
        }
        else
        {
            $slug = JFilterOutput::stringURLSafe($this->course_info['cat_name']);
        }

        $cat_slug = $this->course_info['cat_id'] . "-". $slug;

        if(is_object($menu) && $menu->query['view'] != 'detail') {
                            $pathway->addItem($this->course_info['cat_name'], 'index.php?view=coursecategory&cat_id='.$cat_slug);
                            $pathway->addItem($this->course_info['fullname'], '');
                    }

        $document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname']);

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        parent::display($tpl);
    }
}
?>
