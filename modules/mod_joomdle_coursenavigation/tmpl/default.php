<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$itemid = JoomdleHelperContent::getMenuItem();
if ($joomdle_itemid)
	$itemid = $joomdle_itemid;
if ($courseview_itemid)
	$itemid = $courseview_itemid;

$client_lang = '';
?>
	<ul class="joomdlecoursenavigation<?php echo $moduleclass_sfx; ?>">
<?php if ($params->get( 'show_coursecontents', 1)) :?>
		<li>
<?php $url = JRoute::_("index.php?option=com_joomdle&view=course&course_id=$course_id&itemid=$itemid"); ?>
		<a href='<?php echo $url; ?>'>
		<?php 	echo JText::_ ('COM_JOOMDLE_COURSE_CONTENTS'); ?>
		</a>
		</li>
<?php endif; ?>

<?php if ($params->get( 'show_coursewrapper', 1)) :?>
		<li>
<?php $url = ("index.php?option=com_joomdle&view=wrapper&moodle_page_type=course&id=$course_id&itemid=$default_itemid"); ?>
		<a href='<?php echo $url; ?>'>
		<?php 	echo JText::_ ('COM_JOOMDLE_COURSE'); ?>
		</a>
		</li>
<?php endif; ?>

<?php if ($params->get( 'show_coursemates', 1)) :?>
		<li>
<?php $url = JRoute::_("index.php?option=com_joomdle&view=coursemates&course_id=$course_id&itemid=$itemid"); ?>
		<a href='<?php echo $url; ?>'>
		<?php 	echo JText::_ ('COM_JOOMDLE_COURSE_MATES'); ?>
		</a>
		</li>
<?php endif; ?>

<?php if ($params->get( 'show_coursenews', 1)) :?>
		<li>
<?php $url = JRoute::_("index.php?option=com_joomdle&view=coursenews&course_id=$course_id&itemid=$itemid"); ?>
		<a href='<?php echo $url; ?>'>
		<?php 	echo JText::_ ('COM_JOOMDLE_COURSE_NEWS'); ?>
		</a>
		</li>
<?php endif; ?>

<?php if ($params->get( 'show_courseevents', 1)) :?>
		<li>
<?php $url = JRoute::_("index.php?option=com_joomdle&view=courseevents&course_id=$course_id&itemid=$itemid"); ?>
		<a href='<?php echo $url; ?>'>
		<?php 	echo JText::_ ('COM_JOOMDLE_COURSE_EVENTS'); ?>
		</a>
		</li>
<?php endif; ?>

<?php if ($params->get( 'show_coursegrades', 1)) :?>
		<li>
<?php $url = JRoute::_("index.php?option=com_joomdle&view=coursegrades&course_id=$course_id&itemid=$itemid"); ?>
		<a href='<?php echo $url; ?>'>
		<?php 	echo JText::_ ('COM_JOOMDLE_COURSE_GRADES'); ?>
		</a>
		</li>
<?php endif; ?>


<?php if ($params->get( 'show_coursegroup', 0)) :?>
<?php $group_id = JoomdleHelperSocialGroups::get_group_by_course_id ( (int) $course_id);
	if ($group_id) : ?>
		<li>
	<?php 
		$url = JoomdleHelperSocialGroups::get_group_url ($group_id);
	?>
		<a href='<?php echo $url; ?>'>
		<?php 	echo JText::_ ('COM_JOOMDLE_COURSE_GROUP'); ?>
		</a>
		</li>
	<?php endif; ?>
<?php endif; ?>
	</ul>
