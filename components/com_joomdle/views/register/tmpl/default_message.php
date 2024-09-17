<?php 
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="componentheading">
    <?php echo $this->escape($this->message->title) ; ?>
</div>

<div class="message">
    <?php echo $this->escape($this->message->text) ; ?>
</div>
