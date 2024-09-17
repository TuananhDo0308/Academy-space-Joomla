<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.jmlms
 *
 * @copyright
 * @license     MIT
 */

defined('_JEXEC') or die;

/**
 * Jmlms plugin class.
 *
 * @since  2.5
 * @link https://docs.joomla.org/Plugin/Events/System
 */
class PlgSystemJmlms extends JPlugin{
 

  public function onJ2StoreAfterOrderstatusUpdate($order, $new_status)
    {    	
        $confirmed_status_id = 1;
        $confirmed_status_id = $this->params->get('order_status',1);
        if ($new_status != $confirmed_status_id) {
            return; // just return
        }
        $user = JFactory::getUser($order->user_id);
	 	$params = JComponentHelper::getParams('com_joomonklms');
		$api_key= $params->get('webinar_api','');
		$access_token= $params->get('access_token','');
		$organizer_key= $params->get('organizer_key','');
        // get order items  
        $user_id = $order->user_id;
        // print_r($user_id);
        // exit;
        $items   = $order->getItems();
        if ($user_id <= 0) {
            return;
        }
        foreach ($items as $item) {
            $published = 0;
            if ($new_status == $confirmed_status_id) {
                $published = 1;
            }
        	$db    = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__joomonklms_courses');
            $query->where('j2store_product_id' . ' = ' . $item->product_id);
            $db->setQuery($query);
            $results   = $db->loadObjectList();
            $course_id = $results[0]->id;
 			$cat_id=(int) $results[0]->coursecategory_id;

 			
            // if exists or not
            $db = JFactory::getDbo();
            $lms_query = $db->getQuery(true);
            $lms_query->select('*');
            $lms_query->from('#__joomonklms_courses_access');
            $lms_query->where('course_id' . '=' .$db->q($course_id));
            $lms_query->where('user_id' . '=' . $db->q($user_id));
            $db->setQuery($lms_query);
            $joomonklms_access = $db->loadObjectList();
            // $

            if (empty($joomonklms_access)) {            
            //Register to Webinar
           		$db = JFactory::getDbo();
               	
               	$reg_key=null;
    			$join_url=null;

				//Course insertion
 				 $columns   = array('user_id', 'course_id', 'order_id', 'webinar_join_id','webinar_join_link');
          		 $values    = array($user_id, $course_id, $item->order_id,$db->q($reg_key),$db->q($join_url));
           		 // Prepare the insert query.
                $insert_query = $db->getQuery(true);
                $insert_query->insert($db->quoteName('#__joomonklms_courses_access'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $db->quote($values))); // Set the query using our newly populated query object and execute it.
                $db->setQuery($insert_query);
                $db->execute();
				//Course inserting lesson insertion
                //select lessons for this course_id
	            $db    = JFactory::getDbo();
	            $query = $db->getQuery(true);
	            $query->select('*');
	            $query->from('#__joomonklms_lessons');
	            $query->where('course_id' . ' = ' . $db->quote($course_id));
	            $db->setQuery($query);
	            $lessons   = $db->loadObjectList();
	            $status=1;
	            $immediate_date=new JDate('now');
	            $immediate_date=$immediate_date;
	            
	            $delay_date=new JDate('now');
	            // $lesson_columns   = array('user_id', 'course_id','lesson_id', 'order_id', 'status');
	            $lesson_columns   = array('user_id', 'course_id','lesson_id', 'order_id', 'status','activation_date');
	            foreach ($lessons as $lesson) {
	          		//find lesson exist or not
	          		$db    = JFactory::getDbo();
		            $query = $db->getQuery(true);
		            $query->select('*');
		            $query->from('#__joomonklms_lessons_access');
		   			$query->where('user_id' , '=' , $user_id);	
		            $query->where('course_id' , '=' , $course_id);
		            $query->where('lesson_id' , ' = ' , $lesson->id);
		            $db->setQuery($query);
	                $db->execute();
		            $lesson_access   = $db->loadObjectList();
		            // if lesson access empty
		            if(empty($lesson_access)){
		            	// if access_type delay
	          			if($lesson->access_type == 'delay'){
	          				// if lesson first lesson access immediately
	          				$activation_date=$immediate_date;
 						//if given lesson not a first lesson in course acces delayed
	          				if($status == 0){
	          					$delay_date=new JDate($delay_date .'+'.$lesson->access_day.'day');
	          					$activation_date=$delay_date;
			            	}
	          			}
	          			else{ //access_type immediate
	          				$activation_date=$immediate_date;
	          				$status = 1;
	          			}
	          			
	          			$lesson_values    = array($user_id, $course_id,$lesson->id,$item->order_id,$status,$activation_date);
	          			
		          		$insert_query = $db->getQuery(true);
		                $insert_query->insert($db->quoteName('#__joomonklms_lessons_access'))
		                    ->columns($db->quoteName($lesson_columns))
		                    ->values(implode(',', $db->q($lesson_values)));                  
		             	// Set the query using our newly populated query object and execute it.
		                $db->setQuery($insert_query);
		                $db->execute();
		                $lesson_access_id = $db->insertid();
		               
		                if($status == 0){
		                // inserting data into cron table`
	            		$cron_columns   = array('user_id','lesson_access_id','activation_date');
	          			$cron_values    = array($user_id, $lesson_access_id,$activation_date);
	          			$db1    = JFactory::getDbo();
	          			$cron_query = $db1->getQuery(true);
		                // $cron_query->insert($db1->quoteName('#__joomonklms_lessonaccess_cron'))
		                $cron_query->insert($db1->quoteName('#__joomonklms_access_cron'))
		                    ->columns($db1->quoteName($cron_columns))
		                    ->values(implode(',', $db1->q($cron_values)));                  
		             	// Set the query using our newly populated query object and execute it.
		                $db1->setQuery($cron_query);
		                $db1->execute();
		            		}
		          		$status=0;
		            	}
	           		 }
           		}
        	}
 	// added history
        $order->add_history('Course access has been provided');
    }

  public function onJ2StoreAfterGetProduct(&$product) {


		if(isset($product->product_source) && $product->product_source == 'com_joomonklms' ) {

			

			static $sets;
			
			if(!is_array($sets)) {
				$sets = array();
			}

			// query the course using course id 

			$content = $this->getCourse($product->product_source_id);

			if(isset($content->id) && $content->id) {
				
				$product->source = $content;
				$product->product_name = $content->title;
				$product->product_short_desc = $content->short_desc;
				$product->product_long_desc = $content->description;

				// TODO: create the product edit url to support from backend
				$product->product_edit_url = JRoute::_('index.php?option=com_joomonklms&task=course.edit&id='.$content->id);

				$product->product_view_url = JRoute::_('index.php?option=com_joomonklms&task=course&id='.$content->id);

				if($content->state == 1 ) {
					$product->exists = 1;
				} else {
					$product->exists = 0;
				}

				$sets[$product->product_source][$product->product_source_id] = $content;
			} else {
				$product->exists = 0;
			}

		}
	}

	/**
	 * get the course details to set in the product and send 
	 * @param $course_id int 
	 **/
	function getCourse($course_id=0){
		if($course_id == 0){
			return null;
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*,cat.title as category');
		$query->from('#__joomonklms_courses AS a');
		$query->join('LEFT', '#__categories AS cat ON cat.id = a.coursecategory_id');
		$query->where($db->quoteName('a.id')." = ".$db->quote($course_id));
		$db->setQuery($query);
		$item = $db->loadObject();
		return $item; 
	}

}
