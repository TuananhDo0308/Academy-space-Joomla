<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerAdapter;


// 1.6 installer file
class com_joomdleInstallerScript
{
        /**
         * method to install the component
         *
         * @return void
         */
        function install($parent) 
        {
            $manifest = $parent->getManifest();
            $parent = $parent->getParent();
            $source = $parent->getPath("source");
             
            $installer = new JInstaller();
            
            // Install plugins
            foreach($manifest->plugins->plugin as $plugin) {
                $attributes = $plugin->attributes();
                $plg = $source . '/' . $attributes['folder'].'/'.$attributes['plugin'];
                $plg = $source . '/' . $attributes['folder'];
                $installer->install($plg);
            }
            // Install modules
            foreach($manifest->modules->module as $module) {
                $attributes = $module->attributes();
                $mod = $source . '/' . $attributes['folder'].'/'.$attributes['module'];

                $installer->install($mod);
            }
            
            $db = JFactory::getDbo();
            $tableExtensions = $db->quoteName("#__extensions");
            $columnElement   = $db->quoteName("element");
            $columnType      = $db->quoteName("type");
            $columnEnabled   = $db->quoteName("enabled");
            
            $tableExtensions = "#__extensions";
            $columnElement   = "element";
            $columnType      = "type";
            $columnEnabled   = "enabled";

            // Enable plugins
            $db->setQuery(
                "UPDATE 
                    $tableExtensions
                SET
                    $columnEnabled=1
                WHERE
                    ($columnElement='courses' or $columnElement='coursecategories' or $columnElement='coursetopics' or $columnElement='joomdlehooks' or $columnElement='joomdlelicense')
                AND
                    $columnType='plugin'"
            );
            
            $db->execute();
            
            // Set plugin ordering
            $db->setQuery(
                "UPDATE 
                    $tableExtensions
                SET
                    ordering=100
                WHERE
                    $columnElement='joomdlehooks' 
                AND
                    $columnType='plugin'"
            );
            
            $db->execute();
        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        function uninstall($parent) 
        {
            return; //XXX not working, need to uninstall all plugins/modules separately
        }
 
        /**
         * method to update the component
         *
         * @return void
         */
        function update($parent) 
        {
                // $parent is the class calling this method
                $this->install ($parent);
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return boolean
         */
        function preflight($type, $parent) 
        {
            // On update, check Moodle supports this version before installing
            if ($type == 'update')
            {
                // First we make sure system check is working, so we don't break on install
                // If it is not working, with let installation happen, as there's nothing to break anyway
                require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/helpers/content.php');
                if (!$this->system_ready_for_version_check ())
                    return true;

                // Check that installed Moodle version supports this Joomdle release
                $manifest = $parent->getManifest ();
                $installed_moodle_version = $this->get_moodle_version ();
                if ($installed_moodle_version < $manifest->requiresMoodleVersion)
                {
                    $parent->getParent()->abort('Your Moodle version does not support this Joomdle release.<br>' . 
                            'Installed Moodle version: '. $installed_moodle_version . '<br>'. 
                            'Required Moodle version >= ' . $manifest->requiresMoodleVersion);
                    return false;
                }
            }
        }

        private function system_ready_for_version_check ()
        {
            // Get installed Joomdle version in Joomla
            $xmlfile = JPATH_ADMINISTRATOR.'/components/com_joomdle/joomdle.xml';
            if (file_exists($xmlfile))
            {
                if ($data = Installer::parseXMLInstallFile($xmlfile)) {
                    $version =  $data['version'];
                }
            }
            $joomdle_release_joomla = $version;

            // Check that currently installed version is >= 1.2.4 so that it has check version capability
            if (version_compare ($joomdle_release_joomla, '1.2.4') < 0)
                return false;

            $comp_params = JComponentHelper::getParams( 'com_joomdle' );
            $ws_protocol = $comp_params->get( 'ws_protocol' );

            if ($ws_protocol == 'xmlrpc')
            {
                $php_exts = get_loaded_extensions ();
                $xmlrpc_enabled = in_array ('xmlrpc', $php_exts);

                if (!$xmlrpc_enabled)
                    return false;
            }

            $connection = $comp_params->get( 'connection_method' );

            $connection_method_enabled = false;
            if ($connection == 'fgc')
                $connection_method_enabled = ini_get ('allow_url_fopen');
            else if ($connection == 'curl')
                $connection_method_enabled = function_exists('curl_version') == "Enabled";

            if (!$connection_method_enabled)
                return false;

            /* Test Moodle Web services in joomdle plugin */
            $response = JoomdleHelperContent::call_method_debug ('system_check');
            if ($response == '')
                return false;
            else {
                if ($response ['joomdle_auth'] != 1)
                    return false;
                else if ($response['joomdle_configured'] == 0)
                    return false;
                else if ($response['test_data'] != 'It works')
                    return false;
            }

            // Joomdle version has to be the same in Joomla and Moodle
            if ($response['release'] != $joomdle_release_joomla)
                return false;

            return true;
        }

        private function get_moodle_version ()
        {
            require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/helpers/content.php');

            $moodle_version = JoomdleHelperContent::call_method_debug ('get_moodle_version');

            return $moodle_version;
        }
 
        /**
         * method to run after an install/update/uninstall method
         *
         * @return void
         */
        function postflight($type, $parent) 
        {
            if ($type == 'install')
                $this->load_default_config ();

            $rows = 0;
            $manifest = $parent->getManifest();

?>

<h2>Joomdle Installation</h2>
<table  class="table table-striped">
    <thead>
        <tr>
            <th class="title" colspan="2"><?php echo ('Extension'); ?></th>
            <th width="30%"><?php echo ('Status'); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="3"></td>
        </tr>
    </tfoot>
    <tbody>
        <tr class="row0">
            <td class="key" colspan="2"><?php echo 'Joomdle Component'; ?></td>
            <td><strong><?php echo ('Installed'); ?></strong></td>
        </tr>
        <tr>
            <th><?php echo ('Module'); ?></th>
            <th><?php echo ('Client'); ?></th>
            <th></th>
        </tr>
    <?php foreach ($manifest->modules->module as $module) : ?>
<?php
                $attributes = $module->attributes();
?>
        <tr class="row<?php echo (++ $rows % 2); ?>">
            <td class="key"><?php echo $attributes['title']; ?></td>
            <td class="key"><?php echo ucfirst($attributes['client']); ?></td>
            <td><strong><?php echo ('Installed'); ?></strong></td>
        </tr>
    <?php endforeach; ?>
        <tr>
            <th><?php echo ('Plugin'); ?></th>
            <th><?php echo ('Group'); ?></th>
            <th></th>
        </tr>
    <?php foreach ($manifest->plugins->plugin as $plugin) : ?>
<?php
                $attributes = $plugin->attributes();
?>
        <tr class="row<?php echo (++ $rows % 2); ?>">
            <td class="key"><?php echo ucfirst($attributes['plugin']); ?></td>
            <td class="key"><?php echo $attributes['group']; ?></td>
            <td><strong><?php echo ('Installed'); ?></strong></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php
        }

        function load_default_config ()
        {
            $db = JFactory::getDBO();
            $query = "UPDATE #__extensions set params='auto_create_users=1
            MOODLE_URL=
            connection_method=fgc
            ws_protocol=rest
            auto_delete_users=1
            auto_login_users=0
            linkstarget=wrapper
            scrolling=no
            width=100%
            height=1000
            autoheight=1
            transparency=0
            default_itemid=
            show_topìcs_link=1
            show_grading_system_link=0
            show_teachers_link=0
            show_enrol_link=1
            show_paypal_button=0
            topics_show_numbers=1
            coursecategory_show_category_info=1
            shop_integration=0
            courses_category=0
            buy_for_children=0
            enrol_email_subject=Welcome to COURSE_NAME
            enrol_email_text=To enter the course, go to: COURSE_URL
            additional_data_source=none
            use_xipt_integration=0'
            WHERE name='com_joomdle'";

            $db->setQuery($query);
            if (!$db->execute()) {
                return false;
            }
        }


    function getFields( $table )
    {
        $result = array();
        $db     = JFactory::getDBO();

        $query  = 'SHOW FIELDS FROM ' .$table;

        $db->setQuery( $query );

        $fields = $db->loadObjectList();

        foreach( $fields as $field )
        {
            $result[ $field->Field ]    = preg_replace( '/[(0-9)]/' , '' , $field->Type );
        }

        return $result;
    }

    function TableColumnExists($tablename, $columnname)
    {
        $fields = $this->getFields($tablename);
        if(array_key_exists($columnname, $fields))
        {
            return true;
        }
        return false;
    }

}
