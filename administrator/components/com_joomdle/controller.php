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


/**
 * Joomdle Controller
 *
 * @package Joomla
 * @subpackage Joomdle
 */
class JoomdleController extends JControllerLegacy {
    /**
     * Constructor
     * @access private
     * @subpackage Joomdle
     */
    function __construct() {
        //Get View
        if (JFactory::getApplication()->input->get('view') == '') {
            JFactory::getApplication()->input->set('view', 'default');
        }
        $this->item_type = 'Default';
        parent::__construct();
    }

    public function display($cachable = false, $urlparams = false)
    {
        // Load the submenu.
        $view   = $this->input->get('view', 'default');
        JoomdleHelper::addSubmenu($view);

        parent::display();

        return $this;

    }


    /* Users actions */
    function add_to_moodle ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperContent::add_moodle_users ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
    }

    function migrate_to_joomdle ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperContent::migrate_users_to_joomdle ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
    }

    function sync_profile_to_moodle ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperContent::sync_moodle_profiles ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
    }

    function sync_profile_to_joomla ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperContent::sync_joomla_profiles ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
    }

    function add_to_joomla ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperContent::create_joomla_users ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
    }

    function sync_parents_from_moodle ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperParents::sync_parents_from_moodle ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=users' );
    }


    function create_profiletype_on_moodle ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperProfiletypes::create_on_moodle ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=customprofiletypes' );
    }

    function dont_create_profiletype_on_moodle ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperProfiletypes::dont_create_on_moodle ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=customprofiletypes' );
    }

    function approve_applications ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $course_id   = $this->input->get('course_id', 0);
        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperApplications::approve_applications ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=courseapplications&task=manage_applications&course_id='.$course_id );
    }

    function reject_applications ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $course_id   = $this->input->get('course_id', 0);
        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperApplications::reject_applications ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=courseapplications&task=manage_applications&course_id='.$course_id );
    }

    function approve_application ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app_id   = $this->input->get('app_id', 0);
        $course_id   = $this->input->get('course_id', 0);
        $cid = array ($app_id);
        JoomdleHelperApplications::approve_applications ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=courseapplications&task=manage_applications&course_id='.$course_id );
    }

    function reject_application ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app_id   = $this->input->get('app_id', 0);
        $course_id   = $this->input->get('course_id', 0);
        $cid = array ($app_id);
        JoomdleHelperApplications::reject_applications ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=courseapplications&task=manage_applications&course_id='.$course_id );
    }

    function save_profiletype ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $id   = $this->input->get('profiletype_id', 0);
        $create_on_moodle   = $this->input->get('create_on_moodle', '');
        $moodle_role   = $this->input->get('roles', 0);

        $data->id = $id;
        if ($create_on_moodle == 'on')
            $data->create_on_moodle = 1;
        else
            $data->create_on_moodle = 0;
        $data->moodle_role = $moodle_role;

        JoomdleHelperProfiletypes::save_profiletype ($data);

        $this->setRedirect( 'index.php?option=com_joomdle&view=customprofiletypes' );
    }

    function back ()
    {
        $this->setRedirect( 'index.php?option=com_joomdle' );
    }

    function sync_to_kunena ()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $cid   = $this->input->get('cid', array ());
        Joomla\Utilities\ArrayHelper::toInteger($cid);

        if (count( $cid ) < 1) {
                $error = JText::_( 'COM_JOOMDLE_WARNING_MUST_SELECT' );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return;
        }
        JoomdleHelperForum::sync_forums ($cid);

        $this->setRedirect( 'index.php?option=com_joomdle&view=forums' );
    }

}
?>
