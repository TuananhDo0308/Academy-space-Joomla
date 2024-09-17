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
class JoomdleViewCoursegrades extends JViewLegacy {
    function display($tpl = null) {

        $app        = JFactory::getApplication();
        $this->params = $app->getParams();

        $this->course_id = $this->params->get( 'course_id' );
        if (!$this->course_id)
            $this->course_id = $app->input->get('course_id');
        $this->course_id = (int) $this->course_id;

        // Only for logged users
        $user = JFactory::getUser();
        $username = $user->username;
        if (!$username)
            return;

        if (!$this->course_id)
        {
            echo JText::_('COM_JOOMDLE_NO_COURSE_SELECTED');
            return;
        }

        $this->course_info = JoomdleHelperContent::getCourseInfo($this->course_id, $username);

        // user not enroled
        if (!$this->course_info['enroled'])
            return;

        $document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname'] . ': ' . JText::_('COM_JOOMDLE_GRADES'));

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        $this->gcats = JoomdleHelperContent::call_method ("get_grade_user_report", $this->course_id, $username);

        $tpl = "cats";
        parent::display($tpl);
    }
}
?>
