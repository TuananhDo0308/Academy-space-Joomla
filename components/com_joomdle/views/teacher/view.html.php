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
class JoomdleViewTeacher extends JViewLegacy {
    function display($tpl = null) {

        $app        = JFactory::getApplication();
        $this->params = $app->getParams();

        $username = $app->input->get('username');
        if (!$username)
            $username = $this->params->get( 'username');

        if (!$username)
        {
            echo JText::_('COM_JOOMDLE_NO_USER_SELECTED');
            return;
        }


        $this->courses = JoomdleHelperContent::call_method('teacher_courses', $username);
        $this->username = $username;
        $this->user_info = JoomdleHelperMappings::get_user_info_for_joomla ($this->username);

        $document = JFactory::getDocument();
        $document->setTitle($this->user_info['name']);

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));


        parent::display($tpl);
    }
}
?>
