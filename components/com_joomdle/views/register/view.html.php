<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Registration component
 *
 * @package     Joomla
 * @subpackage  Registration
 * @since 1.0
 */
class JoomdleViewRegister extends JViewLegacy
{
    function display($tpl = null)
    {
        // Check if registration is allowed
        $usersConfig =JComponentHelper::getParams( 'com_users' );
        if (!$usersConfig->get( 'allowUserRegistration' )) {
            echo JText::_ ('COM_JOOMDLE_USER_REGISTRATION_DISABLED');
            return;
        }

        $app                = JFactory::getApplication();

        $user = JFactory::getUser ();
        $this->user = $user;

        // Redirect if not logged in
        if (!$user->id)
        {
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
            return false;
        }

        $pathway = $app->getPathWay();
        $document = JFactory::getDocument();

        $this->params = $app->getParams();

        // Load the form validation behavior
      //  JHTML::_('behavior.formvalidation');

        $this->_prepareDocument();

        // Load com_users lang file
		$lang = JFactory::getLanguage();
		$extension = 'com_users';
		$current_lang = JFactory::getLanguage();
        $language_tag = $current_lang->getTag ();
		$base_dir = JPATH_SITE;
		$reload = true;
		$lang->load($extension, $base_dir, $language_tag, $reload);

        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $title  = null; 

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu)
        {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_CHILDREN_REGISRATION'));
        }
    }

}
