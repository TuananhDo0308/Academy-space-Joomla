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

 
class JFormFieldShopcurrency extends JFormFieldList
{
    /**
    * Element name
    *
    * @access       protected
    * @var          string
    */
    public    $type = 'Shopcurrency';

    function getOptions()
    {
        $options = array();

        $params = JComponentHelper::getParams( 'com_joomdle' );

        $option = array ('value' => 'no', 'text' => JText::_ ('COM_JOOMDLE_NONE'));
        $options[] = $option;

        // Add currencies added via plugins
        JPluginHelper::importPlugin( 'joomdleshop' );
        $currencies_results = JFactory::getApplication()->triggerEvent ('onGetShopCurrencies', array());
        foreach ($currencies_results as $cr)
        {
            if (count ($cr) > 0)
                break;
        }
        if (is_array ($cr))
        {
            foreach ($cr as  $c)
            {
                $option['value'] = $c['code'];
                $option['text'] = $c['name'] . ' (' . $c['code'] . ')';

                $options[] = $option;
            }
        }

        return $options;
    }
}

?>
