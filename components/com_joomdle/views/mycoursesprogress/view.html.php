<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * The user has not completed this activity.
 * This is a completion state value (course_modules_completion/completionstate)
 */
define('COMPLETION_INCOMPLETE', 0);
/**
 * The user has completed this activity. It is not specified whether they have
 * passed or failed it.
 * This is a completion state value (course_modules_completion/completionstate)
 */
define('COMPLETION_COMPLETE', 1);
/**
 * The user has completed this activity with a grade above the pass mark.
 * This is a completion state value (course_modules_completion/completionstate)
 */
define('COMPLETION_COMPLETE_PASS', 2);
/**
 * The user has completed this activity but their grade is less than the pass mark
 * This is a completion state value (course_modules_completion/completionstate)
 */
define('COMPLETION_COMPLETE_FAIL', 3);

/**
 * The effect of this change to completion status is unknown.
 * A completion effect changes (used only in update_state)
 */
define('COMPLETION_UNKNOWN', -1);


jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewMycoursesprogress extends JViewLegacy {
    function display($tpl = null) {

    $app        = JFactory::getApplication();
    $this->params = $app->getParams();

    //document object
    $jdoc = JFactory::getDocument();

    $user = JFactory::getUser();
    $username = $user->username;

    //add the stylesheet
    $jdoc->addStyleSheet(JURI::root ().'components/com_joomdle/css/completion_progress.css');
    $this->my_courses = JoomdleHelperContent::call_method('my_courses_completion_progress', $username);

    $this->jump_url =  JoomdleHelperContent::getJumpURL ();

    $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

    $this->_prepareDocument();

    if ($this->my_courses)
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
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_MY_COURSESPROGRESS'));
        }
    }

    protected function get_course_progress ($progress, $course_id)
    {
        $text = '';

        $total = count  ($progress);
        if ($total == 0)
            return "";

        // Get colours and use defaults if they are not set in global settings.
        $colours = array(
            'attempted_colour' => '#73A839',
            'submittednotcomplete_colour' => '#FFCC00',
            'notattempted_colour' => '#C71C22',
            'futurenotattempted_colour' => '#025187'
        );
        $colors = array();
        foreach ($colours as $name => $color) {
            $colors[$name] = $color;
        }

        $celldisplay = 'table-cell';
        $rowoptions['style'] = 'white-space: nowrap;';
        $i = 0;
        $cellwidth = floor(100 / $total);
        $cellunit = '%';
        foreach ($progress as $p)
        {
            $i++;

            $celloptions = array(
            'class' => 'progressBarCell',
             'style' => 'display:' . $celldisplay .'; width:' . $cellwidth . $cellunit . ';background-color:');

            $hover = $p['name'];
            $celloptions['class'] = 'progressBarCell';

            $attempted = $p['attempted'];
            if ($attempted === 'submitted') {
                $celloptions['style'] .= $colors['submittednotcomplete_colour'].';';
            } else if ($attempted === 'attempted') {
                $celloptions['style'] .= $colors['attempted_colour'].';';

            } else {
                $celloptions['style'] .= $colors['futurenotattempted_colour'].';';
            }

            if ($i == 1) {
                $celloptions['class'] .= ' firstProgressBarCell';
            }
            if ($i == $total) {
                $celloptions['class'] .= ' lastProgressBarCell';
            }

            $text .= "<div  title='$hover' class='".$celloptions['class']."' style='". $celloptions['style'] . "'></div>";
        }

        return $text;
    }

    protected function get_course_completion_progress ($progress, $course_id)
    {
        $text = '';

        $total = count  ($progress);
        if ($total == 0)
            return "";

        // Get colours and use defaults 
        $colours = array(
            'completed_colour' => '#73A839',
            'submittednotcomplete_colour' => '#FFCC00',
            'notCompleted_colour' => '#C71C22',
            'futureNotCompleted_colour' => '#025187'
        );

        $colors = array();
        foreach ($colours as $name => $color) {
            $colors[$name] = $color;
        }

        $celldisplay = 'table-cell';
        $rowoptions['style'] = 'white-space: nowrap;';
        $i = 0;
        $cellwidth = floor(100 / $total);
        $cellunit = '%';

        $params = JComponentHelper::getParams( 'com_joomdle' );
        $linkstarget = $params->get( 'linkstarget' );
        if ($linkstarget == 'wrapper')
            $use_wrapper = 1;
        else $use_wrapper = 0;

        $itemid = $this->params->get( 'default_itemid' );

        $now = time();
        foreach ($progress as $p)
        {
            $i++;

            if ($use_wrapper)
                $link = 'index.php?option=com_joomdle&view=wrapper&moodle_page_type=fullurl&gotourl='. urlencode ($p['link'])
                        . "&Itemid=$itemid";
            else
                $link = $p['link'];

            $celloptions = array(
            'class' => 'progressBarCell',
             'style' => 'display:' . $celldisplay .'; width:' . $cellwidth . $cellunit . ';background-color:',
             'onclick'=>'document.location="'.$link.'"' . ';'
             );

            $hover = $p['name'];
            $celloptions['class'] = 'progressBarCell';

            $complete = $p['completed'];
            if ($complete === 'submitted') 
            {
                $celloptions['style'] .= $colors['submittednotcomplete_colour'].';';
                $cellcontent = '';
            }
            else if ($complete == COMPLETION_COMPLETE || $complete == COMPLETION_COMPLETE_PASS)
            {
                $celloptions['style'] .= $colors['completed_colour'].';';
                $cellcontent = '<img class="smallicon" alt="" src="media/joomdle/images/completion_progress/tick.gif">';
            }
            else if ($complete == COMPLETION_COMPLETE_FAIL ||
                    (!isset($config->orderby) || $config->orderby == 'orderbytime') &&
                    (isset($activity['expected']) && $activity['expected'] > 0 && $activity['expected'] < $now))
            {
                    $celloptions['style'] .= $colors['notCompleted_colour'].';';
                    $cellcontent = '<img class="smallicon" alt="" src="media/joomdle/images/completion_progress/cross.gif">';
            }
             else 
             {
                $celloptions['style'] .= $colors['futureNotCompleted_colour'].';';
                $cellcontent = '';
             }

            if ($i == 1) {
                $celloptions['class'] .= ' firstProgressBarCell';
            }
            if ($i == $total) {
                $celloptions['class'] .= ' lastProgressBarCell';
            }

            $text .= "<div  title='$hover' class='".$celloptions['class']."' style='". $celloptions['style'] . "'" .  "onclick='".$celloptions['onclick']. "'>";
            $text .= $cellcontent;
            $text .= "</div>";
        }

        return $text;
    }


}
?>
