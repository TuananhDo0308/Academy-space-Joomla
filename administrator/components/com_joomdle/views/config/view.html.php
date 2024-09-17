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
class JoomdleViewConfig extends JViewLegacy {
    function display($tpl = null) {

        $form   = $this->get('Form');
        $data   = $this->get('Data');
        $user = JFactory::getUser();

        $this->fieldsets   = $form ? $form->getFieldsets() : null;
        $this->formControl = $form ? $form->getFormControl() : null;

        // Don't use sef_advanced field if Joomla version > 4
        jimport( 'joomla.version' );
        $jversion = new JVersion();
        $joomla_short_version = $jversion->getShortVersion();
        if (version_compare($joomla_short_version, "4.0.0-beta1") >=  0)
            $form->removeField ('sef_advanced');

        // Check for model errors.
        if ($errors = $this->get('Errors')) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Bind the form to the data.
        if ($form && $data) {
            $form->bind($data);
        }

        $this->form   = &$form;

        $this->addToolbar();
        $this->render_sidebar ();

        if (version_compare($joomla_short_version, "4.0.0-beta1") <  0)
            $tpl = '3x';
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_JOOMDLE_VIEW_MAILINGLIST_TITLE_CONFIGURATION'), 'config.png');
        JToolbarHelper::apply('config.apply');
        JToolbarHelper::save('config.save');
        JToolbarHelper::cancel('config.cancel');
        JToolBarHelper::custom( 'config.regenerate_joomla_token', 'refresh', 'refresh', 'COM_JOOMDLE_REGENERATE_JOOMLA_TOKEN', false );

        JHtmlSidebar::setAction('index.php?option=com_joomdle&view=config');
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
