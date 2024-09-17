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

/**
 * Content Component Query Helper
 *
 * @static
 * @package     Joomdle
 * @since 1.5
 */
class JoomdleHelperMappings
{

    static function get_app_mappings ($app)
    {
        $db           = JFactory::getDBO();
        $query = 'SELECT *' .
            ' FROM #__joomdle_field_mappings' .
            " WHERE joomla_app = " . $db->Quote($app);
        $db->setQuery($query);
        $mappings = $db->loadObjectList();

        if (!$mappings)
            return array ();

        return $mappings;
    }

    static function getMappings ($filter_type, $limitstart, $limit, $filter_order, $filter_order_Dir, $search)
    {
        $db           = JFactory::getDBO();

        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('id', 'joomla_app', 'joomla_field', 'moodle_field')));
        $query->from($db->quoteName('#__joomdle_field_mappings'));

        $wheres = array ();
        if ($filter_type != '')
            $query->where($db->quoteName('joomla_app') . ' = '. $db->quote($filter_type));

        if ($search)
        {
            $query->where($db->quoteName('joomla_field') . ' LIKE '. $search  . " OR ". $db->quoteName('moodle_field') . ' LIKE ' . $search);
        }

        $query->order("$filter_order $filter_order_Dir");

        $db->setQuery($query, $limitstart, $limit);
        $mappings = $db->loadAssocList();

        if (!$mappings)
            return array ();

        foreach ($mappings as $mapping)
        {
            $mapping['joomla_field_name'] = JoomdleHelperMappings::get_field_name ( $mapping['joomla_app'], $mapping['joomla_field'] );
            $mapping['moodle_field_name'] = JoomdleHelperMappings::get_moodle_field_name ( $mapping['moodle_field'] );
            $m[] = $mapping;
        }

        return $m;
    }

    static function getMapping ($id)
    {
        $db           = JFactory::getDBO();
        $query = 'SELECT *' .
            ' FROM #__joomdle_field_mappings' .
                              " WHERE id = " . $db->Quote($id);
        $db->setQuery($query);
        $mapping = $db->loadObject();

        return $mapping;
    }

    static function get_moodle_field_name ($field_id)
    {
        static $fields;

        if (!$fields)
            $fields = JoomdleHelperContent::call_method ('user_custom_fields');

        if (!$fields)
            return $field_id;

        foreach ($fields as $field)
        {
            if ("cf_".$field['id'] == $field_id)
                return $field['shortname'] . ' - ' . $field['name'];
        }

        return $field_id;
    }

    static function get_moodle_custom_field_value ($user_info, $field_id)
    {
        foreach ( $user_info['custom_fields'] as $field)
        {
            if ('cf_'.$field['id'] == $field_id)
            {
                $data = utf8_decode ($field['data']);
                break;
            }
        }

        return $data;
    }

    static function delete_mappings ($cid)
    {
                $db           = JFactory::getDBO();
        foreach ($cid as $id)
        {
            $query = 'DELETE ' .
                ' FROM #__joomdle_field_mappings' .
                      " WHERE id = " . $db->Quote($id);
            $db->setQuery($query);
            $db->execute();
        }
    }

    static function get_user_info ($username, $app = '')
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );

        if (!$app)
            $app = $comp_params->get( 'additional_data_source' );

        $id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($id);

        $user_info['email'] = $user->email;

        /* Language */
        $user_info['lang']   = JoomdleHelperMappings::get_moodle_lang ($user->getParam( 'language' ));

        /* Timezone */
        $user_info['timezone']   = $user->getParam( 'timezone' );

        $user_info['password']   = $user->password;

        $user_info['suspended']   = $user->block;

        $more_info = array ();
        switch ($app)
        {
            case 'no':
                $more_info = JoomdleHelperMappings::get_user_info_joomla ($username);
                break;
            default:
                JPluginHelper::importPlugin( 'joomdleprofile' );
                $result = JFactory::getApplication()->triggerEvent('onJoomdleGetUserInfo', array($username));
                foreach ($result as $info)
                {
                    if (count ($info))
                    {
                        // Allow for more than one plugin to return user info
                        $more_info = array_merge ($more_info, $info);
                    }
                }
                break;

        }

		$result = array_merge ($user_info, $more_info);

        // Do not sync Moodle DB ID field to Moodle
        unset ($result['id']);

        return $result;
    }

    static function get_user_info_for_joomla ($username)
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $app = $comp_params->get( 'additional_data_source' );

        $id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($id);

        if (!$id)
            return array ();

        $user_info['name'] = $user->name;
        $user_info['email'] = $user->email;

        /* Language */
        $user_info['lang']   = JoomdleHelperMappings::get_moodle_lang ($user->getParam( 'language' ));

        /* Timezone */
        $user_info['timezone']   = $user->getParam( 'timezone' );

        $more_info = array ();
        switch ($app)
        {
            case 'no':
                $more_info = JoomdleHelperMappings::get_user_info_joomla ($username);
                break;
            default:
                JPluginHelper::importPlugin( 'joomdleprofile' );
                $result = JFactory::getApplication()->triggerEvent ('onJoomdleGetUserInfo', array($username));
                foreach ($result as $info)
                {
                    if (count ($info))
                    {
                        $more_info = $info;
                        break;
                    }
                }
                break;
        }

        if ((is_array ($more_info)) && ((array_key_exists ('country', $more_info))))
        {
            require(JPATH_ADMINISTRATOR.'/'.'components'.'/'.'com_joomdle'.'/'.'helpers'.'/'.'countries_joomla.php');
            if ($more_info['country'])
                $more_info['country'] = $countries[$more_info['country']];
        }

       if ((!is_array ($more_info)) || (!array_key_exists ('pic_url', $more_info)) || (!$more_info['pic_url']) || ($more_info['pic_url'] == 'none'))
                $more_info['pic_url'] = JURI::root() . '/media/joomdle/images/avatar.png';


        return array_merge ($user_info, $more_info);
    }

    static function get_field_name ($app, $field)
    {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $additional_data_source = $params->get( 'additional_data_source' );

        $name = '';
        switch ($app)
        {
            case $additional_data_source:
                JPluginHelper::importPlugin( 'joomdleprofile' );
                $result = JFactory::getApplication()->triggerEvent ('onJoomdleGetFieldName', array($field));
                foreach ($result as $name)
                {
                    if ($name != '')
                        break;
                }
                break;
            default:
                // Don't return name from not enabled data source
                break;
        }

        return $name;
    }

    static function get_moodle_fields ()
    {
        return JoomdleHelperContent::call_method ('user_custom_fields');
    }

    static function get_fields ($app)
    {
        $fields = array ();
        switch ($app)
        {
            default:
                JPluginHelper::importPlugin( 'joomdleprofile' );
                $result = JFactory::getApplication()->triggerEvent('onJoomdleGetFields', array());
                foreach ($result as $fields)
                {
                    if (count ($fields))
                        break;
                }
                break;
        }

        return $fields;
    }


    static function get_user_info_joomla ($username)
    {

        $id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($id);

        $user_info['firstname'] = JoomdleHelperMappings::get_firstname ($user->name);
        $user_info['lastname'] = JoomdleHelperMappings::get_lastname ($user->name);
        $user_info['pic_url'] =  'none';

        return $user_info;
    }

    /* General helper fns */

    static function get_firstname ($name)
    {
        $parts = explode (' ', $name);
        return  $parts[0];
    }

    static function get_lastname ($name)
    {
        $parts = explode (' ', $name);

        $lastname = '';
        $n = count ($parts);
        for ($i = 1; $i < $n; $i++)
        {
                if ($i != 1)
                        $lastname .= ' ';
                $lastname .= $parts[$i];
        }

        return $lastname;
    }

    static function get_moodle_country ($country)
    {
        require(JPATH_ADMINISTRATOR.'/'.'components'.'/'.'com_joomdle'.'/'.'helpers'.'/'.'countries.php');
        $lang = JFactory::getLanguage();
        $extension = 'com_community.country';
        $base_dir = JPATH_SITE;
        $language_tag = 'en-GB';
        $reload = true;
        $lang->load($extension, $base_dir, $language_tag, $reload);

        $country = JText::_ ($country);

        if ($country == 'selectcountry')
                return '';
        return $countries[$country];
    }

    static function get_joomla_country ($country)
    {
        require_once(JPATH_ADMINISTRATOR.'/'.'components'.'/'.'com_joomdle'.'/'.'helpers'.'/'.'countries_joomla.php');
        if ($country == 'selectcountry')
                return '';
        return $countries[$country];
    }

    static function get_moodle_lang ($lang)
    {
        if (!$lang)
            return '';

        return substr ($lang, 0, 2);
    }


    static function sync_user_to_joomla ($username)
    {
        $user_info = JoomdleHelperContent::call_method ('user_details', $username);

        JoomdleHelperMappings::create_additional_profile ($user_info);
        JoomdleHelperMappings::save_user_info ($user_info, false);
        
    }

    static function create_additional_profile ($user_info)
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $app = $comp_params->get( 'additional_data_source' );

        $username = $user_info['username'];
        $id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($id);

        switch ($app)
        {
            default:
                JPluginHelper::importPlugin( 'joomdleprofile' );
                JFactory::getApplication()->triggerEvent ('onJoomdleCreateAdditionalProfile', array($user_info));
                break;
        }
    }

    static function save_user_info ($user_info, $use_utf8_decode = true)
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $app = $comp_params->get( 'additional_data_source' );

        $username = $user_info['username'];
        $id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($id);

        /* Save info to joomla user table */
        if ($use_utf8_decode)
            $user->name = utf8_decode ($user_info['firstname']) . " " . utf8_decode ($user_info['lastname']);
        else
            $user->name = $user_info['firstname'] . " " . $user_info['lastname'];

        // Remove values not present in Joomla
        if (array_key_exists ('timezone', $user_info))
        {
            if (($user_info['timezone'] == 99) || ($user_info['timezone'] == 'UTC'))
                $user_info['timezone'] = '';
            $user->setParam( 'timezone', $user_info['timezone'] );
        }

        $user->block = $user_info['block'];

        switch ($app)
        {
            default:
                JPluginHelper::importPlugin( 'joomdleprofile' );
                $result = JFactory::getApplication()->triggerEvent ('onJoomdleSaveUserInfo', array($user_info, $use_utf8_decode));
                $more_info = array_shift ($result);
                break;

        }

        $user->save ();

        return $user_info;
    }


    static function save_user_info_joomla ($user_info)
    {
        $username = $user_info['username'];
        $id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($id);
    }

    /* This is *admin* profile url */
    static function get_profile_url ($username)
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $app = $comp_params->get( 'additional_data_source' );

        $id = JUserHelper::getUserId($username);
        $user = JFactory::getUser($id);
        $user_id = $user->id;

        $url = '';
        switch ($app)
        {
            case 'no':
                $url = 'index.php?option=com_users&task=user.edit&id='.$user_id;
                break;
            default:
                JPluginHelper::importPlugin( 'joomdleprofile' );
                $result = JFactory::getApplication()->triggerEvent ('onJoomdleGetProfileUrl', array($user_id));
                foreach ($result as $url)
                {
                    if ($url != '')
                        break;
                }
                break;
        }

        // Fail safe
        if ($url == '')
            $url = 'index.php?option=com_users&task=user.edit&id='.$user_id;

        return $url;
    }

    /* This is *frontend* profile url */
    static function get_frontend_profile_url ($username)
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $app = $comp_params->get( 'additional_data_source' );

        $user_id = JUserHelper::getUserId($username);

        $url = '';
        switch ($app)
        {
            case 'no':
                $url = "index.php?option=com_users&view=profile";
                break;
            default:
                JPluginHelper::importPlugin( 'joomdleprofile' );
                $result = JFactory::getApplication()->triggerEvent ('onJoomdleGetFrontendProfileUrl', array($user_id));
                foreach ($result as $url)
                {
                    if ($url != '')
                        break;
                }
                break;
        }

        // Fail safe
        if ($url == '')
            $url = "index.php?option=com_users&view=profile";

        return $url;
    }

    static function get_login_url ($course_id)
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $app = $comp_params->get( 'additional_data_source' );
        $itemid = $comp_params->get( 'joomdle_itemid' );

        $url = '';
        //XXX return only seems to work with normal Joomla login page (not CB or Jomsocial)
        $return = base64_encode ('index.php?option=com_joomdle&view=detail&course_id='.$course_id.'&Itemid='.$itemid);
        switch ($app)
        {
            case 'no':
                $url = "index.php?option=com_users&view=login&return=$return";
                break;
            default:
                JPluginHelper::importPlugin( 'joomdleprofile' );
                $result = JFactory::getApplication()->triggerEvent ('getJoomdleLoginUrl', array($return));
                foreach ($result as $url)
                {
                    if ($url != '')
                        break;
                }
                break;
        }

        // Fail safe
        if ($url == '')
            $url = "index.php?option=com_users&view=login&return=$return";

        return $url;
    }

    static function getStateOptions()
    {
        // Build the filter options.
        $options    = array();

        // Add sources added via plugins
        JPluginHelper::importPlugin( 'joomdleprofile' );
        $more_sources = JFactory::getApplication()->triggerEvent ('onGetAdditionalDataSource', array());
        if (is_array ($more_sources))
        foreach ($more_sources as  $source)
        {
            $keys =  array_keys ($source);
            $key = $keys[0];
            $source_name = array_shift ($source);
            $options[] = JHTML::_('select.option',  $key,  $source_name);

        }

        return $options;
    }

}

?>
