<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/content.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/mappings.php');

class JoomdleHelperUsers
{
    // Used by the web service call to sync a moodle user on registration
    static function create_joomla_user ($user_info)
    {
        // Do nothing if user already exists
        $db           = JFactory::getDBO();
        $query = 'SELECT id' .
            ' FROM #__users' .
            " WHERE username = " . $db->Quote($user_info['username']);
        $db->setQuery($query);
        $id = $db->loadResult();
        if ($id)
            return array ();

        $usersConfig = JComponentHelper::getParams( 'com_users' );

        $user = new JUser ();

        // Initialize new usertype setting
        $newUsertype = $usersConfig->get( 'new_usertype' );
        if (!$newUsertype) {
                $newUsertype = 2;
        }

        // Password comes in cleartext
        // On bind, Joomla hashes it again

        // Bind the user_info array to the user object
        if (!$user->bind( $user_info)) {
                $error = JText::_( $user->getError() );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return false;
        }

        // Set some initial user values
        $user->set('id', 0);
        $user->groups = array ();
        $user->groups[] = $newUsertype;

        $date = JFactory::getDate();
        $user->set('registerDate', $date->toSql());

        $parent = JFactory::getUser();
        if ($parent->id)
            $user->setParam('u'.$parent->id.'_parent_id', $parent->id);

        if ($user_info['block'])
            $user->set('block', '1');

        if (!$user_info['confirmed'])
            $user->set('activation', bin2hex(random_bytes (10)));

        // If there was an error with registration
        if ( !$user->save() )
        {
                $error = JText::_( $user->getError() );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return false;
        }

        /* Update profile additional data */
        return JoomdleHelperMappings::save_user_info ($user_info, false);
    }

    static function activate_joomla_user ($username)
    {
        $user_id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($user_id);
        $user->set('block', '0');
        $user->set('activation', '');
        if (!$user->save())
            return false;

        return true;
    }

    static function getStateOptions()
    {
        // Build the filter options.
        $options    = array();

//      $options[] = JHTML::_('select.option',  0, '- '. JText::_( 'COM_JOOMDLE_SELECT_FILTER' ) .' -');
        $options[] = JHTML::_('select.option',  'joomla', JText::_( 'COM_JOOMDLE_JOOMLA_USERS' ) );
        $options[] = JHTML::_('select.option',  'moodle', JText::_( 'COM_JOOMDLE_MOODLE_USERS' ) );
        $options[] = JHTML::_('select.option',  'joomdle', JText::_( 'COM_JOOMDLE_JOOMLDE_USERS' ) );
        $options[] = JHTML::_('select.option',  'not_joomdle', JText::_( 'COM_JOOMDLE_NOT_JOOMDLE_USERS' ) );


        return $options;
    }

}

?>
