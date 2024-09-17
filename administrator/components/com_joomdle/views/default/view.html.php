<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class JoomdleViewDefault extends JViewLegacy {

    function showButton( $link, $image, $text)
    {
            $lang           = JFactory::getLanguage();
            ?>
                <a href="<?php echo $link; ?>">
                    <button  type="button" class="panel_btn btn-default" style="width:140px;height:120px">
                        <?php echo JHTML::_('image', 'administrator/components/com_joomdle/assets/images/' . $image , NULL, NULL ); ?>
                        <br>
                        <span><?php echo $text; ?></span>
                    </button>
                </a>
            <?php
    }

    function renderAbout ()
    {
        $xmlfile = JPATH_ADMINISTRATOR.'/components/com_joomdle/joomdle.xml';

        $version = '';
        if (file_exists($xmlfile))
        {
            if ($data = \JInstaller::parseXMLInstallFile ($xmlfile)) {
                $version =  $data['version'];
            }
        }

        $output = '<div style="padding: 5px;">';
        $output .= JText::sprintf('COM_JOOMDLE_ABOUT_TEXT_VERSION', $version);
        $output .= '<P>'.JText::sprintf('COM_JOOMDLE_ABOUT_TEXT_PROVIDES');
        $output .= '<P>'.JText::sprintf('COM_JOOMDLE_ABOUT_TEXT_SUPPORT');
        $output .= '<P>'.JText::sprintf('COM_JOOMDLE_ABOUT_SUBSCRIBE');
        $output .= '<P>'.JText::sprintf('COM_JOOMDLE_ABOUT_TEXT_JED');
        $output .= '</div>';

        return $output;
    }

    function display($tpl = null) {
        parent::display($tpl);
    }

}
?>
