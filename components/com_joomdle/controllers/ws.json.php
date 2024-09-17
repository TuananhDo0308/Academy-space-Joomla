<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

use Joomla\CMS\User\UserHelper;

require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/mappings.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/groups.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/users.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/shop.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/points.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/mailinglist.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/joomlagroups.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/forum.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/activities.php');

class JoomdleControllerWs extends JControllerLegacy
{
    function getUserInfo($params)
    {
        $username = $params['username'];
        if (array_key_exists ('app', $params))
            $app = $params['app'];
        else $app = '';
        $user_info = JoomdleHelperMappings::get_user_info ($username, $app);
        return $user_info;
    }

    function test ()
    {
        return "It works";
    }

    /* Web service used to log in from Moodle */
    function login ($params)
    {
        $username = $params['username'];
        $password = $params['password'];

        $mainframe = JFactory::getApplication('site');

        $user_id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($user_id);

        if (!$user)
            return false;

        if ($user->block)
            return false;

        $options = array ( 'skip_joomdlehooks' => '1', 'silent' => 1);
        $credentials = array ( 'username' => $username, 'password' => $password);
        if ($mainframe->login( $credentials, $options ))
            return true;
        return false;
    }

    function joomdle_getDefaultItemid ()
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $default_itemid = $comp_params->get( 'default_itemid' );
        return $default_itemid;
    }


  function confirmJoomlaSession($params)
    {
        $username = $params['username'];
        $token = $params['joomdle_auth_token'];

        $db = JFactory::getDBO();
        $query = 'SELECT session_id' .
                ' FROM #__session' .
                " WHERE username = ". $db->Quote($username). " and  md5(session_id) = ". $db->Quote($token);
        $db->setQuery( $query );
        $session = $db->loadResult();

        if ($session)
            return true;
        else
            return false;
    }

    function logout($params)
    {
        $username = $params['username'];
        $ua_string = $params['ua_string'];

        $mainframe = JFactory::getApplication('site');

        $id = JUserHelper::getUserId($username);

        $error = $mainframe->logout($id, array ( 'clientid' => 0, 'skip_joomdlehooks' => 1));

        // Return "remember me" cookie name so it  can be deleted
        $ua = new JApplicationWebClient ($ua_string);
        $uaString = $ua->userAgent;
        $browserVersion = $ua->browserVersion;
        $uaShort = str_replace($browserVersion, 'abcd', $uaString);

        $r = md5(JUri::base() . $uaShort);

        return $r;
    }

    function deleteUserKey ($params)
    {
        $series = $params['series'];

        // Delete the key
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query
            ->delete('#__user_keys')
            ->where($db->quoteName('series') . ' = ' . $db->quote($series));

        $db->setQuery($query)->execute();
    }

    function createUser ($params)
    {
        $userinfo = $params['userinfo'];
        return JoomdleHelperUsers::create_joomla_user ($userinfo);
    }

    function activateUser ($params)
    {
        $username = $params['username'];

        $username = utf8_decode ($username);

        return JoomdleHelperUsers::activate_joomla_user ($username);
    }

    function updateUser ($params)
    {
        $userinfo = $params['userinfo'];
        return JoomdleHelperMappings::save_user_info ($userinfo, false);
    }

    function changePassword ($params)
    {
        $username = $params['username'];
        $password = $params['password'];

        $username = utf8_decode ($username);

        $user_id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($user_id);

        // Password comes hashed from Moodle, just store it XXX NOT anymoe
 //       $user->password = $password;
        $user->password = UserHelper::hashPassword ($password);

        @$user->save();

        return true;
    }

    function changeUsername ($params)
    {
        $old_username = $params['old_username'];
        $new_username = $params['new_username'];

        $old_username = utf8_decode ($old_username);
        $new_username = utf8_decode ($new_username);

        $user_id = JUserHelper::getUserId($old_username);
        $user = JFactory::getUser($user_id);

        // Password comes hashed from Moodle, just store it
        $user->username = $new_username;
        @$user->save();

        return true;
    }

    function deleteUser ($params)
    {
        $username = $params['username'];
        $username = utf8_decode ($username);

        $user_id = JUserHelper::getUserId($username);

        if (!$user_id)
            return;

        $user = JFactory::getUser($user_id);
        $user->delete();
    }

    function addActivityCourse ($params)
    {
        $id = $params['id'];
        $name = $params['name'];
        $desc = $params['desc'];
        $cat_id = $params['cat_id'];
        $cat_name = $params['cat_name'];

        return JoomdleHelperActivities::add_activity_course ($id, $name, $desc, $cat_id, $cat_name);
    }

    function addActivityCourseEnrolment ($params)
    {
        $username = $params['username'];
        $course_id = $params['course_id'];
        $course_name = $params['course_name'];
        $cat_id = $params['cat_id'];
        $cat_name = $params['cat_name'];

        return JoomdleHelperActivities::add_activity_course_enrolment ($username, $course_id, $course_name, $cat_id, $cat_name);
    }

    function addSocialGroup ($params)
    {
        $description = $params['description'];
        $course_id = $params['course_id'];

        return JoomdleHelperSocialgroups::add_group ($name, $description, $course_id);
    }

    function updateSocialGroup ($params)
    {
        $name = $params['name'];
        $description = $params['desc'];
        $course_id = $params['course_id'];

        return JoomdleHelperSocialgroups::update_group ($name, $description, $course_id);
    }

    function deleteSocialGroup ($params)
    {
        $course_id = $params['course_id'];

        return JoomdleHelperSocialgroups::delete_group ($course_id);
    }

    function addSocialGroupMember ($params)
    {
        $username = $params['username'];
        $permissions = $params['permissions'];
        $course_id = $params['course_id'];

        $username = utf8_decode ($username);

        return JoomdleHelperSocialGroups::add_group_member ($username, $permissions, $course_id);
    }

    function removeSocialGroupMember ($params)
    {
        $username = $params['username'];
        $course_id = $params['course_id'];

        return JoomdleHelperSocialGroups::remove_group_member ($username, $course_id);
    }

    function addPoints ($params)
    {
        $action = $params['action'];
        $username = $params['username'];
        $courseid = $params['course_id'];
        $course_name = $params['course_name'];

        $username = utf8_decode ($username);
        $course_name = utf8_decode ($course_name);

        return JoomdleHelperPoints::addPoints ($action, $username, $courseid, $course_name);
    }

    function addActivityQuizAttempt ($params)
    {
        $username = $params['username'];
        $course_id = $params['course_id'];
        $course_name = $params['course_name'];
        $quiz_name = $params['quiz_name'];

        return JoomdleHelperActivities::add_activity_quiz_attempt ($username, $course_id, $course_name, $quiz_name);
    }

    function addMailingSub ($params)
    {
        $username = $params['username'];
        $course_id = $params['course_id'];
        $type = $params['type'];

        $username = utf8_decode ($username);

        return JoomdleHelperMailinglist::add_list_member ($username, $course_id, $type);
    }

    function removeMailingSub ($params)
    {
        $username = $params['username'];
        $course_id = $params['course_id'];
        $type = $params['type'];

        $username = utf8_decode ($username);

        return JoomdleHelperMailinglist::remove_list_member ($username, $course_id, $type);
    }

    function addUserGroups ($params)
    {
        $course_id = $params['course_id'];
        $course_name = $params['course_name'];

        return JoomdleHelperJoomlagroups::add_course_groups ($course_id, $course_name);
    }

    function updateUserGroups ($params)
    {
        $course_id = $params['course_id'];
        $course_name = $params['course_name'];

        return JoomdleHelperJoomlagroups::update_course_groups ($course_id, $course_name);
    }

    function removeUserGroups ($params)
    {
        $course_id = $params['course_id'];

        return JoomdleHelperJoomlagroups::remove_course_groups ($course_id);
    }

    function addGroupMember ($params)
    {
        $course_id = $params['course_id'];
        $username = $params['username'];
        $type = $params['type'];

        $username = utf8_decode ($username);

        return JoomdleHelperJoomlagroups::add_group_member ($course_id, $username, $type);
    }

    function removeGroupMember ($params)
    {
        $course_id = $params['course_id'];
        $username = $params['username'];
        $type = $params['type'];

        $username = utf8_decode ($username);

        return JoomdleHelperJoomlagroups::remove_group_member ($course_id, $username, $type);
    }

    function addForum ($params)
    {
        $course_id = $params['course_id'];
        $forum_id = $params['forum_id'];
        $forum_name = $params['forum_name'];

        $forum_name = utf8_decode ($forum_name);

        return JoomdleHelperForum::add_forum ($course_id, $forum_id, $forum_name);
    }

    function updateForum ($params)
    {
        $course_id = $params['course_id'];
        $forum_id = $params['forum_id'];
        $forum_name = $params['forum_name'];

        $forum_name = utf8_decode ($forum_name);

        return JoomdleHelperForum::update_forum ($course_id, $forum_id, $forum_name);
    }

    function removeForum ($params)
    {
        $course_id = $params['course_id'];
        $forum_id = $params['forum_id'];

        return JoomdleHelperForum::remove_forum ($course_id, $forum_id);
    }

    function addForumsModerator ($params)
    {
        $course_id = $params['course_id'];
        $username = $params['username'];

        $username = utf8_decode ($username);

        return JoomdleHelperForum::add_forums_moderator ($course_id, $username);
    }

    function removeForumsModerator ($params)
    {
        $course_id = $params['course_id'];
        $username = $params['username'];

        $username = utf8_decode ($username);

        return JoomdleHelperForum::remove_forums_moderator ($course_id, $username);
    }

    function removeCourseForums ($params)
    {
        $course_id = $params['course_id'];

        return JoomdleHelperForum::remove_course_forums ($course_id);
    }

    function getSellUrl ($params)
    {
        $course_id = $params['course_id'];

        return JoomdleHelperShop::get_sell_url ($course_id);
    }

    function addActivityCourseCompleted ($params)
    {
        $username = $params['username'];
        $course_id = $params['course_id'];
        $course_name = $params['course_name'];

        return JoomdleHelperActivities::add_activity_course_completed ($username, $course_id, $course_name);
    }

    function moodleEvent ($params)
    {
        require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/events.php');

        $event_name = 'onJoomdle' . $params['event'];
        $event_params = $params['params'];

        return JoomdleHelperEvents::trigger_event ($event_name, $event_params);
    }

    function check_token ()
    {
        $token = $this->input->get('token');
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );

        $joomla_token = $comp_params->get( 'joomla_auth_token' );

        return  ($token == $joomla_token);
    }

    public function server ()
    {
        if (!$this->check_token ())
        {
            $token = $this->input->get('token');
            print_r (json_encode ("Invalid token:" . $token));
            return;
        }

        $methodvariables = array_merge($_GET, $_POST);

        $wsfunction = $this->input->get('wsfunction');

        // Name change because of conflict
        if ($wsfunction == 'getDefaultItemid')
            $wsfunction = 'joomdle_getDefaultItemid';

        echo json_encode ($this->$wsfunction ($methodvariables));
        exit ();
    }


}

?>
