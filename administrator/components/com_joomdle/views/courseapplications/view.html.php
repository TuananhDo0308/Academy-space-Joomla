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
require_once( JPATH_COMPONENT.'/helpers/content.php' );
require_once( JPATH_COMPONENT.'/helpers/mappings.php' );
require_once( JPATH_COMPONENT.'/helpers/applications.php' );

class JoomdleViewCourseapplications extends JViewLegacy {
    function display($tpl = null) {

        $cursos = JoomdleHelperContent::getCourseList ();
        $i = 0;
        $c = array ();
        foreach ($cursos as $curso)
        {
                $c[$i] = new JObject ();
                $c[$i]->id = $curso['remoteid'];
                $c[$i]->fullname = $curso['fullname'];
                $i++;
        }

        $this->courses = $c;

        $this->addToolbar();
        $this->render_sidebar ();

        $this->course_id = JFactory::getApplication()->input->get('course_id');

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_COURSE_APPLICATIONS_TITLE'), 'courseapplications');

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=courseapplications');
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
