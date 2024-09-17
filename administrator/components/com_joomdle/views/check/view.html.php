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
class JoomdleViewCheck extends JViewLegacy {
    function display($tpl = null) {

    $params = JComponentHelper::getParams( 'com_joomdle' );
        if ($params->get( 'MOODLE_URL' ) == "")
        {
            echo "Joomdle is not configured yet. Please fill Moodle URL setting in Configuration";
            return;
        }


        $this->system_info = JoomdleHelperContent::check_joomdle_system ();

        $this->addToolbar();
        $this->render_sidebar ();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_SYSTEM_CHECK_TITLE'), 'check');

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=check');
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
