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


class JoomdleHelperPoints
{
    static function addPoints ($action, $username, $course_id, $course_name)
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $user_points = $comp_params->get( 'user_points' );

        switch ($user_points)
        {
            default:
                JPluginHelper::importPlugin( 'joomdlepoints' );
                $result = JFactory::getApplication()->triggerEvent ('OnAddPoints', array($action, $username, $course_id, $course_name));
                $courses = array_shift ($result);
                break;

        }
        return "OK";
    }
}


?>
