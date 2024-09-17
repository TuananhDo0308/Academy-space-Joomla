<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');
jimport('joomla.filesystem.folder');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/content.php');


class JoomdleHelperActivities
{
    static function add_activity_course ($id, $name, $desc, $cat_id, $cat_name)
    {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $activities = $params->get( 'activities' );

        switch ($activities)
        {
            default:
                JPluginHelper::importPlugin( 'joomdleactivities' );
                $result = JFactory::getApplication()->triggerEvent ('OnAddActivityCourse', array($id, $name, $desc, $cat_id, $cat_name));
                $courses = array_shift ($result);
                break;
        }
    }

    static function add_activity_course_enrolment ($username, $course_id, $course_name, $cat_id, $cat_name)
    {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $activities = $params->get( 'activities' );

        switch ($activities)
        {
            default:
                JPluginHelper::importPlugin( 'joomdleactivities' );
                $result = JFactory::getApplication()->triggerEvent ('OnAddActivityCourseEnrolment', array($username, $course_id, $course_name, $cat_id, $cat_name));
                $courses = array_shift ($result);
                break;
        }
    }

    static function add_activity_quiz_attempt ($username, $course_id, $course_name, $quiz_name)
    {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $activities = $params->get( 'activities' );

        switch ($activities)
        {
            default:
                JPluginHelper::importPlugin( 'joomdleactivities' );
                $result = JFactory::getApplication()->triggerEvent ('OnAddActivityQuizAttempt', array($username, $course_id, $course_name, $quiz_name));
                $courses = array_shift ($result);
                break;
        }
    }

    static function add_activity_course_completed ($username, $course_id, $course_name)
    {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $activities = $params->get( 'activities' );

        switch ($activities)
        {
            default:
                JPluginHelper::importPlugin( 'joomdleactivities' );
                $result = JFactory::getApplication()->triggerEvent ('OnAddActivityCourseCompleted', array($username, $course_id, $course_name));
                $courses = array_shift ($result);
                break;
        }
    }

}


?>
