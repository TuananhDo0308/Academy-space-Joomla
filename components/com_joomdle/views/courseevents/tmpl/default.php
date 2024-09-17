<?php 
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-eventlist<?php echo $this->pageclass_sfx;?>">
    <h1>
        <?php echo $this->course_info['fullname'] . ': '; ?>
        <?php echo JText::_('COM_JOOMDLE_COURSE_EVENTS'); ?>
    </h1>

<?php
$lang = JoomdleHelperContent::get_lang ();
foreach ($this->events as  $event) : ?>
    <div class="joomdle_event_list_item">
        <div class="joomdle_item_title joomdle_event_list_item_date">
          <?php

            $linkstarget = $this->params->get( 'linkstarget' );
            if ($linkstarget == "new")
                     $target = " target='_blank'";
             else $target = "";

            $link = $this->jump_url."&mtype=event&id=".$event['courseid']."&time=" . $event['timestart'];
            if ($lang)
                $link .= "&lang=$lang";

            echo "<a $target href=\"$link\">".JHTML::_('date', $event['timestart'] , JText::_('DATE_FORMAT_LC2'))."</a>";

                ?>
        </div>
        <div class="joomdle_item_content joomdle_event_list_item_name">
                <?php echo $event['name']; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>
