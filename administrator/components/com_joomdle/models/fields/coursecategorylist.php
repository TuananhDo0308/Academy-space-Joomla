<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */
 
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
require_once(JPATH_SITE.'/components/com_joomdle/helpers/content.php');

JFormHelper::loadFieldClass('list');

 
class JFormFieldCoursecategoryList extends JFormFieldList
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        public    $type = 'CoursecategoryList';

        function getOptions()
        {
            $cats = $this->getCats (0);
            return $cats;
        }


        function getCats ($cat_id, $options = array(), $level = 0)
        {
                $cats = JoomdleHelperContent::getCourseCategories ($cat_id);

                if (!is_array ($cats))
                        return $options;

                foreach ($cats as $cat)
                {
                        $val = $cat['id'];
                        $text = $cat['name'];
                        for ($i = 0; $i < $level; $i++)
                                $text = "-".$text;
                        $options[] = JHTML::_('select.option', $val, $text);
                        $options = $this->getCats ($cat['id'], $options, $level + 1);
                }

                return $options;
        }
}

?>
