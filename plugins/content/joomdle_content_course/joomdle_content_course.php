<?php
/**
* @version        1.4.0
* @package        Joomdle - Plugin to show Course Content in com_content
* @copyright      Qontori Pte Ltd
* @license        http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/helpers/content.php');
require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/helpers/shop.php');
require_once( JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/system.php' );

class plgContentJoomdle_Content_Course extends JPlugin
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename, 
     *                         forcing it to be unique 
     */
    var $_element    = 'joomdle_content_course';
    
    function __construct(& $subject, $config) 
    {
        parent::__construct($subject, $config);
        $this->loadLanguage('plg_content_joomdle_content_course', JPATH_ADMINISTRATOR);
    }
    
    /**
     * Search for the tag and replace it with the content
     * 
     * @param $article
     * @param $params
     * @param $limitstart
     */
    function onContentPrepare($context, &$row, &$params, $page=0 )
    {
        // simple performance check to determine whether bot should process further
        if ( Joomla\String\StringHelper::strpos( $row->text, 'joomdlecourse' ) === false ) {
            return true;
        }
    
        // Get plugin info
        $plugin = JPluginHelper::getPlugin('content', 'joomdle_content_course');
    
        // expression to search for
        $regex = '/{joomdlecourse\s*.*?}/i';
    
        jimport( 'joomla.html.parameter' );

        $pluginParams = $this->params;
    
        // check whether plugin has been unpublished
        if ( !$pluginParams->get( 'enabled', 1 ) ) {
            $row->text = preg_replace( $regex, '', $row->text );
            return true;
        }
    
        // find all instances of plugin and put in $matches
        preg_match_all( $regex, $row->text, $matches );
    
        // Number of plugins
        $count = count( $matches[0] );
    
        // plugin only processes if there are any instances of the plugin in the text
        if ( $count ) {
            foreach($matches as $match)
                $this->showCourses( $row, $matches, $count, $regex );
        }
    }
    
    /**
     * Shows the courses 
     * @param $row
     * @param $matches
     * @param $count
     * @param $regex
     * @return unknown_type
     */
    function showCourses( &$row, &$matches, $count, $regex )
    {
        for ( $i=0; $i < $count; $i++ )
        {
            $load = str_replace( 'joomdlecourse', '', $matches[0][$i] );
            $load = str_replace( '{', '', $load );
            $load = str_replace( '}', '', $load );
            $load = trim( $load );
    
            $course    = $this->showCourse( $load );
            $row->text     = str_replace($matches[0][$i], $course, $row->text );
        }
    
        // removes tags without matching
        $row->text = preg_replace( $regex, '', $row->text );
    }
    
    /**
     * Loads an individual course
     * and displays it
     * 
     * @param $load
     * @return unknown_type
     */
    function showCourse( $load )
    {
        // Divide string by space, allowing double quotes
        $inline_params = preg_split('/"[^"]*"(*SKIP)(*F)|\x20/', $load);

//        $params = $this->get('params');
        $params = $this->params;
        
        $params = $params->toArray();
        
        $params['attributes'] = array();
        // Merge plugin parameters with tag parameters, overwriting wherever necessary
        foreach( $inline_params as $p )
        {
            $data = explode("=", $p);
            $k = $data[0];
            $v = trim ($data[1], '"'); // Remove double quotes
            
            // Merge the attribute options in one subarray
            if (substr($k, 0, 10) == 'attribute_')
                $params['attributes'][$k] = $v;
            else
                $params[$k] = $v;
        }
        
        // No id set, return
        if( !array_key_exists('id', $params) )
            return;
        
        $user = JFactory::getUser();
        $username = $user->username;

        // Load Course
        $course_info= JoomdleHelperContent::getCourseInfo ( (int) $params['id'] , $username);
        
        // Course not found
        if( !$course_info )
            return;
            
        ob_start();
        include( 'joomdle_content_course/tmpl/view.php' );
        $return = ob_get_contents();
        ob_end_clean();
        
        
        return $return;
    }
    
    function _getLayout($layout, $vars = false, $plugin = '', $group = 'tienda' )
    {
        if (empty($plugin))
        {
            $plugin = $this->_element;
        }

        ob_start();
        $layout = $this->_getLayoutPath( $plugin, $group, $layout );
        include($layout);
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }


    /**
     * Get the path to a layout file
     *
     * @param   string  $plugin The name of the plugin file
     * @param   string  $group The plugin's group
     * @param   string  $layout The name of the plugin layout file
     * @return  string  The path to the plugin layout file
     * @access protected
     */
    function _getLayoutPath($plugin, $group, $layout = 'default')
    {
        $app = JFactory::getApplication();

        // get the template and default paths for the layout
        $templatePath = JPATH_SITE.'/templates/'.$app->getTemplate().'/html/plugins/'.$group.'/'.$plugin.'/'.$layout.'.php';
        $defaultPath = JPATH_SITE.'/plugins/'.$group.'/'.$plugin.'/'.'tmpl'.'/'.$layout.'.php';

        // if the site template has a layout override, use it
        jimport('joomla.filesystem.file');
        if (JFile::exists( $templatePath ))
        {
            return $templatePath;
        }
        else
        {
            return $defaultPath;
        }
    }
}
