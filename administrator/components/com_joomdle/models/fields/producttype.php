<?php
/**
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomdle
 */
class JFormFieldProducttype extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'Producttype';

    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions()
    {
        $options = array();

        $option = array ('value' => '', 'text' => JText::_ ('COM_JOOMDLE_SELECT_PRODUCT_TYPE'));
        $options[] = $option;
        $option = array ('value' => 'courses', 'text' => JText::_( 'COM_JOOMDLE_COURSES' ));
        $options[] = $option;
        $option = array ('value' => 'bundles', 'text' => JText::_( 'COM_JOOMDLE_BUNDLES' ));
        $options[] = $option;
        $option = array ('value' => 'all', 'text' => JText::_( 'COM_JOOMDLE_ALL' ));
        $options[] = $option;

        return $options;
    }
}
