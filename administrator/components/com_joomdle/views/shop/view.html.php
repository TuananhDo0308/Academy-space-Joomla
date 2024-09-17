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
require_once( JPATH_COMPONENT.'/helpers/shop.php' );
require_once( JPATH_COMPONENT.'/helpers/groups.php' );

class JoomdleViewShop extends JViewLegacy {
    function display($tpl = null) {

        $params = JComponentHelper::getParams( 'com_joomdle' );

        if ($params->get( 'shop_integration' ) == 'no')
        {
            JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_SHOP_TITLE'), 'shop');
            $this->message = JText::_('COM_JOOMDLE_SHOP_INTEGRATION_NOT_ENABLED');
            $tpl = "disabled";
            parent::display($tpl);
            return;
        }

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
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_SHOP_TITLE'), 'shop');

        JToolBarHelper::addNew('bundle.add', 'COM_JOOMDLE_NEW_BUNDLE');
        JToolbarHelper::publish('shop.publish', 'JTOOLBAR_PUBLISH', true);
        JToolbarHelper::unpublish('shop.unpublish', 'JTOOLBAR_UNPUBLISH', true);

        JToolBarHelper::custom( 'shop.reload', 'refresh', 'refresh', 'COM_JOOMDLE_RELOAD_FROM_MOODLE', true, false );
        JToolBarHelper::trash('shop.delete_courses_from_shop');


        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=shop');
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
