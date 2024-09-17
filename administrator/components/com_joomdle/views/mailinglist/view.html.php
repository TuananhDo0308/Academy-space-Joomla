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

require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/mailinglist.php');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class JoomdleViewMailinglist extends JViewLegacy {
    function display($tpl = null) {

        $params = JComponentHelper::getParams( 'com_joomdle' );
        if ($params->get('mailing_list_integration') == 'no')
        {
            JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAILINGLIST_TITLE'), 'mailinglist');
            $this->message = JText::_('COM_JOOMDLE_MAILING_LIST_INTEGRATION_NOT_ENABLED');
            $tpl = "disabled";
            parent::display($tpl);
            return;
        }

        $this->courses = JoomdleHelperMailinglist::getListCourses ();

        $this->addToolbar();
        $this->render_sidebar ();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        jimport( 'joomla.version' );
        $jversion = new JVersion();
        $joomla_short_version = $jversion->getShortVersion();

        if (version_compare($joomla_short_version, "4.0.0-beta1") <  0)
        {
            $this->addToolbar3 ();
            return;
        }

        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAILINGLIST_TITLE'), 'mailinglist');

        $toolbar = Toolbar::getInstance('toolbar');

        $dropdown = $toolbar->dropdownButton('status-group')
            ->text('JTOOLBAR_CHANGE_STATUS')
            ->toggleSplit(false)
            ->icon('fas fa-ellipsis-h')
            ->buttonClass('btn btn-action')
            ->listCheck(true);

        $childBar = $dropdown->getChildToolbar();

        $childBar->standardButton('students_publish')
            ->text('COM_JOOMDLE_CREATE_STUDENT_LIST')
            ->task('mailinglist.students_publish')
            ->icon('fas fa-check')
            ->listCheck(true);

        $childBar->standardButton('students_unpublish')
            ->text('COM_JOOMDLE_DELETE_STUDENT_LIST')
            ->task('mailinglist.students_unpublish')
            ->icon('fas fa-times')
            ->listCheck(true);

        $childBar->standardButton('teachers_publish')
            ->text('COM_JOOMDLE_CREATE_TEACHER_LIST')
            ->task('mailinglist.teachers_publish')
            ->icon('fas fa-check')
            ->listCheck(true);

        $childBar->standardButton('teachers_unpublish')
            ->text('COM_JOOMDLE_DELETE_TEACHER_LIST')
            ->task('mailinglist.teachers_unpublish')
            ->icon('fas fa-times')
            ->listCheck(true);

        $childBar->standardButton('parents_publish')
            ->text('COM_JOOMDLE_CREATE_PARENT_LIST')
            ->task('mailinglist.parents_publish')
            ->icon('fas fa-check')
            ->listCheck(true);

        $childBar->standardButton('parents_unpublish')
            ->text('COM_JOOMDLE_DELETE_PARENT_LIST')
            ->task('mailinglist.parents_unpublish')
            ->icon('fas fa-times')
            ->listCheck(true);

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=mailinglist');
    }

    private function render_sidebar ()
    {
        jimport( 'joomla.version' );
        $jversion = new JVersion();
        $joomla_short_version = $jversion->getShortVersion();

        if (version_compare($joomla_short_version, "4.0.0-beta1") <  0)
            $this->sidebar = JHtmlSidebar::render();
    }

    protected function addToolbar3()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAILINGLIST_TITLE'), 'mailinglist');

        JToolbarHelper::publish('mailinglist.students_publish', 'COM_JOOMDLE_CREATE_STUDENT_LIST', true);
        JToolbarHelper::unpublish('mailinglist.students_unpublish', 'COM_JOOMDLE_DELETE_STUDENT_LIST', true);

        JToolbarHelper::publish('mailinglist.teachers_publish', 'COM_JOOMDLE_CREATE_TEACHER_LIST', true);
        JToolbarHelper::unpublish('mailinglist.teachers_unpublish', 'COM_JOOMDLE_DELETE_TEACHER_LIST', true);

        JToolbarHelper::publish('mailinglist.parents_publish', 'COM_JOOMDLE_CREATE_PARENT_LIST', true);
        JToolbarHelper::unpublish('mailinglist.parents_unpublish', 'COM_JOOMDLE_DELETE_PARENT_LIST', true);

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=mailinglist');
    }

}
?>
