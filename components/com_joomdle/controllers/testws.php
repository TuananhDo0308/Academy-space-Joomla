<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/content.php');
require_once(JPATH_SITE.'/components/com_joomdle/controllers/ws.xmlrpc.php');

class JoomdleControllerTestws extends JControllerLegacy
{

    function test ()
    {
        return;
        $params = array ('pepe');
        $a = getUserInfo ("", $params);
        echo "<pre>";
        print_R ($a);
        exit ();
    }
}
