<?php
/**
 * @package      Joomdle
 * @copyright    Qontori Pte Ltd
 * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewMenteescertificates extends JViewLegacy {
    function display($tpl = null) {
        global $mainframe;

        $app        = JFactory::getApplication();
        $this->params = $app->getParams();

        $this->moodle_url = $this->params->get( 'MOODLE_URL' );

        $this->show_send_certificate = $this->params->get('show_send_certificate');
        $this->cert_type = $this->params->get('certificate_type', 'normal');

        $user = JFactory::getUser();
        $username = $user->username;
        $this->mentees = JoomdleHelperContent::call_method ("get_mentees_certificates", $username, $this->cert_type);

        $this->_prepareDocument();

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
            $this->params->def('page_heading', JText::_('COM_JOOMDLE_MENTEES_CERTIFICATES'));
        }
    }

}
?>
