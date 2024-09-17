<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');
// Import Joomla! libraries
jimport( 'joomla.application.component.view');
require_once( JPATH_COMPONENT.'/helpers/forum.php' );
require_once( JPATH_COMPONENT.'/helpers/content.php' );

class JoomdleViewForums extends JViewLegacy {
    function display($tpl = null) {

        $this->courses = JoomdleHelperContent::getCourseList ();

        $this->addToolbar();
        $this->render_sidebar ();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_FORUMS_TITLE'), 'forums');

        JToolBarHelper::custom( 'sync_to_kunena', 'forward', 'forward', 'COM_JOOMDLE_SYNC_TO_KUNENA', true, false );

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=forums');
    }

    private function render_sidebar ()
    {
        jimport( 'joomla.version' );
        $jversion = new JVersion();
        $joomla_short_version = $jversion->getShortVersion();

        if (version_compare($joomla_short_version, "4.0.0-beta1") <  0)
            $this->sidebar = JHtmlSidebar::render();
    }

}
?>
