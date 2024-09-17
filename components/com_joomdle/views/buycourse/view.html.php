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


/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewBuycourse extends JViewLegacy {
    function display($tpl = null) {

        $app                = JFactory::getApplication();
        $pathway =$app->getPathWay();

        $menus      = $app->getMenu();
        $menu  = $menus->getActive();

        $this->params = $app->getParams();

        $logged_user = JFactory::getUser();
        $user_id = $logged_user->id;

         $id = $this->params->get( 'course_id' );
         if (!$id)
             $id = $app->input->get('course_id');
         $id = (int) $id;

        $itemid = JoomdleHelperContent::getMenuItem();
        $link = base64_encode ("index.php?option=com_joomdle&view=detail&course_id=$id&Itemid=$itemid");
        /* If user is logged, dont redirect */
        if (!$user_id)
        {
            $app->redirect(JURI::base ()."index.php?option=com_users&view=login&return=$link");
        }

        if (!$id)
        {
            echo JText::_('COM_JOOMDLE_NO_COURSE_SELECTED');
            return;
        }

        $this->course_info = JoomdleHelperContent::getCourseInfo($id);

        $this->moodle_url = $this->params->get( 'MOODLE_URL' );

        parent::display($tpl);
    }
}
?>
