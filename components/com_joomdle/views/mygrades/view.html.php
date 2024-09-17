<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');


/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewMygrades extends JViewLegacy {
    function display($tpl = null) {

        $app        = JFactory::getApplication();
        $this->params = $app->getParams();

        $user = JFactory::getUser();
        $id = $user->get('id');
        $username = $user->get('username');

        $layout = $this->params->get('layout');

        if ($layout == 'basic')
        {
            $this->tasks = JoomdleHelperContent::call_method ("get_my_grades", $username);
        }
        else
        {
            $this->tasks = JoomdleHelperContent::call_method ("get_my_grade_user_report", $username);
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
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_MY_GRADES'));
        }
    }

}
?>
