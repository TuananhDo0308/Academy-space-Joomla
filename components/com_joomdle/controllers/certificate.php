<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/content.php');

class JoomdleControllerCertificate extends JControllerLegacy
{

    function send_certificate ()
    {
        $app                = JFactory::getApplication();
        $params = $app->getParams();
        $moodle_url = $params->get( 'MOODLE_URL' );

        $cert_id   = $this->input->get('cert_id');
        $cert_type   = $this->input->get('cert_type');

        $email   = $this->input->get('mailto', '', NULL);
        $sender   = $this->input->get('sender');
        $from   = $this->input->get('from');
        $user = JFactory::getUser();
        $username = $user->username;

        $subject_default    = JText::sprintf('COM_JOOMDLE_CERTIFICATE_EMAIL_SUBJECT', $user->name);
        $subject   = $this->input->get('subject', $subject_default);
        if (!$subject)
            $subject = $subject_default;

        $mailer = JFactory::getMailer();

        $config = JFactory::getConfig();
        $sender = array(
        $config->get( 'mailfrom' ),
        $config->get( 'fromname' ) );
    
        $mailer->setSender($sender);
        $mailer->addRecipient($email);

        $body   = JText::sprintf('COM_JOOMDLE_CERTIFICATE_EMAIL_BODY', $user->name);
        $mailer->setSubject($subject);
        $mailer->setBody($body);

        $session                = JFactory::getSession();
        $token = md5 ($session->getId());

        switch ($cert_type)
        {
            case "simple":
                $url = $moodle_url.'/auth/joomdle/simplecertificate_view.php?id='.$cert_id.'&certificate=1&action=review&username='.$username.'&token='.$token;
                break;
            case "custom":
                $url = $moodle_url.'/auth/joomdle/customcert_view.php?id='.$cert_id.'&action=download&username='.$username.'&token='.$token;
                break;
            case "normal":
            default:
                $url = $moodle_url.'/auth/joomdle/certificate_view.php?id='.$cert_id.'&certificate=1&action=review&username='.$username.'&token='.$token;
                break;
        }
        $pdf =file_get_contents ($url);
        $tmp_path = $config->get('tmp_path');
        $filename = 'certificate-'.$cert_id.'-'.$user->name.'.pdf';
        file_put_contents ($tmp_path.'/'.$filename, $pdf);
        $mailer->addAttachment($tmp_path.'/'.$filename);

        $send = $mailer->Send();
        unlink ($tmp_path.'/'.$filename);
        if ( $send !== true ) {
            $error = JText::_( 'COM_JOOMDLE_EMAIL_NOT_SENT' );
            JFactory::getApplication()->enqueueMessage($error, 'notice');
        } else {
?>
<div style="padding: 10px;">
    <div style="text-align:right">
        <a href="javascript: void window.close()">
            <?php echo JText::_('COM_JOOMDLE_CLOSE_WINDOW'); ?> <?php echo JHtml::_('image','mailto/close-x.png', NULL, NULL, true); ?></a>
    </div>

    <h2>
        <?php echo JText::_('COM_JOOMDLE_EMAIL_SENT'); ?>
    </h2>
</div>

<?php
        }

    }
}
