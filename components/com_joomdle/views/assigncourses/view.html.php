<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/parents.php' );

/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewAssigncourses extends JViewLegacy {
    function display($tpl = null) {

        $app        = JFactory::getApplication();
        $this->params = $app->getParams();

        $this->my_courses = JoomdleHelperParents::getUnassignedCourses();

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        parent::display($tpl);
    }
}
?>
