<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');

//require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/help/coursegroups.php');


class JoomdleHelperSocialgroups
{
    static function add_group ($name, $description, $course_id)
    {
        JPluginHelper::importPlugin( 'joomdlesocialgroups' );
        JFactory::getApplication()->triggerEvent ('onAddSocialGroup', array ($name, $description, $course_id));
    }

    static function update_group ($name, $description, $course_id)
    {
        JPluginHelper::importPlugin( 'joomdlesocialgroups' );
        JFactory::getApplication()->triggerEvent ('onUpdateSocialGroup', array ($name, $description, $course_id));
    }

    static function delete_group ($course_id)
    {
        JPluginHelper::importPlugin( 'joomdlesocialgroups' );
        JFactory::getApplication()->triggerEvent ('onDeleteSocialGroup', array ($course_id));
    }

    static function add_group_member ($username, $permissions, $course_id)
    {
        JPluginHelper::importPlugin( 'joomdlesocialgroups' );
        JFactory::getApplication()->triggerEvent ('onAddSocialGroupMember', array ($username, $permissions, $course_id));
    }

    static function remove_group_member ($username, $course_id)
    {
        JPluginHelper::importPlugin( 'joomdlesocialgroups' );
        JFactory::getApplication()->triggerEvent ('onRemoveSocialGroupMember', array ($username, $course_id));
    }

    static function get_group_by_course_id ($course_id)
    {
        JPluginHelper::importPlugin( 'joomdlesocialgroups' );
        $result = JFactory::getApplication()->triggerEvent ('onGetGroupByCourseId', array ($course_id));
        foreach ($result as $group_id)
        {
            if ($group_id != '')
                break;
        }

        return $group_id;
    }

    static function get_group_url ($course_id)
    {
        JPluginHelper::importPlugin( 'joomdlesocialgroups' );
        $result = JFactory::getApplication()->triggerEvent ('onGetGroupUrl', array ($course_id));
        foreach ($result as $url)
        {
            if ($url != '')
                break;
        }

        return $url;
    }

}

?>
