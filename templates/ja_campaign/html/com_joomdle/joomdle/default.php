<?php 
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die('Restricted access');

$itemid = JoomdleHelperContent::getMenuItem();
$free_courses_button = $this->params->get( 'free_courses_button' );
$paid_courses_button = $this->params->get( 'paid_courses_button' );
$show_buttons = $this->params->get( 'show_buttons' );
$show_description = $this->params->get( 'show_description' );

$unicodeslugs = JFactory::getConfig()->get('unicodeslugs');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joomdle Course List</title>
    <style>
        .joomdle-courselist {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            padding: 20px;
        }

        .joomdle_course_list_item {
        height: 300px;
        width: 400px;
        flex: 0 0 calc(33.33% - 20px);
        background-color: #ffffff; /* Màu nền của phần tử */
        border-radius: 10px; /* Độ cong của góc */
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1), 0 10px 10px rgba(0, 0, 0, 0.08); /* Đổ bóng */
        display: flex;
        flex-direction: column;
        }
        .joomdle_item_title {
            background:none;
            border:none;
            padding: 10px 20px;
            margin:0px
        }
        .joomdle_course_list_item_title a {
        color: #000ff; 
        text-decoration: none; 
        font-size: 25px;
        font-weight: 30px;
        background-color: transparent; 
        }

        .joomdle_course_description {
            color: black; 
            text-decoration: none; 
            font-size: 15px;
            font-weight: 10px;
            background-color: transparent; /* Đặt màu nền là trong suốt */
            padding: 0px 20px;
        }

        .joomdle_course_buttons {
            color: red; /* Màu chữ cho nút */
            background-color: transparent; /* Đặt màu nền là trong suốt */
        }
        .joomdle_course_image {
            height:200px;
            width:400px;
            border-radius: 10px 10px 0px 0px;
            object-fit: cover; /* Cắt ảnh theo kích thước */
        }
    </style>
</head>
<body>
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
            <?php if (($show_description) && ($curso['summary'])) : ?>
                <?php 
                    if (count ($curso['summary_files']))
                    {
                        foreach ($curso['summary_files'] as $file) :
                        ?>
                            <img class="joomdle_course_image" src="<?php echo $file['url']; ?>">
                        <?php
                        endforeach;
                    }
                ?>                
            <?php endif; ?>
            <?php if ($show_description) : ?>
            <div class="joomdle_item_title joomdle_course_list_item_title">
            <?php else : ?>
            <div class="joomdle_item_full joomdle_course_list_item_title">
            <?php endif; ?>
                <?php $url = JRoute::_("index.php?option=com_joomdle&view=detail&course_id=$course_id-$course_slug"); ?>
                <?php  echo "<a href=\"$url\">".$curso['fullname']."</a><br>"; ?>
            </div>
            <div class="joomdle_course_description">
                    <?php echo JoomdleHelperSystem::fix_text_format($curso['summary']); ?>
                </div>
                <?php if ($show_buttons) : ?>
                <div class="joomdle_course_buttons">
                    <?php echo JoomdleHelperSystem::actionbutton ( $curso, $free_courses_button, $paid_courses_button); ?>
                </div>
                <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
