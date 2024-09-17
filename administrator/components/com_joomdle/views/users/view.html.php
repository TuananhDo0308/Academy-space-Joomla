<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

//jimport( 'joomla.application.component.view');
require_once( JPATH_COMPONENT.'/helpers/content.php' );
require_once( JPATH_COMPONENT.'/helpers/users.php' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class JoomdleViewUsers extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;


    function display($tpl = null) {
        $this->users   = $this->get('Items');
        $this->pagination   = $this->get('Pagination');
        $this->state        = $this->get('State');

        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

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

        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_USERS_TITLE'), 'user');

        $toolbar = Toolbar::getInstance('toolbar');
        $toolbar->standardButton('add_to_joomla')
            ->text('COM_JOOMDLE_ADD_USERS_TO_JOOMLA')
            ->task('add_to_joomla')
            ->icon('fas fa-plus')
            ->listCheck(true);
        $toolbar->standardButton('add_to_moodle')
            ->text('COM_JOOMDLE_ADD_USERS_TO_MOODLE')
            ->task('add_to_moodle')
            ->icon('fas fa-plus')
            ->listCheck(true);
        $toolbar->standardButton('migrate_to_joomdle')
            ->text('COM_JOOMDLE_MIGRATE_USERS_TO_JOOMDLE')
            ->task('migrate_to_joomdle')
            ->icon('fas fa-check')
            ->listCheck(true);

        $dropdown = $toolbar->dropdownButton('status-group')
            ->text('JTOOLBAR_CHANGE_STATUS')
            ->toggleSplit(false)
            ->icon('fas fa-ellipsis-h')
            ->buttonClass('btn btn-action')
            ->listCheck(true);

        $childBar = $dropdown->getChildToolbar();

        $childBar->standardButton('sync_profile_to_moodle')
            ->text('COM_JOOMDLE_SYNC_MOODLE_PROFILES')
            ->task('sync_profile_to_moodle')
            ->icon('fas fa-forward')
            ->listCheck(true);
        $childBar->standardButton('sync_profile_to_joomla')
            ->text('COM_JOOMDLE_SYNC_JOOMLA_PROFILES')
            ->task('sync_profile_to_joomla')
            ->icon('fas fa-forward')
            ->listCheck(true);
        $childBar->standardButton('sync_parents_from_moodle')
            ->text('COM_JOOMDLE_SYNC_PARENTS_FROM_MOODLE')
            ->task('sync_parents_from_moodle')
            ->icon('fas fa-forward')
            ->listCheck(true);

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=users');
    }

    // Needed because id function in /libraries/src/HTML/Helpers/Grid.php does not provide a way to disable checkbox
    // We cannot just not show it like we did before, because the clickable table messes up IDs
    function get_disabled_checkbox ($rowNum, $recId, $checkedOut = false, $name = 'cid', $stub = 'cb', $title = '', $formId = null)
    {
        return '<label for="' . $stub . $rowNum . '"><span class="sr-only">' . Text::_('JSELECT')
                    . ' ' . htmlspecialchars($title, ENT_COMPAT, 'UTF-8') . '</span></label>'
                    . '<input autocomplete="off" type="checkbox" id="' . $stub . $rowNum . '" name="' . $name . '[]" value="' . $recId
                    . '" disabled="disabled" onclick="Joomla.isChecked(this.checked);">';
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

        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_USERS_TITLE'), 'user');

        JToolBarHelper::custom( 'add_to_joomla', 'new', 'new', 'COM_JOOMDLE_ADD_USERS_TO_JOOMLA', true, false );
        JToolBarHelper::custom( 'add_to_moodle', 'new', 'new', 'COM_JOOMDLE_ADD_USERS_TO_MOODLE', true, false );
        JToolBarHelper::custom( 'migrate_to_joomdle', 'checkin', 'checkin', 'COM_JOOMDLE_MIGRATE_USERS_TO_JOOMDLE', true, false );
        JToolBarHelper::custom( 'sync_profile_to_moodle', 'forward', 'forward', 'COM_JOOMDLE_SYNC_MOODLE_PROFILES', true, false );
        JToolBarHelper::custom( 'sync_profile_to_joomla', 'forward', 'forward', 'COM_JOOMDLE_SYNC_JOOMLA_PROFILES', true, false );
        JToolBarHelper::custom( 'sync_parents_from_moodle', 'forward', 'forward', 'COM_JOOMDLE_SYNC_PARENTS_FROM_MOODLE', true, false );

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=users');
    }

}
?>
