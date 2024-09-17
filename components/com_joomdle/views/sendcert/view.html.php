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
class JoomdleViewSendcert extends JViewLegacy {
    function display($tpl = null) {

    $app        = JFactory::getApplication();
    $this->params = $app->getParams();

    $cert_id = $app->input->get('cert_id');
    $this->cert_type =  $app->input->get('cert_type');

    $data = $this->getData();
    if ($data === false) {
            return false;
        }

    $this->set('data'  , $data);


    parent::display($tpl);
    }

    function &getData()
    {
        $user = JFactory::getUser();
        $data = new stdClass();

        $app        = JFactory::getApplication();
        $data->cert_id = $app->input->get('cert_id');

        // Load with previous data, if it exists
        $mailto = $app->input->get('mailto');
        $sender = $app->input->get('sender');
        $from = $app->input->get('from');
        $subject = $app->input->get('subject');

        if ($user->get('id') > 0) {
            $data->sender   = $user->get('name');
            $data->from     = $user->get('email');
        }
        else
        {
            $data->sender   = $sender;
            $data->from     = $from;
        }

        $data->subject  = $subject;
        $data->mailto   = $mailto;

        return $data;
    }

}
?>
