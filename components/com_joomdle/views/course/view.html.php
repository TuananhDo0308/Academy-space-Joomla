<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewCourse extends JViewLegacy {
    function display($tpl = null) {

        $app                = JFactory::getApplication();
        $pathway =$app->getPathWay();
        $app        = JFactory::getApplication();
        $menus      = $app->getMenu();
        $menu  = $menus->getActive();

        $this->params = $app->getParams();
        $show_contents_link = $this->params->get( 'show_contents_link' );
        if (!$show_contents_link)
        {
            echo JText::_('COM_JOOMDLE_COURSE_CONTENTS_NOT_ENABLED');
            return;
        }

        $id = $app->input->get('course_id');
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
        $this->is_enroled = $this->course_info['enroled'];

        if ($this->is_enroled)
            $this->mods = JoomdleHelperContent::call_method ( 'get_course_mods', (int) $id, $username);
        else
            $this->mods = JoomdleHelperContent::call_method ( 'get_course_mods', (int) $id, '');

        /* pathway */
        $cat_slug = $this->course_info['cat_id']."-".$this->course_info['cat_name'];
        $course_slug = $this->course_info['remoteid']."-".$this->course_info['fullname'];

        if(is_object($menu) && $menu->query['view'] != 'course') {
                        $pathway->addItem($this->course_info['cat_name'], 'index.php?view=coursecategory&cat_id='.$cat_slug);
                        $pathway->addItem($this->course_info['fullname'], 'index.php?view=detail&cat_id='.$cat_slug.'&course_id='.$course_slug);
                        $pathway->addItem(JText::_('COM_JOOMDLE_COURSE_CONTENTS'), '');
                }

        $document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname']);

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        parent::display($tpl);
    }
}
?>
