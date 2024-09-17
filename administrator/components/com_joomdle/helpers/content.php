<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');

use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerAdapter;

define ('CJ_EMPTY_VALUE', -775577);

require_once(JPATH_ADMINISTRATOR.'/components/com_joomdle/helpers/profiletypes.php');


class JoomdleHelperContent
{
    static function _get_xmlrpc_url () {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $moodle_xmlrpc_server_url = $params->get( 'MOODLE_URL' ).'/webservice/xmlrpc/server.php?wstoken='.$params->get( 'auth_token');

        return $moodle_xmlrpc_server_url;
    }

    static function _get_rest_url () {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $moodle_rest_server_url = $params->get( 'MOODLE_URL' ).'/webservice/rest/server.php?moodlewsrestformat=json&wstoken='.$params->get( 'auth_token');

        return $moodle_rest_server_url;
    }

    static function _get_cm () {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $connection_method = $params->get( 'connection_method' );
        return $connection_method;
    }

    static function _get_protocol () {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $ws_protocol = $params->get( 'ws_protocol', 'xmlrpc' );
        return $ws_protocol;
    }

    static function get_request ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
    {
        if ($params == CJ_EMPTY_VALUE)
            $request = xmlrpc_encode_request("joomdle_".$method, array (), array ('encoding' => 'utf-8', 'escaping'=>'markup'));
        else if ($params2 == CJ_EMPTY_VALUE)
            $request = xmlrpc_encode_request("joomdle_".$method, array ($params) , array ('encoding' => 'utf-8', 'escaping'=>'markup'));
        else if ($params3 == CJ_EMPTY_VALUE)
            $request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2), 
                    array ('encoding' => 'utf-8', 'escaping'=>'markup'));
        else if ($params4 == CJ_EMPTY_VALUE)
            $request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2, $params3), 
                    array ('encoding' => 'utf-8', 'escaping'=>'markup'));
        else if ($params5 == CJ_EMPTY_VALUE)
            $request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2, $params3, $params4), 
                    array ('encoding' => 'utf-8', 'escaping'=>'markup'));
        else
            $request = xmlrpc_encode_request("joomdle_".$method, array ($params, $params2, $params3, $params4, $params5), 
                    array ('encoding' => 'utf-8', 'escaping'=>'markup'));

        return $request;
    }

    static function get_request_rest ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
    {
        /*
           We use a external file with the Moodle web services definitions, to get parameter names.
           The goal is to convert the params* vars to an array where the key is the parameter name expected by the web service
           Example for list_courses:
            $rest_params = array ('enrollable_only' => $params, 'sortby' => $params2, 'guest' => $params3, 'username' => $params4);
       */
        require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/helpers/externallib.php');
        $external_helper = new joomdle_helpers_external ();

        if (method_exists ('joomdle_helpers_external', $method.'_parameters'))
        {
            $sig = $external_helper->{$method.'_parameters'} ();
        }
        else
        {
            // Try to find sig in plugins if not found in Joomdle file
            $result = JFactory::getApplication()->triggerEvent('onJoomdleGetRequestParameters', array($method));
            $found = false;
            foreach ($result as $method_params)
            {
                if (count ($method_params))
                {
                    $sig = $method_params;
                    $found = true;
                    break;
                }
            }

            if (!$found)
                die ('ERROR: Joomdle external signature not found: ' . $method);
        }

        $rest_params = array ();
        $i = 1;
        foreach ($sig as $param_key => $param_discard)
        {
            $param = 'params';
            if ($i > 1)
                $param .= $i;
            $i++;

            // For empty optional parameters
            if (${$param} == CJ_EMPTY_VALUE)
                continue; 

            $rest_params[$param_key] = ${$param};
        }

        $rest_params = JoomdleHelperContent::format_postdata_for_curlcall($rest_params);

        return $rest_params;
    }

    static function call_method_xmlrpc_curl ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

        $request =  JoomdleHelperContent::get_request ($method, $params, $params2, $params3, $params4, $params5);

        $headers = array();
        array_push($headers,"Content-Type: text/xml");
        array_push($headers,"Content-Length: ".strlen($request));
        array_push($headers,"\r\n");

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $moodle_xmlrpc_server_url); # URL to post to
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
        $file = curl_exec( $ch ); # run!
        curl_close($ch); 

        $file = trim ($file);
        $response = xmlrpc_decode($file, 'utf8');

        if (is_array ($response))
            if (xmlrpc_is_fault ($response))
            {
                echo "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
                die;
            }

        return $response;
    }

    static function call_method_xmlrpc_fgc ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
    {
        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

        $request =  JoomdleHelperContent::get_request ($method, $params, $params2, $params3, $params4, $params5);

        $context = stream_context_create(array('http' => array(
                    'method' => "POST",
                    'header' => "Content-Type: text/xml",
                    'content' => $request
                        )));
        $file = file_get_contents($moodle_xmlrpc_server_url , false, $context);
        $file = trim ($file);

        $response = xmlrpc_decode($file, 'utf8');

        if (is_array ($response))
            if (xmlrpc_is_fault ($response))
            {
                /*
                $config = JFactory::getConfig();
                $log_path = $config->get('log_path');
                $log_data = date ('Y-m-d H:i:s') . ' - ' . $method . ' '  . serialize ($params) . ' ' . serialize ($params2) . ' ' . serialize ($params3) . "\n";
                file_put_contents ($log_path . '/xmlrpc_errors.log', $log_data, FILE_APPEND);
                */
                echo "XML-RPC Error (".$response['faultCode']."): ".$response['faultString'];
                die;
            }

        return $response;
    }

    static function call_method_rest_curl ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
    {
        $moodle_rest_server_url = JoomdleHelperContent::_get_rest_url ();

        $url = $moodle_rest_server_url . '&wsfunction=joomdle_' . $method;

        $request =  JoomdleHelperContent::get_request_rest ($method, $params, $params2, $params3, $params4, $params5);

        $headers = array();
        array_push($headers,"Content-Type: application/x-www-form-urlencoded");
        array_push($headers,"Content-Length: ".strlen($request));
        array_push($headers,"\r\n");

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url); # URL to post to
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
        $response = curl_exec( $ch ); # run!
        curl_close($ch); 

        $response = trim ($response);
        $response = json_decode ($response, true);

        return $response;
    }

    static function call_method_rest_fgc ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
    {
        $moodle_rest_server_url = JoomdleHelperContent::_get_rest_url ();

        $url = $moodle_rest_server_url . '&wsfunction=joomdle_' . $method;

        $request =  JoomdleHelperContent::get_request_rest ($method, $params, $params2, $params3, $params4, $params5);

        $context = stream_context_create(array('http' => array(
                    'method' => "POST",
                    'header' => "Content-Type: application/x-www-form-urlencoded",
                    'content' => $request
                        )));
        $file = @file_get_contents($url , false, $context);
        $file = trim ($file);
        $response = json_decode ($file, true);

        return $response;
    }

    static function call_method_rest_curl_named_params ($method, $params)
    {
        $moodle_rest_server_url = JoomdleHelperContent::_get_rest_url ();

        $url = $moodle_rest_server_url . '&wsfunction=joomdle_' . $method;

        $request = JoomdleHelperContent::format_postdata_for_curlcall($params);

        $headers = array();
        array_push($headers,"Content-Type: application/x-www-form-urlencoded");
        array_push($headers,"Content-Length: ".strlen($request));
        array_push($headers,"\r\n");

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url); # URL to post to
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
        $response = curl_exec( $ch ); # run!
        curl_close($ch); 

        $response = trim ($response);
        $response = json_decode ($response, true);

        return $response;
    }

    static function call_method_rest_fgc_named_params ($method, $params)
    {
        $moodle_rest_server_url = JoomdleHelperContent::_get_rest_url ();

        $url = $moodle_rest_server_url . '&wsfunction=joomdle_' . $method;

        $request = JoomdleHelperContent::format_postdata_for_curlcall($params);

        $context = stream_context_create(array('http' => array(
                    'method' => "POST",
                    'header' => "Content-Type: application/x-www-form-urlencoded",
                    'content' => $request
                        )));
        $file = @file_get_contents($url , false, $context);
        $file = trim ($file);
        $response = json_decode ($file, true);

        return $response;
    }

    static function call_method ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
    {
        $protocol = JoomdleHelperContent::_get_protocol ();

        if ($protocol == 'xmlrpc')
            $response = JoomdleHelperContent::call_method_xmlrpc ($method, $params, $params2, $params3, $params4,  $params5);
        else
            $response = JoomdleHelperContent::call_method_rest ($method, $params, $params2, $params3, $params4,  $params5);

        if ((is_array ($response)) && (array_key_exists ('exception', $response)))
        {
            $error = $response['message'];
            if (array_key_exists ('debuginfo', $response))
                $error .= ' (' . $response['debuginfo'] . ')';
            die (JText::_ ('COM_JOOMDLE_WEB_SERVICES_ERROR') . ': ' . $error);
        }

        return $response;
    }

    static function call_method_xmlrpc ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
    {
        $cm = JoomdleHelperContent::_get_cm ();

        if ($cm == 'fgc')
            $response = JoomdleHelperContent::call_method_xmlrpc_fgc ($method, $params, $params2, $params3, $params4,  $params5);
        else
            $response = JoomdleHelperContent::call_method_xmlrpc_curl ($method, $params, $params2, $params3, $params4,  $params5);

        return $response;
    }

    static function call_method_rest ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE, $params5 = CJ_EMPTY_VALUE)
    {
        $cm = JoomdleHelperContent::_get_cm ();

        if ($cm == 'fgc')
            $response = JoomdleHelperContent::call_method_rest_fgc ($method, $params, $params2, $params3, $params4,  $params5);
        else
            $response = JoomdleHelperContent::call_method_rest_curl ($method, $params, $params2, $params3, $params4,  $params5);

        return $response;
    }

    static function call_method_rest_named_params ($method, $params)
    {
        $cm = JoomdleHelperContent::_get_cm ();

        if ($cm == 'fgc')
            $response = JoomdleHelperContent::call_method_rest_fgc_named_params ($method, $params);
        else
            $response = JoomdleHelperContent::call_method_rest_curl_named_params ($method, $params);

        return $response;
    }

    static function call_method_debug ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE)
    {
        $protocol = JoomdleHelperContent::_get_protocol ();

        if ($protocol == 'xmlrpc')
            $response = JoomdleHelperContent::call_method_debug_xmlrpc ($method, $params, $params2, $params3, $params4);
        else
            $response = JoomdleHelperContent::call_method_debug_rest ($method, $params, $params2, $params3, $params4);

        return $response;
    }

    static function call_method_debug_xmlrpc ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE)
    {
        $cm = JoomdleHelperContent::_get_cm ();

        if ($cm == 'fgc')
            $response = JoomdleHelperContent::call_method_debug_xmlrpc_fgc ($method, $params, $params2, $params3, $params4);
        else
            $response = JoomdleHelperContent::call_method_debug_xmlrpc_curl ($method, $params, $params2, $params3, $params4);

        return $response;
    }

    static function call_method_debug_rest ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE, $params3 = CJ_EMPTY_VALUE, $params4 = CJ_EMPTY_VALUE)
    {
        $cm = JoomdleHelperContent::_get_cm ();

        if ($cm == 'fgc')
            $response = JoomdleHelperContent::call_method_debug_rest_fgc ($method, $params, $params2, $params3, $params4);
        else
            $response = JoomdleHelperContent::call_method_debug_rest_curl ($method, $params, $params2, $params3, $params4);

        return $response;
    }

    static function call_method_debug_xmlrpc_fgc ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE)
    {
        $moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

        $request =  JoomdleHelperContent::get_request ($method, $params, $params2);

        $context = stream_context_create(array('http' => array(
                    'method' => "POST",
                    'header' => "Content-Type: text/xml ",
                    'content' => $request
                        )));
        $file = @file_get_contents($moodle_xmlrpc_server_url , false, $context);
        $file = trim ($file);
        $response = xmlrpc_decode($file);

        // Save raw reply to log
        $config = JFactory::getConfig();
        $log_path = $config->get('log_path');
        file_put_contents ($log_path . '/' . 'joomdle_system_check.xml', $file);

        return $response;
    }

    static function call_method_debug_xmlrpc_curl ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE)
    {
        $moodle_xmlrpc_server_url = JoomdleHelperContent::_get_xmlrpc_url ();

        $request =  JoomdleHelperContent::get_request ($method, $params, $params2);
        $headers = array();
        array_push($headers,"Content-Type: text/xml");
        array_push($headers,"Content-Length: ".strlen($request));
        array_push($headers,"\r\n");

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $moodle_xmlrpc_server_url); # URL to post to
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
        $response = curl_exec( $ch ); # run!
        curl_close($ch); 

        // Save raw reply to log
        $config = JFactory::getConfig();
        $log_path = $config->get('log_path');
        file_put_contents ($log_path . '/' . 'joomdle_system_check.xml', $response);

        $response = trim ($response);
        $response = xmlrpc_decode ($response);

        return $response;
    }

    static function call_method_debug_rest_curl ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE)
    {
        $moodle_rest_server_url = JoomdleHelperContent::_get_rest_url ();

        $url = $moodle_rest_server_url . '&wsfunction=joomdle_' . $method;

        $request =  JoomdleHelperContent::get_request_rest ($method, $params, $params2);

        $headers = array();
        array_push($headers,"Content-Type: application/x-www-form-urlencoded");
        array_push($headers,"Content-Length: ".strlen($request));
        array_push($headers,"\r\n");

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url); # URL to post to
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); # custom headers, see above
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
        $response = curl_exec( $ch ); # run!
        curl_close($ch); 

        // Save raw reply to log
        $config = JFactory::getConfig();
        $log_path = $config->get('log_path');
        file_put_contents ($log_path . '/' . 'joomdle_system_check.json', $response);

        $response = trim ($response);
        $response = json_decode ($response, true);

        return $response;
    }

    static function call_method_debug_rest_fgc ($method, $params = CJ_EMPTY_VALUE, $params2 = CJ_EMPTY_VALUE)
    {
        $moodle_rest_server_url = JoomdleHelperContent::_get_rest_url ();

        $url = $moodle_rest_server_url . '&wsfunction=joomdle_' . $method;

        $request =  JoomdleHelperContent::get_request_rest ($method, $params, $params2);

        $context = stream_context_create(array('http' => array(
                    'method' => "POST",
                    'header' => "Content-Type: application/x-www-form-urlencoded",
                    'content' => $request
                        )));
        $file = @file_get_contents($url , false, $context);
        $file = trim ($file);
        $response = json_decode ($file, true);

        // Save raw reply to log
        $config = JFactory::getConfig();
        $log_path = $config->get('log_path');
        file_put_contents ($log_path . '/' . 'joomdle_system_check.json', $file);

        return $response;
    }

    static function format_array_postdata_for_curlcall($arraydata, $currentdata, &$data) {
        foreach ($arraydata as $k=>$v) {
            $newcurrentdata = $currentdata;
            if (is_object($v)) {
                $v = (array) $v;
            }
            if (is_array($v)) { //the value is an array, call the function recursively
                $newcurrentdata = $newcurrentdata.'['.urlencode($k).']';
                JoomdleHelperContent::format_array_postdata_for_curlcall($v, $newcurrentdata, $data);
            }  else { //add the POST parameter to the $data array
                $data[] = $newcurrentdata.'['.urlencode($k).']='.urlencode($v);
            }
        }
    }

    static function format_postdata_for_curlcall($postdata) {
        if (is_object($postdata)) {
            $postdata = (array) $postdata;
        }
        $data = array();
        foreach ($postdata as $k=>$v) {
            if (is_object($v)) {
                $v = (array) $v;
            }
            if (is_array($v)) {
                $currentdata = urlencode($k);
                JoomdleHelperContent::format_array_postdata_for_curlcall($v, $currentdata, $data);
            }  else {
                $data[] = urlencode($k).'='.urlencode($v);
            }
        }
        $convertedpostdata = implode('&', $data);
        return $convertedpostdata;
    }

    static function get_file ($file)
    {
        $cm = JoomdleHelperContent::_get_cm ();

        if ($cm == 'fgc')
                $response = file_get_contents ($file, FALSE, NULL);
        else
                $response = JoomdleHelperContent::get_file_curl ($file);

        return $response;
    }

    static function get_file_curl ($file)
    {
            $ch = curl_init();
            // set url
            curl_setopt($ch, CURLOPT_URL, $file);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // $output contains the output string
            $output = curl_exec($ch);

            // close curl resource to free up system resources
            curl_close($ch);

            return $output;
    }


    static function getCourseEvents($id)
    {
        return JoomdleHelperContent::call_method ('get_upcoming_events', $id);
    }

    static function getCourseInfo ($id, $username = '')
    {
        return JoomdleHelperContent::call_method ('get_course_info', (int) $id , $username);
    }

    static function getCourseCategories ($id = 0)
    {
        return JoomdleHelperContent::call_method ('get_course_categories', $id);
    }

    static function getCourseCategory ($id, $username)
    {
        return JoomdleHelperContent::call_method ('courses_by_category', $id, 0, $username);
    }

    static function getCourseNews ($id)
    {
        return JoomdleHelperContent::call_method ('get_news_items', $id);
    }

    static function getCourseStudentsNo ($id)
    {
        return JoomdleHelperContent::call_method ('get_course_students_no', $id);
    }

    static function getAssignmentSubmissions ($id)
    {
        return JoomdleHelperContent::call_method ('get_assignment_submissions', $id);
    }

    static function getAssignmentGrades ($id)
    {
        return JoomdleHelperContent::call_method ('get_assignment_grades', $id);
    }

    static function getCourseDailyStats ($id)
    {
        return JoomdleHelperContent::call_method ('get_course_daily_stats', $id);
    }

    static function getCourseList ($enrollable_only = 0, $orderby = 'fullname ASC', $guest = 0, $username = '')
    {
        return JoomdleHelperContent::call_method ('list_courses', (int) $enrollable_only, $orderby, (int) $guest, $username);
    }

    static function getStudentsNo ()
    {
        return JoomdleHelperContent::call_method ('get_student_no');
    }

    static function getCoursesNo ()
    {
        return JoomdleHelperContent::call_method ('get_course_no');
    }

    static function getEnrollableCoursesNo ()
    {
        return JoomdleHelperContent::call_method ('get_enrollable_course_no');
    }

    static function getAssignmentsNo ()
    {
        return JoomdleHelperContent::call_method ('get_total_assignment_submissions');
    }

    static function getLastWeekStats ()
    {
        return JoomdleHelperContent::call_method ('get_site_last_week_stats');
    }

    static function getCourseTeachers ($id)
    {
        return JoomdleHelperContent::call_method ('get_course_editing_teachers', $id);
    }

    static function getCourseContents ($id)
    {
        return JoomdleHelperContent::call_method ('get_course_contents', $id);
    }

    static function enrolUser ($username, $id)
    {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        if ($params->get( 'use_profiletypes' ))
        {
            $moodle_role = JoomdleHelperProfiletypes::get_user_profile_role ($username);
            if (!$moodle_role)
                $moodle_role = 5;
            $return = JoomdleHelperContent::call_method ('enrol_user', $username, (int) $id, (int) $moodle_role);
        }
        else
        {
            // Use roleid defined in manual enrol method in course
            $return = JoomdleHelperContent::call_method ('enrol_user', $username, (int) $id, 0);
        }


        return $return;

    }

    static function getMyCourses ($username = "")
    {
        if ($username)
        {
            $user_id = JUserHelper::getUserId($username);
            $user = JFactory::getUser($user_id);
        }
        else 
            $user = JFactory::getUser();

        if (!$user)
            return array ();

        $id = $user->get('id');
        $username = $user->get('username');

        $cursos = JoomdleHelperContent::call_method ('my_courses', $username, 0);

        return $cursos;
    }

    static function is_enroled ($username, $course_id)
    {
        if ($username)
        {
            $user_id = JUserHelper::getUserId($username);
            $user = JFactory::getUser($user_id);
        }
        else 
            $user = JFactory::getUser();

        if (!$user)
            return 0;

        $username = $user->get('username');

        $cursos = JoomdleHelperContent::call_method ('my_courses', $username, 0);

        foreach ($cursos as $curso)
        {
            if ($curso['id'] == $course_id)
                return 1;
        }
        return 0;
    }

    static function getMyEvents ()
    {
        $user = JFactory::getUser();
        if (!$user)
            return array ();

        $id = $user->get('id');
        $username = $user->get('username');

        $events = JoomdleHelperContent::call_method ('get_my_events', $username);

        return $events;
    }

    static function getMyNews ()
    {
        $user = JFactory::getUser();
        if (!$user)
            return array ();

        $id = $user->get('id');
        $username = $user->get('username');

        $news = JoomdleHelperContent::call_method ('get_my_news', $username);

        return $news;
    }

    static function getCourseGradeCategories ($id)
    {
        return JoomdleHelperContent::call_method ('get_course_grade_categories', $id);
    }

    static function multisort($array, $order_dir, $sort_by, $key1, $key2=NULL, $key3=NULL, $key4=NULL, $key5=NULL, $key6=NULL, $key7=NULL, $key8=NULL){
        // sort by ?

        if (!count ($array))
            return $array;

        foreach ($array as $pos =>  $val)
            $tmp_array[$pos] = $val[$sort_by];

        $order_dir = strtolower ($order_dir);
        if ($order_dir == 'desc')
            arsort($tmp_array);
        else
            asort($tmp_array);
       
        // display however you want
        foreach ($tmp_array as $pos =>  $val){
            $return_array[$pos][$sort_by] = $array[$pos][$sort_by];
            $return_array[$pos][$key1] = $array[$pos][$key1];
            if (isset($key2)){
                $return_array[$pos][$key2] = $array[$pos][$key2];
                }
            if (isset($key3)){
                $return_array[$pos][$key3] = $array[$pos][$key3];
                }
            if (isset($key4)){
                $return_array[$pos][$key4] = $array[$pos][$key4];
                }
            if (isset($key5)){
                $return_array[$pos][$key5] = $array[$pos][$key5];
                }
            if (isset($key6)){
                $return_array[$pos][$key6] = $array[$pos][$key6];
                }
            if (isset($key7)){
                $return_array[$pos][$key7] = $array[$pos][$key7];
                }
            if (isset($key8)){
                $return_array[$pos][$key8] = $array[$pos][$key8];
                }
        }

        return $return_array;
    }

    static function is_admin($userid)
    {
        jimport( 'joomla.user.helper' );
        $user = JFactory::getUser($userid);
        $groups = JUserHelper::getUserGroups($user->id);
 
        $admin_groups = array(); //put all the groups that you consider to be admins
        $admin_groups[] = "Super Users";
        $admin_groups[] = "Administrator";
 
        foreach($admin_groups as $temp)
        {
            if(!empty($groups[$temp]))
            {
                return true;
                break;
            }
        }
 
        return false;
    }

    static function add_moodle_users ($user_ids)
    {
        foreach ($user_ids as $id)
        {
            /* If user not already in Joomla, warn user and continue to next item */
            if ($id < 0)
            {
                $error = JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA' ) . ": " . $id;
                JFactory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            $user = JFactory::getUser($id);
            /* If user already in Moodle, warn user and continue to next item */
            if (JoomdleHelperContent::call_method ('user_exists', $user->username))
            {
                $error = JText::_( 'COM_JOOMDLE_USER_ALREADY_EXISTS_IN_MOODLE' ) . ": " . $user->username;
                JFactory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            JoomdleHelperContent::call_method ('create_joomdle_user', $user->username);

            // Save user to trigger user events
            $user->save ();
        }
    }

    static function migrate_users_to_joomdle ($user_ids)
    {
        foreach ($user_ids as $id)
        {
            /* If user not already in Joomla, warn user and continue to next item */
            if ($id < 0)
            {
                $error = JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA' ) . ": " . $id;
                JFactory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            $user = JFactory::getUser($id);
            /* If user not already in Moodle, warn user and continue to next item */
            if (!JoomdleHelperContent::call_method ('user_exists', $user->username))
            {
                $error = JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE' ) . ": " . $user->username;
                JFactory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            JoomdleHelperContent::call_method ('migrate_to_joomdle', $user->username);
        }
    }

    static function sync_moodle_profiles ($user_ids)
    {
        foreach ($user_ids as $id)
        {
            /* If user not already in Joomla, warn user and continue to next item */
            if ($id < 0)
            {
                $error = JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA' ) . ": " . $id;
                JFactory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            $user = JFactory::getUser($id);
            /* If user not already in Moodle, warn user and continue to next item */
            if (!JoomdleHelperContent::call_method ('user_exists', $user->username))
            {
                $error = JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE' ) . ": " . $user->username;
                JFactory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            JoomdleHelperContent::call_method ('create_joomdle_user', $user->username);
        }
    }

    static function sync_joomla_profiles ($user_ids)
    {
        foreach ($user_ids as $id)
        {
            /* If user not already in Joomla, warn user and continue to next item */
            if ($id < 0)
            {
                $error = JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_JOOMLA' ) . ": " . $id;
                JFactory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            $user = JFactory::getUser($id);
            /* If user not already in Moodle, warn user and continue to next item */
            if (!JoomdleHelperContent::call_method ('user_exists', $user->username))
            {
                $error = JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE' ) . ": " . $user->username;
                JFactory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            JoomdleHelperMappings::sync_user_to_joomla ( $user->username);
        }
    }

    static function create_joomla_users ($user_ids)
    {
        foreach ($user_ids as $id)
        {
            /* If user already in Joomla, warn user and continue to next item */
            if ($id >= 0)
            {
                $error = JText::_( 'COM_JOOMDLE_USER_ALREADY_EXISTS_IN_JOOMLA' ) . ": " . $id;
                JFactory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            /* Here we already know ID is from Moodle, as it is not from Joomla */
            $moodle_user = JoomdleHelperContent::call_method ('user_details_by_id', -$id); //We remove the minus
            if (!$moodle_user)
            {
                $error = JText::_( 'COM_JOOMDLE_USER_ID_DOES_NOT_EXIT_IN_MOODLE' ) . ": " . $user->username;
                JFactory::getApplication()->enqueueMessage($error, 'error');
                continue;
            }
            $username = $moodle_user['username'];
            JoomdleHelperContent::create_joomla_user ($username);
        }
    }

    static function create_joomla_user ($username)
    {
        $mainframe = JFactory::getApplication( 'site' );

        $db           = JFactory::getDBO();
        // Get required system objects
        $user           = clone(JFactory::getUser());
        $config         = JFactory::getConfig();

        $usersConfig    = JComponentHelper::getParams( 'com_users' );
        $useractivation = $usersConfig->get( 'useractivation' );

        $newUsertype = 'Registered';

        $moodle_user['username'] = $username;
        $user_details =JoomdleHelperContent::call_method ('user_details', $username);

        $moodle_user['name'] = $user_details['firstname'] .' '.$user_details['lastname'];
        $moodle_user['email'] = $user_details['email'];

        $moodle_user['activation'] = '';
        $moodle_user['sendEmail'] = 0;

        // Bind the post array to the user object
        if (!$user->bind( $moodle_user, 'usertype' )) {
                JFactory::getApplication()->enqueueMessage($user->getError (), 'error');
                return;
        }

        // Set some initial user values
        $user->set('id', 0);
        $user->set('usertype', $newUsertype);

        $system = 2; // ID of Registered
        $user->groups = array ();
        $user->groups[] = $system;


        $date = JFactory::getDate();
        $user->set('registerDate', $date->toSql());

        jimport('joomla.user.helper');

        $user->set('lastvisitDate', $db->getNullDate());

        // If there was an error with registration, set the message and display form
        if ( !$user->save() )
        {
                $error = JText::_( JText::_( $user->getError()) );
                JFactory::getApplication()->enqueueMessage($error, 'error');
                return false;
        }

        // Manually store password from Moodle
        $user->password = $user_details['password'];
        $user->save ();
    }

    static function getJumpURL ()
    {
        $params = JComponentHelper::getParams( 'com_joomdle' );
        $moodle_auth_land_url = $params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';

         $linkstarget = $params->get( 'linkstarget' );
         if ($linkstarget == 'wrapper')
             $use_wrapper = 1;
         else $use_wrapper = 0;

        $user = JFactory::getUser();
        $id = $user->get('id');
        $username = $user->get('username');

        if (!$username)
            $username = 'guest';

        $db           = JFactory::getDBO();
        $query = 'SELECT session_id' .
            ' FROM #__session' .
            ' WHERE userid =';
        $query .= "'$id'";
        $db->setQuery($query);
        $sessions = $db->loadObjectList();

        if (count($sessions))
        {
            foreach ($sessions as $session)
            {
                $token = md5 ($session->session_id);
            }
        }

        $jump_url = $moodle_auth_land_url."?username=$username&token=$token&use_wrapper=$use_wrapper";

        return $jump_url;
    }

    static function getMenuItem ()
    {
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        $menuItem = $menu->getActive();

        if (!$menuItem)
            return;

        $itemid = $menuItem->id;

        return $itemid;
    }

    static function get_language_str ($lang)
    {
        require_once (dirname(__FILE__).'/'.'languages.php');
        $l = explode ("_", $lang);
        $index = $l[0];

        return $LANGUAGES["$index"];
    }

    static function user_id_exists ($id)
    {
        $db           = JFactory::getDBO();

        $id = $db->Quote ($id);
        $query = "SELECT id from #__users where id=$id";
        $db->setQuery($query);
        $users = $db->loadObjectList();

        if ($db->getErrorNum()) {
            $error = JText::_( $db->stderr() );
            JFactory::getApplication()->enqueueMessage($error, 'error');
        }

        return (count ($users) != 0);
    }

    static function system_ok ()
    {
        $php_exts = get_loaded_extensions ();
        $xmlrpc_enabled = in_array ('xmlrpc', $php_exts);

        if (!$xmlrpc_enabled)
            return false;

        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $connection = $comp_params->get( 'connection_method' );

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
        else if ((is_array ($response)) && (xmlrpc_is_fault ($response)))
            return false;
        else {
            if ((!array_key_exists ('joomdle_auth', $response)) || ($response ['joomdle_auth'] != 1))
                return false;
            else if ((!array_key_exists ('joomdle_configured', $response)) || ($response ['joomdle_configured'] == 0))
                return false;
            else if ((!array_key_exists ('test_data', $response)) || ($response ['test_data'] != 'It works'))
                return false;
        }

        return true;
    }

    static function check_joomdle_system ()
    {
        $joomla_config = new JConfig();

        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $ws_protocol = $comp_params->get( 'ws_protocol' );

        if ($ws_protocol == 'xmlrpc')
        {
            /* PHP XMLRPC extension enabled */
            $php_exts = get_loaded_extensions ();
            $xmlrpc_enabled = in_array ('xmlrpc', $php_exts);
            $system[2]['description'] = JText::_ ('COM_JOOMDLE_XMLRPC_PHP_EXTENSION');
            $system[2]['value'] = $xmlrpc_enabled;
            if ($system[2]['value'] == '0')
                $system[2]['error'] =  JText::_ ('COM_JOOMDLE_XMLRPC_PHP_EXTENSION_ERROR');
            else $system[2]['error'] = '';
        }

        /* Error reporting */
        /*
        $display_errors = ini_get('display_errors');
        $error_reporting = ini_get('error_reporting');
        $system[6]['description'] = JText::_ ('COM_JOOMDLE_ERROR_REPORTING');

        if (($display_errors) && ($error_reporting & E_DEPRECATED))
        {
            $system[6]['error'] =  JText::_ ('COM_JOOMDLE_DEPRECATED_ERRORS_ON');
            $system[6]['value'] = 0;
        }
        else
        {
            $system[6]['error'] = '';
            $system[6]['value'] = 1;
        }
*/

        /*
        $system[0]['description'] = JText::_ ('COM_JOOMDLE_JOOMLA_WEB_SERVICES');
        $system[0]['value'] = $joomla_config->xmlrpc_server;
        if ($joomla_config->xmlrpc_server == '0')
            $system[0]['error'] =  JText::_ ('COM_JOOMDLE_JOOMLA_WEB_SERVICES_ERROR');
        else $system[0]['error'] = '';
*/
        /* Mandatory Joomdle plugins enabled */

        $system[5]['description'] = JText::_ ('COM_JOOMDLE_JOOMDLEHOOKS_PLUGIN');
        $system[5]['value'] = JPluginHelper::isEnabled ('user', 'joomdlehooks');
        if (JPluginHelper::isEnabled ('user', 'joomdlehooks') != '1')
            $system[5]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLEHOOKS_PLUGIN_ERROR');
        else $system[5]['error'] = '';

        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        $connection = $comp_params->get( 'connection_method' );

        if ($connection == 'fgc')
        {
            /* file_get_contents call.  Test to see if allow_url_fopen PHP option is enabled */
            $system[1]['description'] = JText::_ ('COM_JOOMDLE_ALLOW_URL_FOPEN');
            $system[1]['value'] = ini_get ('allow_url_fopen');
            if ($system[1]['value'] != '1')
                $system[1]['error'] =  JText::_ ('COM_JOOMDLE_ALLOW_URL_FOPEN_ERROR');
            else $system[1]['error'] = '';
        }
        else if ($connection == 'curl')
        {
            $system[1]['description'] = JText::_ ('COM_JOOMDLE_CURL_ENABLED');
            $system[1]['value'] = function_exists('curl_version') == "Enabled";
            if (!$system[1]['value'])
                $system[1]['error'] =  JText::_ ('COM_JOOMDLE_CURL_ENABLED_ERROR');
            else $system[1]['error'] = '';
        }

        if ($system[1]['error'] != '')
        {
            /* If no working connection, no need to continue */
            return $system;
        }

        // Check bare HTTP connection
        $moodle_url = $comp_params->get( 'MOODLE_URL');
        $moodle_file_url = $moodle_url . '/auth/joomdle/connection_test.php';
        $joomla_file_url = $moodle_url . '/auth/joomdle/connection_test_joomla.php';

        // Joomla to Moodle
        $result = JoomdleHelperContent::get_file ($moodle_file_url);
        $system[6]['description'] = JText::_ ('COM_JOOMDLE_JOOMDLE_JOOMLA_TO_MOODLE_CONNECTION');
        if (strncmp ($result, 'OK', 2) != 0)
        {
            $system[6]['value'] = 0;
            $system[6]['error'] =  JText::_ ('COM_JOOMDLE_JOOMLA_CANNOT_CONNECT_TO_MOODLE');
        }
        else
        {
            $system[6]['value'] = 1;
            $system[6]['error'] =  '';
        }

        // Moodle to Joomla
        $result = JoomdleHelperContent::get_file ($joomla_file_url);
        $system[7]['description'] = JText::_ ('COM_JOOMDLE_JOOMDLE_MOODLE_TO_JOOMLA_CONNECTION');
        if (strncmp ($result, 'OK', 2) != 0)
        {
            $system[7]['value'] = 0;
            $system[7]['error'] =  JText::_ ('COM_JOOMDLE_MOODLE_CANNOT_CONNECT_TO_JOOMLA');
        }
        else
        {
            $system[7]['value'] = 1;
            $system[7]['error'] =  '';
        }

        // Get installed Joomdle version in Joomla
        $xmlfile = JPATH_ADMINISTRATOR.'/components/com_joomdle/joomdle.xml';
        if (file_exists($xmlfile))
        {
            if ($data = Installer::parseXMLInstallFile($xmlfile)) {
                $version =  $data['version'];
            }
        }
        else $version = '';

        $joomdle_release_joomla = $version;

        /* Test Moodle Web services in joomdle plugin */
        $system[3]['description'] = JText::_ ('COM_JOOMDLE_JOOMDLE_WEB_SERVICES');
        $response = JoomdleHelperContent::call_method_debug ('system_check');
        if ($response == '')
        {
            $system[3]['value'] = 0;
            $system[3]['error'] =  JText::_ ('COM_JOOMDLE_EMPTY_RESPONSE_FROM_MOODLE');
        }
        else if ((is_array ($response)) && (array_key_exists ('exception', $response)))
        {
            $system[3]['value'] = 0;
            $system[3]['error'] =  $response['message'];

            if (array_key_exists ('debuginfo', $response))
                $system[3]['error'] .= ' ' . $response['debuginfo'];
        }
        else if (($ws_protocol == 'xmlrpc') && (is_array ($response)) && (array_key_exists ('faultString', $response)))
        {
            $system[3]['value'] = 0;
            $system[3]['error'] =  $response['faultString'];
        }
        else {
            if ($response ['joomdle_auth'] != 1)
            {
                $system[3]['value'] = 0;
                $system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLE_AUTH_NOT_ENABLED');
            }
            else if ($response['joomdle_configured'] == 0)
            {
                $system[3]['value'] = 0;
                $system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMLA_URL_NOT_CONFIGURED_IN_MOODLE_PLUGIN');
            }
            else if ($response['test_data'] != 'It works')
            {
                if ($response['test_data'] == '')
                {
                    $system[3]['value'] = 0;
                    $system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMLA_URL_MISCONFIGURED_IN_MOODLE_PLUGIN');
                }
                else
                {
                    $system[3]['value'] = 0;
                    $system[3]['error'] = $response['test_data'];
                }
            }
            else if ($response['release'] != $joomdle_release_joomla)
            {
                    $system[3]['value'] = 0;
                    $system[3]['error'] =  JText::_ ('COM_JOOMDLE_JOOMDLE_VERSION_MISMATCH');
            }
            else {
                $system[3]['value'] = 1;
                $system[3]['error'] = '';
            }
        }

        return $system;
    }

    static function get_course_url ($course_id)
    {
        $mainframe = JFactory::getApplication( 'site' );

        $user = JFactory::getUser ();
        $username = $user->username;

        if (!$user->id)
            $username = 'guest';

        $session                = JFactory::getSession();
        $token = md5 ($session->getId());

        $itemid   = JFactory::getApplication()->input->get('Itemid');
        $params = $mainframe->getParams();

        $comp_params = JComponentHelper::getParams( 'com_joomdle' );
        if ($comp_params->get( 'goto_course_button' ) == 'moodle')
        {
            if ($comp_params->get( 'linkstarget' ) == 'wrapper')
                $open_in_wrapper = 1;
            else
                $open_in_wrapper = 0;
            $moodle_auth_land_url = $comp_params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';

            $url = $moodle_auth_land_url."?username=$username&token=$token&mtype=course&id=$course_id&use_wrapper=$open_in_wrapper&Itemid=$itemid";
        }
        else // link to course Joomdle view
        {
            $itemid = $params->get( 'courseview_itemid');
            $url = JRoute::_("index.php?option=com_joomdle&view=course&course_id=$course_id&Itemid=$itemid");
        }

        return $url;
    }

    static function can_enrol ($course_id)
    {
        $enrol_methods = JoomdleHelperContent::call_method ('course_enrol_methods', $course_id);

        $self_ok = false;
        foreach ($enrol_methods as $method)
        {
            if ($method['enrol'] == 'self')
            {
                $self_ok = true;
                break;
            }
        }

        return $self_ok;
    }

    static function get_enrol_instance_id ($course_id, $enrol_method)
    {
        $enrol_methods = JoomdleHelperContent::call_method ('course_enrol_methods', $course_id);

        foreach ($enrol_methods as $method)
        {
            if ($method['enrol'] == $enrol_method)
            {
                break;
            }
        }

        return $method['id'];
    }


    static function in_enrol_date ($course_id)
    {
        $enrol_methods = JoomdleHelperContent::call_method ('course_enrol_methods', $course_id);

        $now = time();
        $in = true;
        foreach ($enrol_methods as $method)
        {
            if (($method['enrolstartdate']) && ($method['enrolenddate']))
            {
                $in = false;
                if (($method['enrolstartdate'] <= $now) && ($method['enrolenddate'] >= $now))
                {
                    $in = true;
                    break;
                }
            }
        }

        return $in;
    }

    static function get_lang ()
    {
        $client_lang = '';
        $lang_known = false;
        $lang   = JFactory::getApplication()->input->get('lang');

        if ($lang)
        {
            //lang set via GET/POST
            $client_lang = $lang;
            $lang_known = true;

        }

        if ($lang_known)
            return $client_lang;
        else return false;
    }

}
