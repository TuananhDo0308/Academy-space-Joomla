<?php 
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die('Restricted access'); ?>
<?php
$linkto = $this->params->get( 'mycourses_linkto' );
$default_itemid = $this->params->get( 'default_itemid' );
$joomdle_itemid = $this->params->get( 'joomdle_itemid' );
$courseview_itemid = $this->params->get( 'courseview_itemid' );
if ($linkto == 'moodle')
{
    if ($default_itemid)
        $itemid = $default_itemid;
    else
        $itemid = JoomdleHelperContent::getMenuItem();
}
else if ($linkto == 'detail')
{
        $itemid = JoomdleHelperContent::getMenuItem();

        if ($joomdle_itemid)
            $itemid = $joomdle_itemid;
}
else
{
        $itemid = JoomdleHelperContent::getMenuItem();

        if ($joomdle_itemid)
            $itemid = $joomdle_itemid;
        if ($courseview_itemid)
            $itemid = $courseview_itemid;
}

$linkstarget = $this->params->get( 'linkstarget' );
if ($linkstarget == "new")
     $target = " target='_blank'";
 else $target = "";

$show_unenrol_link = $this->params->get( 'show_unenrol_link' );
$show_images_and_summary = $this->params->get( 'show_images_and_summary' );

$unicodeslugs = JFactory::getConfig()->get('unicodeslugs');
?>

<div class="joomdle-mycourses<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
    <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

    <div class="barContainer">
    <?php
    $lang = JoomdleHelperContent::get_lang ();
    if (is_array ($this->my_courses))
        foreach ($this->my_courses as $id => $curso) :  ?>
            <?php
                if ($unicodeslugs == 1)
                {
                    $slug = JFilterOutput::stringURLUnicodeSlug($curso['fullname']);
                }
                else
                {
                    $slug = JFilterOutput::stringURLSafe($curso['fullname']);
                }
            ?>
            <div class="barRowx">
                <?php
                    if ($linkto == 'moodle')
                    {
                        $link = $this->jump_url."&mtype=course&id=".$curso['id']."&Itemid=$itemid";
                        if ($lang)
                            $link .= "&lang=$lang";

                        $course_link =  "<a $target href=\"$link\">".$curso['fullname']."</a>";
                    }
                    else if ($linkto == 'detail')
                    {
                        // Link to detail view
                        $redirect_url = JRoute::_("index.php?option=com_joomdle&view=detail&course_id=".$curso['id'].'-'.$slug.
                            "&Itemid=$itemid");
                        $course_link =  "<a href=\"".$redirect_url."\">".$curso['fullname']."</a>";
                    }
                    else
                    {
                        // Link to course view
                        $redirect_url = JRoute::_("index.php?option=com_joomdle&view=course&course_id=".$curso['id'].'-'.$slug.
                            "&Itemid=$itemid");
                        $course_link =  "<a href=\"".$redirect_url."\">".$curso['fullname']."</a>";
                    }

                    echo $course_link;
            ?>  
            </div>
            <div class="barRow">
            <?php
                echo $this->get_course_completion_progress ($curso['progress'], $id);
            ?>
            </div>
            <div><br></div>
        <?php endforeach; ?>
    </div>
    </div>
</div>
