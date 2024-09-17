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
 

class JFormFieldQuestioncategoryList extends JFormFieldList
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        public    $type = 'QuestionCategoryList';

        function getOptions()
        {
            $cats = $this->getCats ();
            return $cats;
        }

        function getCats ($options = array(), $level = 0)
        {
            $cats = JoomdleHelperContent::call_method ('quiz_get_question_categories');

            if (!is_array ($cats))
                    return $options;

            foreach ($cats as $cat)
            {
                    $val = $cat['id'];
                    $text = $cat['name'];
                    for ($i = 0; $i < $level; $i++)
                            $text = "-".$text;
                    $options[] = JHTML::_('select.option', $val, $text);
            }

            return $options;
        }

}

?>
