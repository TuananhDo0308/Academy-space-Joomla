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
class JoomdleViewMycourses extends JViewLegacy {
    function display($tpl = null) {

    $app        = JFactory::getApplication();
    $this->params = $app->getParams();

    $group_by_category = $this->params->get( 'group_by_category' );

    $user = JFactory::getUser();
    $username = $user->username;
    if ($group_by_category)
        $this->my_courses = JoomdleHelperContent::call_method ('my_courses', $username, 1);
    else
        $this->my_courses = JoomdleHelperContent::getMyCourses();

    if  ($this->params->get( 'courses_shown' ))
    {
        if (is_array($this->params->get( 'courses_shown' )))
            $courses_shown = $this->params->get( 'courses_shown' );
        else
            $courses_shown = array ( $this->params->get( 'courses_shown' ));

        $this->my_courses = $this->filter_by_value ($this->my_courses, 'id', $courses_shown);
    }

    if  ($this->params->get( 'courses_not_shown' ))
    {
        if (is_array($this->params->get( 'courses_not_shown' )))
            $courses_not_shown = $this->params->get( 'courses_not_shown' );
        else
            $courses_not_shown = array ( $this->params->get( 'courses_not_shown' ));

        $this->my_courses = $this->exclude_by_value ($this->my_courses, 'id', $courses_not_shown);
    }

    if  ($this->params->get( 'categories_shown' ))
    {
        if (is_array($this->params->get( 'categories_shown' )))
            $cats_shown = $this->params->get( 'categories_shown' );
        else
            $cats_shown = array ( $this->params->get( 'categories_shown' ));

        $this->my_courses = $this->filter_by_value ($this->my_courses, 'category', $cats_shown);
    }

    if  ($this->params->get( 'categories_not_shown' ))
    {
        if (is_array($this->params->get( 'categories_not_shown' )))
            $cats_shown = $this->params->get( 'categories_not_shown' );
        else
            $cats_shown = array ( $this->params->get( 'categories_not_shown' ));

        $this->my_courses = $this->exclude_by_value ($this->my_courses, 'category', $cats_shown);
    }


    $this->jump_url =  JoomdleHelperContent::getJumpURL ();

    $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

    $this->_prepareDocument();


    if ($group_by_category)
        $tpl =  "cats";

    if ($this->my_courses)
        parent::display($tpl);
    else
        echo '<span class="joomdle_nocourses_message">'.$this->params->get('nocourses_text') . "</span>";
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
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_MY_COURSES'));
        }
    }

    protected function filter_by_value ($array, $index, $value)
    {
        $newarray = array ();
        if(is_array($array) && count($array)>0)
        {
            foreach(array_keys($array) as $key)
            {
                if (array_key_exists ($index, $array[$key]))
                    $temp[$key] = $array[$key][$index];
                else $temp[$key] = 0;

                if (in_array ($temp[$key] ,$value)){
                    $newarray[$key] = $array[$key];
                }
            }
        }
        return $newarray;
    }

    protected function exclude_by_value ($array, $index, $value)
    {
        $newarray = array ();
        if(is_array($array) && count($array)>0)
        {
            foreach(array_keys($array) as $key)
            {
                if (array_key_exists ($index, $array[$key]))
                    $temp[$key] = $array[$key][$index];
                else $temp[$key] = 0;

                if (!in_array ($temp[$key] ,$value)){
                    $newarray[$key] = $array[$key];
                }
            }
        }
        return $newarray;
    }
}
?>
