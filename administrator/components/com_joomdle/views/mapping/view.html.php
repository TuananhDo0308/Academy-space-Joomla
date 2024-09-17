<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');
// Import Joomla! libraries
//jimport( 'joomla.application.component.view');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;


require_once( JPATH_COMPONENT.'/helpers/content.php' );
require_once( JPATH_COMPONENT.'/helpers/mappings.php' );

class JoomdleViewMapping extends JViewLegacy {

    protected $form;

    protected $item;

    function display($tpl = null) {

        $this->form         = $this->get('Form');
        $this->item         = $this->get('Item');

        $params = JComponentHelper::getParams( 'com_joomdle' );
        $additional_data_source = $params->get( 'additional_data_source' );

        if ($additional_data_source == 'no')
        {
            echo JText::_ ('COM_JOOMDLE_YOU_NEED_TO_SELECT_AN_ADDITIONAL_DATA_SOURCE');
            return;
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        parent::display($tpl);
        $this->addToolbar();

    }

    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        ToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAPPINGS_TITLE'), 'mapping');
        ToolbarHelper::apply('mapping.apply');
        ToolbarHelper::save('mapping.save');

        if (empty($this->item->id))  {
            ToolbarHelper::cancel('mapping.cancel');
        } else {
            ToolbarHelper::cancel('mapping.cancel', 'JTOOLBAR_CLOSE');
        }
    }

}
?>
