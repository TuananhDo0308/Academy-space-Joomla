<?php
/**
* @version      
* @package      Joomdle
* @copyright        Antonio Duran Terres
* @license      GNU/GPL, see LICENSE.php
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

class  plgSystemJoomdlesession extends JPlugin
{

    function __construct (& $subject, $config)
    {
        parent::__construct($subject, $config);

    }

    /* Updates Moodle Session */
    function onAfterRender()
    {
        // Fail gracefully if Joomdle is not present
        if (!file_exists (JPATH_SITE.'/components/com_joomdle/helpers/content.php'))
            return;

        require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');

        $app = JFactory::getApplication();

        if ($app->isClient('administrator'))
            return;

        $logged_user = JFactory::getUser();
        $user_id = $logged_user->id;

        /* Don't update guest sessions */
        if (!$user_id)
            return;

        $reply = JoomdleHelperContent::call_method ("update_session", $logged_user->username);
    }

}
