<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class JoomdleViewCoursemates extends JViewLegacy {
    function display($tpl = null) {

        $app        = JFactory::getApplication();
        $this->params = $app->getParams();

        $course_id = $app->input->get('course_id');
        if (!$course_id)
            $course_id = $this->params->get( 'course_id' );

        $this->course_info = JoomdleHelperContent::getCourseInfo($course_id);
        $this->users = JoomdleHelperContent::call_method('get_course_students', (int) $course_id, (int) 0);

        $document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname'] . ': ' . JText::_('COM_JOOMDLE_STUDENTS'));

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));


        parent::display($tpl);
    }
}
?>
