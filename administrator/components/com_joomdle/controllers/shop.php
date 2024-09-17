<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.'/helpers/helper.php' );
require_once( JPATH_COMPONENT.'/helpers/content.php' );
require_once( JPATH_COMPONENT.'/helpers/shop.php' );
require_once( JPATH_COMPONENT.'/helpers/mappings.php' );
require_once( JPATH_COMPONENT.'/helpers/parents.php' );
require_once( JPATH_COMPONENT.'/helpers/profiletypes.php' );
require_once( JPATH_COMPONENT.'/helpers/applications.php' );
require_once( JPATH_COMPONENT.'/helpers/mailinglist.php' );
require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/helpers/forum.php');


// Added to deal with Hikashop, which still uses DS
if(!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);


/**
 * Joomdle Controller
 *
 * @package Joomla
 * @subpackage Joomdle
 */
class JoomdleControllerShop extends JControllerAdmin {

    function publish()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Check that a shop category is set
        $params = JComponentHelper::getParams( 'com_joomdle' );
        if ($params->get('courses_category') == 'no')
        {
            $error = JText::_( 'COM_JOOMDLE_SHOP_CATEGORY_NOT_SET' );
            JFactory::getApplication()->enqueueMessage($error, 'error');
            $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
            return;
        }

        $cid   = $this->input->get('cid', array ());

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperShop::publish_courses ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );

    }

    function unpublish()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperShop::dont_sell_courses ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );

    }

    function reload ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        $real_cid = array();
        foreach ($cid as $id)
        {
            // Skip bundles 
            if ($id)
                $real_cid[] = $id;
        }
        JoomdleHelperShop::reload_courses ($real_cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
    }

    function delete_courses_from_shop ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperShop::delete_courses ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
    }

    function new_bundle ()
    {
        $this->setRedirect( 'index.php?option=com_joomdle&view=shop&task=edit_bundle' );
    }

    function save_bundle ()
    {
        $bundle['courses']   = $this->input->get('cid', array ());
        $bundle['name']   = $this->input->get('name');
        $bundle['description']   = $this->input->get('description');
        $bundle['cost']   = $this->input->get('cost');
        $bundle['currency']   = $this->input->get('currency');
        $bundle['bundle_id']   = $this->input->get('bundle_id');

        if ($bundle_id)
            $bundle['id'] = $bundle_id;

        JoomdleHelperShop::create_bundle ($bundle);

        $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
    }

    function cancel_edit_shop ()
    {
        $this->setRedirect( 'index.php?option=com_joomdle&view=shop' );
    }
}
?>
