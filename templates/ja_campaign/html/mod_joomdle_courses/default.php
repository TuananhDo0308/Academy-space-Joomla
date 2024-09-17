<?php 
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

$itemid = JoomdleHelperContent::getMenuItem();

if ($linkstarget == "new")
    $target = " target='_blank'";
else $target = "";

if ($linkstarget == 'wrapper')
    $open_in_wrapper = 1;
else
    $open_in_wrapper = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joomdle Course List</title>
    <style>
        /* CSS cho danh sách khóa học hiển thị theo kiểu flexbox */
        .joomdlecourses {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 20px; /* Khoảng cách giữa các khóa học */
        }

        .joomdlecourses li {
            flex: 0 0 calc(33.33% - 20px); /* Phần trăm chiều rộng của mỗi khóa học, có thể điều chỉnh tùy ý */
            background-color: #f9f9f9; /* Màu nền của khóa học */
            padding: 20px; /* Khoảng cách lề trong mỗi khóa học */
            border-radius: 5px; /* Bo góc cho khung khóa học */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Đổ bóng cho khóa học */
        }

        .joomdlecourses li img {
            width: 100%; /* Hình ảnh điều chỉnh tự động chiều rộng */
            height: auto; /* Hình ảnh điều chỉnh tự động chiều cao */
            border-radius: 5px; /* Bo góc cho hình ảnh */
        }

        .joomdlecourses li h3 {
            margin-top: 10px; /* Khoảng cách từ hình ảnh đến tiêu đề khóa học */
        }

        .joomdlecourses li a.btn {
            display: inline-block;
            background-color: #007bff; /* Màu nền của nút */
            color: #fff; /* Màu chữ của nút */
            padding: 10px 20px; /* Kích thước lề trong của nút */
            text-decoration: none; /* Loại bỏ gạch chân cho liên kết */
            border-radius: 5px; /* Bo góc cho nút */
            transition: background-color 0.3s ease; /* Hiệu ứng chuyển đổi màu nền */
        }

        .joomdlecourses li a.btn:hover {
            background-color: #0056b3; /* Màu nền khi di chuột qua nút */
        }
    </style>
</head>
<body>
    <ul class="joomdlecourses<?php echo $moduleclass_sfx; ?>">
        <?php
        $i = 0;
        if (is_array($cursos))
            foreach ($cursos as $id => $curso) {
                $id = $curso['remoteid'];
                $course_name = $curso['fullname'];
                // Thêm đường dẫn hình ảnh của khóa học nếu có
                $course_image_url = ''; // Điền vào đường dẫn hình ảnh của khóa học
                ?>
                <li>
                    <!-- Hình ảnh của khóa học -->
                    <?php if (!empty($course_image_url)) : ?>
                        <img src="<?php echo $course_image_url; ?>" alt="<?php echo $course_name; ?>">
                    <?php endif; ?>
                    <!-- Tên của khóa học -->
                    <h3><?php echo $course_name; ?></h3>
                    <!-- Nút để chuyển đến chi tiết khóa học -->
                    <?php
                    if ($linkto == 'moodle') {
                        // URL để chuyển đến chi tiết khóa học trên Moodle
                        $course_detail_url = ($open_in_wrapper) ? $moodle_auth_land_url . "?username=guest&mtype=course&id=$id&use_wrapper=$open_in_wrapper&Itemid=$itemid" : "$moodle_url/course/view.php?id=$id";
                    } else {
                        // URL để chuyển đến chi tiết khóa học trong Joomla
                        $course_detail_url = JRoute::_("index.php?option=com_joomdle&view=detail&cat_id=" . $curso['cat_id'] . ":" . JFilterOutput::stringURLSafe($curso['cat_name']) . "&course_id=" . $curso['remoteid'] . ':' . JFilterOutput::stringURLSafe($curso['fullname']) . "&Itemid=$itemid");
                    }
                    ?>
                    <a href="<?php echo $course_detail_url; ?>" class="btn">Xem chi tiết</a>
                </li>
            <?php
                $i++;
                if ($i >= $limit) // Show only this number of latest courses
                    break;
            }
        ?>
    </ul>
</body>
</html>
