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
class JoomdleViewCoursestats extends JViewLegacy {
    function display($tpl = null) {

        $app                = JFactory::getApplication();
        $pathway =$app->getPathWay();
        $menus      = $app->getMenu();
        $menu  = $menus->getActive();

        $this->params = $app->getParams();

        $id = $this->params->get( 'course_id' );
        if (!$id)
            $id = $app->input->get('course_id');

        $id = (int) $id;

        if (!$id)
        {
            echo JText::_('COM_JOOMDLE_NO_COURSE_SELECTED');
            return;
        }



        $this->course_info = JoomdleHelperContent::getCourseInfo($id);
        $this->student_no = JoomdleHelperContent::getCourseStudentsNo($id);
        $this->assignments = JoomdleHelperContent::getAssignmentSubmissions($id);
        $this->grades = JoomdleHelperContent::getAssignmentGrades($id);
        $this->daily_stats = JoomdleHelperContent::getCourseDailyStats($id);

        if(is_object($menu) && $menu->query['view'] != 'coursestats') {
                            $pathway->addItem($this->course_info['fullname'], '');
                    }

        $document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname'] . ': ' . JText::_('COM_JOOMDLE_STATS'));


        parent::display($tpl);
    }
}
?>
