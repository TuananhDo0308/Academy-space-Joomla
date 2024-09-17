<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

//namespace Joomla\Component\Joomdle\Site\View\Category;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\CategoryFeedView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
//use Joomla\Component\Content\Site\Helper\RouteHelper;

/*
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
*/

/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewCoursecategories extends JViewLegacy {
    function display($tpl = null) {

        $app        = JFactory::getApplication();
        $this->params = $app->getParams();

        $this->categories = JoomdleHelperContent::getCourseCategories();

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
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_COURSE_CATEGORIES'));
        }
    }

}
?>
