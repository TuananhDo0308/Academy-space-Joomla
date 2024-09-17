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

class JoomdleViewCustomprofiletype extends JViewLegacy {

    protected $form;

    protected $item;

    function display($tpl = null) {
        global $mainframe, $option;

        $this->form         = $this->get('Form');
        $this->item         = $this->get('Item');

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

        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_PROFILETYPES_TITLE'), 'customprofiletype');
        JToolbarHelper::apply('customprofiletype.apply');
        JToolbarHelper::save('customprofiletype.save');

        if (empty($this->item->id))  {
            JToolbarHelper::cancel('customprofiletype.cancel');
        } else {
            JToolbarHelper::cancel('customprofiletype.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}

?>
