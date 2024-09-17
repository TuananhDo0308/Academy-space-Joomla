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
class JFormFieldJoomdleuser extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'Joomdleuser';

    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions()
    {
        $options = array();

        $option = array ('value' => '', 'text' => JText::_ ('COM_JOOMDLE_SELECT_USERS'));
        $options[] = $option;
        $option = array ('value' => 'joomla', 'text' => JText::_( 'COM_JOOMDLE_JOOMLA_USERS' ));
        $options[] = $option;
        $option = array ('value' => 'moodle', 'text' => JText::_( 'COM_JOOMDLE_MOODLE_USERS' ));
        $options[] = $option;
        $option = array ('value' => 'joomdle', 'text' => JText::_( 'COM_JOOMDLE_JOOMLDE_USERS' ));
        $options[] = $option;
        $option = array ('value' => 'not_joomdle', 'text' => JText::_( 'COM_JOOMDLE_NOT_JOOMDLE_USERS' ));
        $options[] = $option;

        return $options;
    }
}
