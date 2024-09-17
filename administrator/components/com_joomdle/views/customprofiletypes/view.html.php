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
require_once( JPATH_COMPONENT.'/helpers/profiletypes.php' );

class JoomdleViewCustomprofiletypes extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null) {

        $mainframe = JFactory::getApplication();
        $params = JComponentHelper::getParams( 'com_joomdle' );

        if (!$params->get( 'use_profiletypes' ))
        {
            JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_PROFILETYPES_TITLE'), 'customprofiletypes');
            $this->message = JText::_('COM_JOOMDLE_PROFILE_TYPES_INTEGRATION_NOT_ENABLED');
            $tpl = "disabled";
            parent::display($tpl);
            return;
        }

        /* List of profiletypes */
        $this->profiletypes   = $this->get('Items');
        $this->pagination   = $this->get('Pagination');
        $this->state        = $this->get('State');

        $this->addToolbar();
        $this->render_sidebar ();
        parent::display($tpl);
    }

    protected function addToolbar()
    {

        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_PROFILETYPES_TITLE'), 'customprofiletypes');

        JToolBarHelper::custom( 'create_profiletype_on_moodle', 'publish', 'publish', 'COM_JOOMDLE_CREATE_ON_MOODLE', true, false );
        JToolBarHelper::custom( 'dont_create_profiletype_on_moodle', 'unpublish', 'unpublish', 'COM_JOOMDLE_NOT_CREATE_ON_MOODLE', true, false );

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=customprofiletypes');

        JHtmlSidebar::addFilter(
            JText::_('COM_JOOMDLE_SELECT_STATE'),
            'filter_state',
            JHtml::_('select.options',  JoomdleHelperProfiletypes::getStateOptions(), 'value', 'text', $this->state->get('filter.state'))
        );

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
