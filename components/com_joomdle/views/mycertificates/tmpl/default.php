<?php 
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-mycertificates<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>


    <div class="joomdle_mycertificates">
    <ul>
    <?php
    if (is_array ($this->my_certificates))
        foreach ($this->my_certificates as $cert) :  ?>
            <li>
                <?php
                    $id = $cert['id'];

                    switch ($this->cert_type)
                    {
                        case 'simple':
                            $redirect_url = $this->moodle_url."/mod/simplecertificate/view.php?id=$id&certificate=1&action=review";
                            break;
                        case 'custom':
                            $redirect_url = $this->moodle_url."/mod/customcert/view.php?id=$id&downloadissue=1";
                            break;
                        case 'coursecertificate':
                            $redirect_url = $this->moodle_url . "/admin/tool/certificate/view.php?code=" . $cert['code'];
                            break;
                        default:
                            $redirect_url = $this->moodle_url."/mod/certificate/view.php?id=$id&certificate=1&action=review";
                            break;

                    }
                ?>
                <span>
                    <a target='_blank' href="<?php echo $redirect_url; ?>"><?php echo $cert['name']; ?></a>
                    <?php if ($this->show_send_certificate) : ?>
                        <a href="index.php?option=com_joomdle&view=sendcert&tmpl=component&cert_type=<?php echo $this->cert_type; ?>&cert_id=<?php echo $id; ?>" onclick="window.open(this.href,'win2','width=400,height=350,menubar=yes,resizable=yes'); return false;" title="Email"><img alt="Email" src="media/system/images/emailButton.png"></a>
                <?php endif; ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
    </div>
</div>
