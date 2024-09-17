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
class JoomdleViewCoursecategory extends JViewLegacy {
    function display($tpl = null) {

        $app                = JFactory::getApplication();
        $pathway = $app->getPathWay();
        $menus      = $app->getMenu();
        $menu  = $menus->getActive();

        $this->params = $app->getParams();

        $id = $app->input->get('cat_id','', 'STRING');
        if (!$id)
            $id = $this->params->get( 'cat_id' );
        
        // Remove slug to get ID
        $id_parts = explode ('-', $id);
        $id = (int) $id_parts[0];

        if (!$id)
        {
            echo JText::_('COM_JOOMDLE_NO_CATEGORY_SELECTED');
            return;
        }

        $this->cat_id = $id;

        $this->cat_name = JoomdleHelperContent::call_method ('get_cat_name', $id);

        $user = JFactory::getUser();
        $username = $user->username;
        $this->cursos = JoomdleHelperContent::getCourseCategory ($id, $username);

        $this->categories = JoomdleHelperContent::getCourseCategories ($id);

        if(is_object($menu) && $menu->query['view'] != 'coursecategory') {
                            $pathway->addItem($this->cat_name, '');
                    }

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        $document = JFactory::getDocument();
        $document->setTitle($this->cat_name);

        parent::display($tpl);
    }
}
?>
