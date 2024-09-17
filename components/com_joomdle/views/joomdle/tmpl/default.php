<?php 
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
$free_courses_button = $this->params->get( 'free_courses_button' );
$paid_courses_button = $this->params->get( 'paid_courses_button' );
$show_buttons = $this->params->get( 'show_buttons' );
$show_description = $this->params->get( 'show_description' );

$unicodeslugs = JFactory::getConfig()->get('unicodeslugs');
?>
<div class="joomdle-courselist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>


    <?php
    if (is_array ($this->cursos))
    foreach ($this->cursos as  $curso) : ?>
    <?php
    $cat_id = $curso['cat_id'];
    $course_id = $curso['remoteid'];
    if ($unicodeslugs == 1)
    {
        $course_slug = JFilterOutput::stringURLUnicodeSlug($curso['fullname']);
        $cat_slug = JFilterOutput::stringURLUnicodeSlug($curso['cat_name']);
    }
    else
    {
        $course_slug = JFilterOutput::stringURLSafe($curso['fullname']);
        $cat_slug = JFilterOutput::stringURLSafe($curso['cat_name']);
    }

    ?>
    <div class="joomdle_course_list_item">
    <?php if ($show_description) : ?>
        <div class="joomdle_item_title joomdle_course_list_item_title">
    <?php else : ?>
        <div class="joomdle_item_full joomdle_course_list_item_title">
    <?php endif; ?>
            <?php //$url = JRoute::_("index.php?option=com_joomdle&view=detail&cat_id=$cat_id-$cat_slug&course_id=$course_id-$course_slug&Itemid=$itemid"); ?>
            <?php $url = JRoute::_("index.php?option=com_joomdle&view=detail&course_id=$course_id-$course_slug"); ?>
            <?php  echo "<a href=\"$url\">".$curso['fullname']."</a><br>"; ?>
        </div>
        <?php if (($show_description) && ($curso['summary'])) : ?>
        <div class="joomdle_item_content joomdle_course_list_item_description">
            <div class="joomdle_course_description">
            <?php 
                if (count ($curso['summary_files']))
                {
                    foreach ($curso['summary_files'] as $file) :
                    ?>
                        <img hspace="5" vspace="5" align="left" src="<?php echo $file['url']; ?>">
                    <?php
                    endforeach;
                }
            ?>
            <?php   echo JoomdleHelperSystem::fix_text_format($curso['summary']); ?>
            </div>
            <?php if ($show_buttons) : ?>
            <div class="joomdle_course_buttons">
                <?php
                    echo JoomdleHelperSystem::actionbutton ( $curso, $free_courses_button, $paid_courses_button);
                ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
