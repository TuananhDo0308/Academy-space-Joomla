<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die;

class JoomdleRouter extends JComponentRouterView
{
    protected $noIDs = false; // Not supported, as we cannot just look at the database to find the alias

    /**
     * Search Component router constructor
     *
     * @param   JApplicationCms  $app   The application object
     * @param   JMenu            $menu  The menu object to work with
     */
    public function __construct($app = null, $menu = null)
    {
        $params = JComponentHelper::getParams('com_joomdle');
        $categories = new JComponentRouterViewconfiguration('coursecategories');
        $categories->setKey('cat_id');
        $this->registerView($categories);
        $category = new JComponentRouterViewconfiguration('coursecategory');
        $category->setKey('cat_id')->setParent($categories, 'cat_id')->setNestable();
        $this->registerView($category);

        $courses = new JComponentRouterViewconfiguration('joomdle');
        $courses->setKey('course_id');
        $this->registerView($courses);

        $detail = new JComponentRouterViewconfiguration('detail');
        $detail->setKey('course_id')->setParent($courses, 'cat_id');
        $this->registerView($detail);

        // We cannot have a view as a parent for several views, so we cannot set parent in these ones
        $this->registerView(new JComponentRouterViewconfiguration('topics'));
        $this->registerView(new JComponentRouterViewconfiguration('course'));
        $this->registerView(new JComponentRouterViewconfiguration('coursegradecategories'));
        $this->registerView(new JComponentRouterViewconfiguration('teachers'));

        $this->registerView(new JComponentRouterViewconfiguration('mycoursegrades'));
        $this->registerView(new JComponentRouterViewconfiguration('coursemates'));
        $this->registerView(new JComponentRouterViewconfiguration('coursestats'));
        $this->registerView(new JComponentRouterViewconfiguration('myapplications'));
        $this->registerView(new JComponentRouterViewconfiguration('mycompletedcourses'));

        $coursegrades = new JComponentRouterViewconfiguration('coursegrades');
        $coursegrades->setKey('course_id');
        $this->registerView($coursegrades);

        $coursenews = new JComponentRouterViewconfiguration('coursenews');
        $coursenews->setKey('course_id');
        $this->registerView($coursenews);
        $newsitem = new JComponentRouterViewconfiguration('newsitem');
        $newsitem->setKey('id')->setParent($coursenews, 'course_id');
        $this->registerView($newsitem);

        $this->registerView(new JComponentRouterViewconfiguration('coursesabc'));
        $this->registerView(new JComponentRouterViewconfiguration('coursesbycategory'));
        $this->registerView(new JComponentRouterViewconfiguration('mycourses'));
        $this->registerView(new JComponentRouterViewconfiguration('myevents'));
        $this->registerView(new JComponentRouterViewconfiguration('teachersabc'));
        $this->registerView(new JComponentRouterViewconfiguration('mygrades'));
        $this->registerView(new JComponentRouterViewconfiguration('buycourse'));
        $this->registerView(new JComponentRouterViewconfiguration('courseevents'));
        $this->registerView(new JComponentRouterViewconfiguration('childrengrades'));
        $this->registerView(new JComponentRouterViewconfiguration('mynews'));
        $this->registerView(new JComponentRouterViewconfiguration('page'));
        $this->registerView(new JComponentRouterViewconfiguration('wrapper'));
        $this->registerView(new JComponentRouterViewconfiguration('mycertificates'));
        $this->registerView(new JComponentRouterViewconfiguration('menteescertificates'));

        $assigncourses = new JComponentRouterViewconfiguration('assigncourses');
        $this->registerView($assigncourses);
        $register = new JComponentRouterViewconfiguration('register');
        $register->setParent($assigncourses);
        $this->registerView($register);

        parent::__construct($app, $menu);

        $this->attachRule(new JComponentRouterRulesMenu($this));

        if ($params->get('sef_advanced', 1))
        {
            $this->attachRule(new JComponentRouterRulesStandard($this));
            $this->attachRule(new JComponentRouterRulesNomenu($this));
        }
        else
        {
            require_once (JPATH_ADMINISTRATOR . '/components/com_joomdle/helpers/legacyrouter.php'); // Needed for J3.x
            JLoader::register('JoomdleRouterRulesLegacy', __DIR__ . '/helpers/legacyrouter.php');
            $this->attachRule(new JoomdleRouterRulesLegacy($this));
        }
    }

    /**
     * Method to get the segment(s) for a category
     *
     * @param   string  $id     ID of the category to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getCoursecategorySegment($id, $query)
    {
        $cat_id = (int) $id;
        $path = array ($cat_id => $id,  0 => '0:root'); // It seems '0:root' is needed here
        return $path;
    }

    /**
     * Method to get the segment(s) for a category
     *
     * @param   string  $id     ID of the category to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getCoursecategoriesSegment($id, $query)
    {
        return $this->getCoursecategorySegment($id, $query);
    }

    /**
     * Method to get the segment(s) for a contact
     *
     * @param   string  $id     ID of the contact to retrieve the segments for
     * @param   array   $query  The request that is built right now
     *
     * @return  array|string  The segments of this item
     */
    public function getDetailSegment($id, $query)
    {
        return array((int) $id => $id);
    }

    public function getJoomdleSegment($id, $query)
    {
        $course_id = (int) $id;
        $path = array ($course_id => $id);
        return $path;
    }

    /**
     * Method to get the id for a category
     *
     * @param   string  $segment  Segment to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getCoursecategoryId($segment, $query)
    {
        return (int) $segment;
    }

    /**
     * Method to get the segment(s) for a category
     *
     * @param   string  $segment  Segment to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getCoursecategoriesId($segment, $query)
    {
        return $this->getCoursecategoryId($segment, $query);
    }

    /**
     * Method to get the segment(s) for a contact
     *
     * @param   string  $segment  Segment of the contact to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getDetailId($segment, $query)
    {
        return (int) $segment;
    }
}
