<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die;
?>
<fieldset class="form-horizontal options-menu options-form">
    <legend><?php echo JText::_('COM_JOOMDLE_ACTION_BUTTONS'); ?></legend>
    <?php
    foreach ($this->form->getFieldset('actionbuttons') as $field):
    ?>
        <div class="control-group">
            <div class="control-label"><?php echo $field->label; ?></div>
            <div class="controls"><?php echo $field->input; ?></div>
        </div>
    <?php
    endforeach;
    ?>
</fieldset>
