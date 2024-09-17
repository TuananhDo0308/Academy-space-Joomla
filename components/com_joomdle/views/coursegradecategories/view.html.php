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
class JoomdleViewCoursegradecategories extends JViewLegacy {
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

        if ($this->params->get('gradingsystem_show_items') == 0)
            $this->gcats = JoomdleHelperContent::getCourseGradeCategories($id);
        else
        {
            $this->gcats = JoomdleHelperContent::call_method('get_course_grade_categories_and_items', $id);
            $tpl = 'full';
        }

        /* pathway */
        $cat_slug = $this->course_info['cat_id']."-".$this->course_info['cat_name'];
        $course_slug = $this->course_info['remoteid']."-".$this->course_info['fullname'];

        if(is_object($menu) && $menu->query['view'] != 'coursegradecategories') {
                        $pathway->addItem($this->course_info['cat_name'], 'index.php?view=coursecategory&cat_id='.$cat_slug);
                        $pathway->addItem($this->course_info['fullname'], 'index.php?view=detail&cat_id='.$cat_slug.'&course_id='.$course_slug);
                        $pathway->addItem(JText::_('COM_JOOMDLE_COURSE_GRADING_SYSTEM'), '');
                }

        $document = JFactory::getDocument();
        $document->setTitle($this->course_info['fullname'] . ': ' . JText::_('COM_JOOMDLE_GRADING_SYSTEM'));

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));


        parent::display($tpl);
    }
}
?>
