<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');

/**
 * Content Component Query Helper
 *
 * @static
 * @package     Joomdle
 */
class JoomdleHelperEvents
{
    static function trigger_event ($event, $data)
    {
        JPluginHelper::importPlugin( 'joomdleevent' );
        JFactory::getApplication()->triggerEvent ($event, array ($data));
    }
}
