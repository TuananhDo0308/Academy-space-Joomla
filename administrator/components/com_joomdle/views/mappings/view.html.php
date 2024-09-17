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

class JoomdleViewMappings extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;


    function display($tpl = null) {

        $this->items   = $this->get('Items');
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
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAPPINGS_TITLE'), 'mapping');

        JToolbarHelper::addNew('mapping.add');
        JToolbarHelper::deleteList('', 'mappings.delete');

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=mappings');
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
