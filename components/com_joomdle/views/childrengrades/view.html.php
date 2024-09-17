<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');


/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewChildrengrades extends JViewLegacy {
    function display($tpl = null) {

        $app        = JFactory::getApplication();
        $this->params = $app->getParams();

        $user = JFactory::getUser();
        $username = $app->input->get('child_username');

        $layout = $this->params->get('layout');

        $user = JFactory::getUser ();
        $username = $user->username;

        if ($layout == 'basic')
        {
            $this->tasks = JoomdleHelperContent::call_method ("get_children_grades", $username);
        }
        else
        {
            $this->tasks = JoomdleHelperContent::call_method ("get_children_grade_user_report", $username);
            $tpl = 'cats';
        }

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        $this->_prepareDocument();


        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $title  = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu)
        {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_GRADES') . ": " . $this->child->name);
        }
    }

}
?>
