<?php
/**
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/shop.php' );

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomdle
 */
class JFormFieldSocialgroupcategory extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'Socialgroupcategory';

    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions()
    {
        $options = array();

        $params = JComponentHelper::getParams( 'com_joomdle' );

        $option = array ('value' => 'no', 'text' => JText::_ ('COM_JOOMDLE_NONE'));
        $options[] = $option;

        // Add categories added via plugins
        JPluginHelper::importPlugin( 'joomdlesocialgroups' );
        $cats_results = JFactory::getApplication()->triggerEvent ('onGetSocialGroupCategories', array());
        
        $cats = NULL;
        foreach ($cats_results as $cats)
        {
            if (count ($cats) > 0)
                break;
        }
        if (is_array ($cats))
        {
            foreach ($cats as  $cat)
            {
                $option['value'] = $cat['id'];
                $option['text'] = $cat['name'];

                $options[] = $option;
            }
        }

        return $options;
    }
}
