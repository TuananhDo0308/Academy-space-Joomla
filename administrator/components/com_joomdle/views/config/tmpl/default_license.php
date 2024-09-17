<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die;
?>
<fieldset class="form-horizontal">
    <legend><?php echo JText::_('COM_JOOMDLE_LICENSE'); ?></legend>
    <?php
    foreach ($this->form->getFieldset('license') as $field):
    ?>
        <div class="control-group">
            <div class="control-label"><?php echo $field->label; ?></div>
            <div class="controls"><?php echo $field->input; ?></div>
        </div>
    <?php
    endforeach;
    ?>
</fieldset>
<?php echo JText::_ ('COM_JOOMDLE_LICENSED_REQUIRED_FOR'); ?>
