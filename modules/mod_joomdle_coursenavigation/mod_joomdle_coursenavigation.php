<?php
/**
* @version      1.1.0
* @package      Joomdle - Mod Show Course Navigation Block
* @copyright    Qontori Pte Ltd
* @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/content.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/system.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/groups.php');

// Include the whosonline functions only once
require_once (dirname(__FILE__).'/helper.php');

$linkto = $params->get( 'linkto' , 'moodle');

$comp_params = JComponentHelper::getParams( 'com_joomdle' );

$default_itemid = $comp_params->get( 'default_itemid' );
$joomdle_itemid = $comp_params->get( 'joomdle_itemid' );
$courseview_itemid = $comp_params->get( 'courseview_itemid' );
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$course_id = JRequest::getVar ('course_id');
if (!$course_id)
    $course_id = JRequest::getVar ('id', 0, 'int');

if (!$course_id)
    return;


require(JModuleHelper::getLayoutPath('mod_joomdle_coursenavigation'));
