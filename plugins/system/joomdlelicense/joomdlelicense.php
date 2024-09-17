<?php
/**
* @version      1.0.0
* @package      Joomdle License
* @copyright    Qontori Pte Ltd
* @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class  plgSystemJoomdlelicense extends JPlugin
{
    function onInstallerAfterInstaller ($model, $package, $installer, $result, $msg)
    {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $license = $params->get ('license_key');
        $db           = JFactory::getDBO();
        $query = 'update' .
            ' #__update_sites' .
            " SET extra_query=" . $db->Quote('dlid=' . $license) .
            " WHERE  name = " . $db->Quote('Joomdle');

        $db->setQuery($query);
        $db->execute();
    }
}
