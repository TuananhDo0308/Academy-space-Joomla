<?php
/**
 * @package     com_joomdle
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/applications.php' );


/**
 * Items list controller class.
 */
class JoomdleControllerApplications extends JControllerAdmin
{

    public function approve ()
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

        $this->setRedirect( 'index.php?option=com_joomdle&view=applications&course_id='.$course_id );

    }

    function reject ()
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

        $this->setRedirect( 'index.php?option=com_joomdle&view=applications&course_id='.$course_id );
    }

}
