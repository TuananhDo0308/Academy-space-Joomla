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
class JoomdleViewCourseevents extends JViewLegacy {
    function display($tpl = null) {

        $app        = JFactory::getApplication();
        $this->params = $app->getParams();

        $user = JFactory::getUser();
        $username = $user->username;

        $id = $this->params->get( 'course_id' );
        if (!$id)
            $id = $app->input->get('course_id');

        $id = (int) $id;

        if (!$id)
        {
            echo JText::_('COM_JOOMDLE_NO_COURSE_SELECTED');
            return;
        }

        $this->course_info = JoomdleHelperContent::getCourseInfo($id, $username);

        // user not enroled and no guest access
        if ((!$this->course_info['enroled']) && (!$this->course_info['guest']))
            return;

        $this->events = JoomdleHelperContent::getCourseEvents($id);

        $this->jump_url =  JoomdleHelperContent::getJumpURL ();

        $document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname'] . ': ' . JText::_('COM_JOOMDLE_EVENTS'));

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));


        parent::display($tpl);
    }
}
?>
