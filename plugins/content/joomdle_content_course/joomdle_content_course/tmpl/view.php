<?php defined('_JEXEC') or die('Restricted access');
//JHTML::_('stylesheet', 'tienda.css', 'media/com_tienda/css/');
$item = @$course_info;
$course_id =  $item['remoteid'];

$comp_params = JComponentHelper::getParams( 'com_joomdle' );
$moodle_url = $comp_params->get( 'MOODLE_URL' );
$free_courses_button = $comp_params->get( 'free_courses_button' );
$paid_courses_button = $comp_params->get( 'paid_courses_button' );

$linkstarget = $comp_params->get( 'linkstarget' );

if ($linkstarget == 'wrapper')
    $open_in_wrapper = 1;
else
    $open_in_wrapper = 0;

if ($linkstarget == "new")
    $target = " target='_blank'";
else $target = "";

?>  
<div class="joomdle-coursedetails">
		<div class="joomdle_course_name">
                <?php if (@$params['link_to'] == 'detail') : ?>
					<a href='index.php?option=com_joomdle&view=detail&course_id=<?php echo $item['remoteid']; ?>'><?php echo $item['fullname'];  ?></a>
                <?php elseif (@$params['link_to'] == 'course') : ?>
				<?php
					$itemid = $comp_params->get( 'courseview_itemid' );
					$url = JRoute::_("index.php?option=com_joomdle&view=course&course_id=".$item['remoteid'].':'.JFilterOutput::stringURLSafe($item['fullname'])."&Itemid=$itemid");
				?>
					<a  href='<?php echo $url; ?>'><?php echo $item['fullname'];  ?></a>
                <?php elseif (@$params['link_to'] == 'moodle') : ?>
				<?php
					$itemid = $comp_params->get( 'default_itemid' );
					if ($open_in_wrapper)
						$url = 'index.php?option=com_joomdle&view=wrapper&moodle_page_type=course&id='.$item['remoteid'] . "&Itemid=$itemid";
					else
						$url = $moodle_url . '/course/view.php?id='. $item['remoteid'];
				?>
					<a <?php echo $target; ?> href='<?php echo $url; ?>'><?php echo $item['fullname'];  ?></a>
                <?php 
				else :
                	echo $item['fullname']; 
				endif;
                ?>
		</div>
      
			<?php if (@$params['show_description']) : ?>
			<div class="joomdle_course_description">
					<?php echo $item['summary']; ?>
			</div>
			<?php endif; ?>

			<?php if ((@$params['show_language']) && ($course_info['lang'])) : ?>
			<div class="joomdle_course_language">
					<b><?php echo JText::_('COM_JOOMDLE_LANGUAGE'); ?>:&nbsp;</b><?php echo JoomdleHelperContent::get_language_str ($course_info['lang']); ?>
			</div>
			<?php endif; ?>

			<?php if ((@$params['show_startdate']) && ($course_info['startdate'])) : ?>
			<div class="joomdle_course_startdate">
					<b><?php echo JText::_('COM_JOOMDLE_START_DATE'); ?>:&nbsp;</b>
					<?php echo JHTML::_('date', $course_info['startdate'] , JText::_('DATE_FORMAT_LC')); ?>
			</div>
			<?php endif; ?>

			<?php if ((@$params['show_enroldates']) && ($course_info['enrolstartdate'])) : ?>
			<div class="joomdle_course_enrolstartdate">
					<b><?php echo JText::_('COM_JOOMDLE_ENROLMENT_START_DATE'); ?>:&nbsp;</b>
					<?php echo JHTML::_('date', $course_info['enrolstartdate'] , JText::_('DATE_FORMAT_LC')); ?>
			</div>
			<?php endif; ?>

			<?php if ((@$params['show_enroldates']) && ($course_info['enrolenddate'])) : ?>
			<div class="joomdle_course_enrolenddate">
					<b><?php echo JText::_('COM_JOOMDLE_ENROLMENT_END_DATE'); ?>:&nbsp;</b>
					<?php echo JHTML::_('date', $course_info['enrolenddate'] , JText::_('DATE_FORMAT_LC')); ?>
			</div>
			<?php endif; ?>

			<?php if ((@$params['show_enrolperiod']) && (array_key_exists ('enrol_period', $course_info))) : ?>
			<div class="joomdle_course_enrolperiod">
					<b><?php echo JText::_('COM_JOOMDLE_ENROLMENT_DURATION'); ?>:&nbsp;</b>
				<?php
				if ($course_info['enrolperiod'] == 0)
					echo JText::_('COM_JOOMDLE_UNLIMITED');
				else
					echo ($course_info['enrolperiod'] / 86400)." ".JText::_('COM_JOOMDLE_DAYS');
				?>
			</div>
			<?php endif; ?>

			<?php if ((@$params['show_cost']) && ($course_info['cost'])) : ?>
			<div class="joomdle_course_enrolperiod">
					<b><?php echo JText::_('COM_JOOMDLE_COST'); ?>:&nbsp;</b>
					<?php echo $item['cost']." (".$course_info['currency'].")"; ?>
			</div>
			<?php endif; ?>

			<?php if (@$params['show_topicsnumber']) : ?>
			<div class="joomdle_course_topicsnumber">
					<b><?php echo JText::_('COM_JOOMDLE_TOPICS'); ?>:&nbsp;</b>
					<?php echo $item['numsections']; ?>
			</div>
			<?php endif; ?>

			 <div class="joomdle_course_buttons">
			         <?php echo JoomdleHelperSystem::actionbutton ( $course_info, $free_courses_button, $paid_courses_button, @$params['override_button_text']); ?>
			</div>
</div>
