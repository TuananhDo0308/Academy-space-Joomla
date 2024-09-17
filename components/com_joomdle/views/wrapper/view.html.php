<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joomdle component
 */
class JoomdleViewWrapper extends JViewLegacy {
    function display($tpl = null) {

        $app                = JFactory::getApplication();
        $this->params = $app->getParams();

        $this->wrapper = new JObject ();
        $this->wrapper->load = '';

        $mtype = $app->input->get('moodle_page_type');
        if (!$mtype)
            $mtype = $this->params->get( 'moodle_page_type' );
        $id = $app->input->get('id');
        if (!$id)
            $id = $this->params->get( 'course_id' );

        $id = (int) $id;

        $time = $app->input->get('time');
        $lang = $app->input->get('lang');

        $topic = $app->input->get('topic');
        $redirect = $app->input->get('redirect');
        $hash = $app->input->get('hash');
        $section = $app->input->get('section');

        $layout = $app->input->get('layout');

        $params = $this->params;
        switch ($mtype)
        {
            case "course" :
                $path = '/course/view.php?id=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                if ($topic)
                    $this->wrapper->url .= '&topic='.$topic;
                if ($section)
                    $this->wrapper->url .= '#section-'.$section;
                break;
            case "coursecategory" :
                $path = '/course/index.php?categoryid=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                break;
            case "news" :
                $path = '/mod/forum/discuss.php?d=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                break;
            case "forum" :
                $path = '/mod/forum/view.php?id=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                break;
            case "event" :
                $path = "/calendar/view.php?view=day&time=$time";
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path;
                break;
            case "user" :
                $path = '/user/view.php?id=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                break;
            case "edituser" :
                $user = JFactory::getUser ();
                $id = JoomdleHelperContent::call_method ('user_id', $user->username);
                $path = '/user/edit.php?&course_id=1&id=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                break;
            case "resource" :
                $path = '/mod/resource/view.php?id=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                break;
            case "quiz" :
                $path = '/mod/quiz/view.php?id=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                break;
            case "page" :
                $path = '/mod/page/view.php?id=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                break;
            case "assignment" :
                $path = '/mod/assignment/view.php?id=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                break;
            case "folder" :
                $path = '/mod/folder/view.php?id=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                break;
            case "messages" :
                $path = '/message/index.php';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path;
                break;
            case "badge" :
                $path = '/badges/badge.php?hash=';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$hash;
                break;
            case "moodle" :
                $path = '/?a=1';
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path;
                break;
            case "customurl" :
                $path = $params->get ('customurl');
                $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path;
                break;
            case "fullurl" :
                $gotourl = $app->input->get('gotourl', '', STRING);
                $this->wrapper->url = $gotourl;
                break;
            default:
                if ($mtype)
                {
                    $path = '/mod/'.$mtype.'/view.php?id=';
                    $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path.$id;
                    break;
                }
                else
                {
                    $path = '/?a=1';
                    $this->wrapper->url = $params->get( 'MOODLE_URL' ).$path;
                }
                break;
        }

        if ($lang)
            $this->wrapper->url .= "&lang=$lang";

        if ($redirect)
            $this->wrapper->url .= "&redirect=$redirect";

        // Moodle theme can be overriden by plugin
        JPluginHelper::importPlugin( 'joomdletheme' );
        $result = JFactory::getApplication()->triggerEvent ('onGetMoodleTheme', array ());
        $theme = array_shift ($result);

        if (!$theme) // If no theme by plugin, check configuration
            $theme = $params->get('theme');
        if ($theme) 
            $this->wrapper->url .= "&theme=".$theme;

        if ($layout == 'getout')
            $tpl = 'getout';
        else if ($this->params->get('crossdomain_autoheight'))
            $tpl = 'cross';

        parent::display($tpl);
    }
}
?>
