<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die;


jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of user records.
 *
 */
class JoomdleModelUsers extends JModelList
{

    /**
     * Internal memory based cache array of data.
     *
     * @var    array
     * @since  1.6
     */
    protected $cache = array();


    private $items;

    /**
     * Constructor.
     *
     * @param   array   An optional associative array of configuration settings.
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'name', 'name',
                'username', 'username',
                'email', 'email',
                'state', 'state',
                'group_id'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return  void
     * @since   1.6
     */
    //protected function populateState($ordering = null, $direction = null)
    protected function populateState($ordering = 'name', $direction = 'asc')
    {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Adjust the context to support modal layouts.
        if ($layout = JFactory::getApplication()->input->get('layout', 'default')) {
            $this->context .= '.'.$layout;
        }

        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '');
        $group_id = $this->getUserStateFromRequest($this->context.'.filter.group_id', 'filter_group_id', '');

        if ($state == '')
        {
            // XXX Check for sites with lots of users: this may hang this page until timeout if we show all users
            // XXX so we use a safe filter instead
            $total_moodle = $this->getMoodleUsersNumber ($search);
            $total_joomla = $this->getJoomlaUsersNumber ($search, $group_id);

            $max_users = 1000;
            if (($total_joomla > $max_users) || ($total_moodle > $max_users))
                $state = 'joomla';

        }
        $this->setState('filter.state', $state);

        $this->setState('filter.group_id', $this->getUserStateFromRequest($this->context . '.filter.group_id', 'filter_group_id', null, 'int'));

        // Load the parameters.
        $params     = JComponentHelper::getParams('com_joomdle');
        $this->setState('params', $params);

        // List state information.
        //parent::populateState('name', 'asc');
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string      $id A prefix for the store id.
     *
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':'.$this->getState('filter.search');
        $id .= ':'.$this->getState('filter.state');
        $id .= ':'.$this->getState('filter.group_id');

        return parent::getStoreId($id);
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   1.6
     */
    public function getItems()
    {
        // Get a storage key.
        $store = $this->getStoreId();

        // Try to load the data from internal storage.
        if (isset($this->cache[$store]))
        {
            return $this->cache[$store];
        }

        try
        {
            // Load the list items and add the items to the internal cache.
            $this->cache[$store] = $this->_getData();
        }
        catch (\RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        return $this->cache[$store];
    }

    public function _getData()
    {
        $db     = $this->getDbo();
        $search = $this->getState ('filter.search');
        if ($search != '')
            $searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
        else $searchEscaped = "";

        $pagination = $this->getPagination ();
        $limitstart = $pagination->limitstart;
        $limit = $pagination->limit;

        $listOrder  = $this->state->get('list.ordering');
        $listDirn   = $this->state->get('list.direction');
        $filter_order = $listOrder;
        $filter_order_Dir = $listDirn;

        $group_id = $this->state->get ('filter.group_id');

        $filter_type = $this->getState ('filter.state');
        switch ($filter_type)
        {

            case 'moodle':
                $users = $this->getMoodleUsers ($limitstart, $limit,$filter_order, $filter_order_Dir, $search, $group_id);
                break;
            case 'joomla':
                $users = $this->getJoomlaUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $search, $group_id);
                break;
            case 'joomdle':
                $users = $this->getJoomdleUsers ($limitstart, $limit,  $filter_order, $filter_order_Dir, $search, $group_id);
                break;
            case 'not_joomdle':
                $users = $this->getNotJoomdleUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $search, $group_id);
                break;
            default:
                $users = $this->getAllUsers ($limitstart, $limit, $filter_order, $filter_order_Dir, $search, $group_id); 
                break;
        }

        return $users;
    }

    protected function getListQuery()
    {
        //Note: this does nothing useful for us now, as we cannot pull data via a simple DB query,  but it seems needed

        // Create a new query object.
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.*'
            )
        );
        $query->from('#__users AS a');

        return $query;
    }

    function getTotal ()
    {
        $db     = $this->getDbo();
        $search = $this->getState ('filter.search');
        $filter_type = $this->state->get ('filter.state');
        $group_id = $this->state->get ('filter.group_id');
        switch ($filter_type)
        {
            case 'moodle':
                $total = $this->getMoodleUsersNumber ($search);
                break;
            case 'joomla':
                $total =  $this->getJoomlaUsersNumber ($search, $group_id);
                break;
            case 'joomdle':
                $total = count ($this->getJoomdleUsers (0, 0, 'username', 'asc', $search, $group_id));
                break;
            case 'not_joomdle':
                $total = count ($this->getNotJoomdleUsers (0, 0, 'username', 'asc', $search, $group_id));
                break;
            default:
                $total = count ($this->getAllUsers (0, 0, 'username', 'asc', $search, $group_id));
                break;
        }
        return $total;
    }

    /// XXX Ver si se puede hacer sin coger todos los de moodle... empezando por limite....XXX
    /* Note: Moodle only users have negative ID */
    private function getAllUsers ($limitstart, $limit, $order, $order_dir, $search, $group_id)
    {
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                'a.*'
        );

        $query->from('#__users AS a');

        $searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
        if ($search)
        {
            $query->where ('a.username LIKE ' . $searchEscaped, 'OR')
                ->where ('a.email LIKE ' . $searchEscaped, 'OR')
                ->where ('a.name LIKE ' . $searchEscaped);
        }

        if ($group_id)
        {
            $query->join('LEFT', '#__user_usergroup_map as ugm ON ugm.user_id = a.id');
            $query->select ('ugm.group_id');
            $query->where ('ugm.group_id = ' . $db->quote ($group_id));
        }

        $query->order($db->qn($db->escape($this->getState('list.ordering', 'a.name'))) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        $db->setQuery($query);
        $jusers = $db->loadObjectList();

        $ju_by_usernames = array ();
        foreach ($jusers as $user)
        {
            $ju_by_usernames[$user->username] = $user;
        }

        $musers = JoomdleHelperContent::call_method ('get_moodle_users', 0, 0, $order, $order_dir, $search);
        $mu_by_usernames = array ();
        if (is_array ($musers))
        {
            foreach ($musers as $user)
            {
                $mu_by_usernames[$user['username']] = $user;
            }
        }
        $rdo = array();
        foreach ($jusers as $user)
        {
            $item = get_object_vars ($user);
            $item['name_lower'] = strtolower($item['name']);;
            $item['username_lower'] = strtolower($item['username']);;
            $item['email_lower'] = strtolower($item['email']);;

            $item['j_account'] = 1;

            if (!array_key_exists ($user->username, $mu_by_usernames))
            {
                $item['m_account'] = 0;

                if (JoomdleHelperContent::is_admin ($user->id))
                    $item['admin'] = 1;
                else $item['admin'] = 0;

                $item['auth'] = 'N/A';
            }
            else
            {
                // User in Joomla and Moodle
                $item['m_account'] = 1;

                if (!$mu_by_usernames[$user->username]['admin'])
                {
                    if (JoomdleHelperContent::is_admin ($user->id))
                        $item['admin'] = 1;
                    else $item['admin'] = 0;
                }
                else $item['admin'] = 1;

                $item['auth'] = $mu_by_usernames[$user->username]['auth'];
            }

            $rdo[] = $item;
        }

        // If there is a Joomla group selected in filter, we don't show Moodle only users
        if (!$group_id)
        {
            // Get Moodle only users: those without a Joomla account
            $rdo2 =  array ();
            if (is_array ($musers)) 
            {
                foreach ($musers as $user)
                {
                    $item = array ();
                    $item = $user;
                    $item['m_account'] = 1;
                    if (!array_key_exists ($user['username'], $ju_by_usernames))
                    {
                        // User not found in Joomla -> not a Joomdle user
                        $item['j_account'] = 0;
                        $item['m_account'] = 1;

                        $item['id'] = - $user['id'];

                        $item['name_lower'] = strtolower($item['name']);;
                        $item['username_lower'] = strtolower($item['username']);;
                        $item['email_lower'] = strtolower($item['email']);;

                        $rdo2[] = $item;
                    }
                }
            }

            /* Kludge for uppercases */
            if ($order == 'name')
                $order = 'name_lower';
            if ($order == 'username')
                $order = 'username_lower';
            if ($order == 'email')
                $order = 'email_lower';

            $merged = array_merge ($rdo, $rdo2);
            $all = JoomdleHelperContent::multisort ($merged, $order_dir, $order, 'id', 'name', 'username', 'email', 'm_account', 'j_account',
                    'auth', 'admin');
        }
        else
            $all = $rdo;


        if ($limit)
            return array_slice ($all, $limitstart, $limit);
        else
            return $all;
    }

    private function getJoomlaUsers ($limitstart, $limit, $order, $order_dir, $search = "", $group_id = NULL)
    {
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                'a.*'
        );

        $query->from('#__users AS a');

        if ($group_id)
        {
            $query->join('LEFT', '#__user_usergroup_map as ugm ON ugm.user_id = a.id');
            $query->select ('ugm.group_id');
            $query->where ('ugm.group_id = ' . $db->quote ($group_id));
        }
        else $query->where ('1 = 1'); // Seems needed to chain next andWhere call
        
        $searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
        if ($search)
        {
            $query->andWhere (array ('a.username LIKE ' . $searchEscaped, 'a.email LIKE ' . $searchEscaped, 'a.name LIKE ' . $searchEscaped),'OR');
        }

        $searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );

        $query->order($db->qn($db->escape($this->getState('list.ordering', 'a.name'))) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        if ($limit)
            $query->setLimit ($limit, $limitstart);

        $db->setQuery($query);
        $jusers = $db->loadObjectList();

        $musers = JoomdleHelperContent::call_method ('get_moodle_users', 0, 0, $order, $order_dir, $search);
        $mu_by_usernames = array ();
        foreach ($musers as $user)
        {
            $mu_by_usernames[$user['username']] = $user;
        }

        $rdo = array();
        $i = 0;
        foreach ($jusers as $user)
        {
            $item = array ();
            $item = get_object_vars ($user);
            $item['j_account'] = 1;
            if (!array_key_exists ($user->username, $mu_by_usernames))
            {
                // User not in Moodle
                $item['m_account'] = 0;
                $item['auth'] = 'N/A';
            }
            else
            {
                // User in Moodle
                $item['m_account'] = 1;
                $item['auth'] = $mu_by_usernames[$user->username]['auth'];
            }

            if ((!$item['m_account']) || (!$mu_by_usernames[$user->username]['admin']))
            {
                if (JoomdleHelperContent::is_admin ($user->id))
                    $item['admin'] = 1;
                else $item['admin'] = 0;
            }
            else $item['admin'] = 1;

            $rdo[] = $item;
        }

        return ($rdo);
    }

    private function getJoomlaUsersNumber ($search = "", $group_id = NULL)
    {
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                'count(a.id) as n'
        );

        $query->from('#__users AS a');

        if ($group_id)
        {
            $query->join('LEFT', '#__user_usergroup_map as ugm ON ugm.user_id = a.id');
            $query->select ('ugm.group_id');
            $query->where ('ugm.group_id = ' . $db->quote ($group_id));
        }
        else $query->where ('1 = 1'); // Seems needed to chain next andWhere call
        
        $searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
        if ($search)
        {
            $query->andWhere (array ('a.username LIKE ' . $searchEscaped, 'a.email LIKE ' . $searchEscaped, 'a.name LIKE ' . $searchEscaped),'OR');
        }

        $db->setQuery($query);
        $n = $db->loadResult();

        return $n;
    }

    private function getMoodleUsersNumber ($search = "")
    {
        return  JoomdleHelperContent::call_method ('get_moodle_users_number', $search);
    }

    /* Note: Moodle only users have negative ID */
    private function getMoodleUsers ($limitstart = 0, $limit = 20, $order = "", $order_dir = "", $search = "", $group_id = 0)
    {
        if ($group_id)
        {
            // If a Joomla group is selected, we need to fetch all Moodle users and then remove those not in group
            $users = JoomdleHelperContent::call_method ('get_moodle_users', 0, 0, $order, $order_dir, $search);
        }
        else
        {
            $users = JoomdleHelperContent::call_method ('get_moodle_users', $limitstart, $limit, $order, $order_dir, $search);
        }

        if (!is_array ($users))
            return array();

        $u = array ();
        foreach ($users as $user)
        {

            $user['id'] = -$user['id'];
            $user['m_account'] = 1;

            $id = JUserHelper::getUserId($user['username']);
            if ($id)
            {
                $user_obj = JFactory::getUser($id);

                // If a group is selected, skip all users not in group
                if (($group_id) && (!in_array ($group_id, $user_obj->groups)))
                        continue;

                if (!$user['admin'])
                {
                    // If not moodle admin, check if joomla admin
                    if (JoomdleHelperContent::is_admin ($user_obj->id))
                        $user['admin'] = 1;
                    else $user['admin'] = 0;
                }

                $user['j_account'] = 1;
                $user['id'] = $id;
            }
            else
            {
                // If Joomla group selected, and user has no Joomla account, don't show
                if ($group_id)
                        continue;
                $user['j_account'] = 0;
            }

            $u[] = $user;
        }

        if ($limit)
            return array_slice ($u, $limitstart, $limit);
        else
            return $u;
    }

    private function getJoomdleUsers ($limitstart, $limit, $order, $order_dir, $search, $group_id)
    {
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                'a.*'
        );

        $query->from('#__users AS a');

        if ($group_id)
        {
            $query->join('LEFT', '#__user_usergroup_map as ugm ON ugm.user_id = a.id');
            $query->select ('ugm.group_id');
            $query->where ('ugm.group_id = ' . $db->quote ($group_id));
        }
        else $query->where ('1 = 1'); // Seems needed to chain next andWhere call
        
        $searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
        if ($search)
        {
            $query->andWhere (array ('a.username LIKE ' . $searchEscaped, 'a.email LIKE ' . $searchEscaped, 'a.name LIKE ' . $searchEscaped),'OR');
        }

        $query->order($db->qn($db->escape($this->getState('list.ordering', 'a.name'))) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        if ($limit)
            $query->setLimit ($limit, $limitstart);

        $db->setQuery($query);
        $jusers = $db->loadObjectList();

        $musers = JoomdleHelperContent::call_method ('get_moodle_users', 0, 0, $order, $order_dir, $search);
        $mu_by_usernames = array ();
        foreach ($musers as $user)
        {
            $mu_by_usernames[$user['username']] = $user;
        }

        $rdo = array();
        $i = 0;
        foreach ($jusers as $user)
        {
            $item = array ();
            $item = get_object_vars ($user);
            $item['j_account'] = 1;
            if (!array_key_exists ($user->username, $mu_by_usernames))
                continue; // User not in Moodle -> not a joomdle user

            // User does not have joomdle auth method -> not a joomdle user
            if ($mu_by_usernames[$user->username]['auth'] != 'joomdle')
                continue;

            $item['m_account'] = 1;

            if (JoomdleHelperContent::is_admin ($user->id))
                $item['admin'] = 1;
            else $item['admin'] = 0;

            $item['auth'] = $mu_by_usernames[$user->username]['auth'];

            $rdo[] = $item;
        }

        return $rdo;
    }

    /* Note: Moodle only users have negative ID */
    private function getNotJoomdleUsers ($limitstart, $limit, $order, $order_dir, $search, $group_id)
    {
        $db           = JFactory::getDBO();

        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                'a.*'
        );

        $query->from('#__users AS a');

        if ($group_id)
        {
            $query->join('LEFT', '#__user_usergroup_map as ugm ON ugm.user_id = a.id');
            $query->select ('ugm.group_id');
            $query->where ('ugm.group_id = ' . $db->quote ($group_id));
        }
        else $query->where ('1 = 1'); // Seems needed to chain next andWhere call
        
        $searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
        if ($search)
        {
            $query->andWhere (array ('a.username LIKE ' . $searchEscaped, 'a.email LIKE ' . $searchEscaped, 'a.name LIKE ' . $searchEscaped),'OR');
        }

        $query->order($db->qn($db->escape($this->getState('list.ordering', 'a.name'))) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

      //  if ($limit)
       //     $query->setLimit ($limit, $limitstart);

        $db->setQuery($query);
//echo $query->__tostring ();

        $jusers = $db->loadObjectList();

        $ju_by_usernames = array ();
        foreach ($jusers as $user)
        {
            $ju_by_usernames[$user->username] = $user;
        }

        $musers = JoomdleHelperContent::call_method ('get_moodle_users', 0, 0, $order, $order_dir, $search);
        $mu_by_usernames = array ();
        foreach ($musers as $user)
        {
            $mu_by_usernames[$user['username']] = $user;
        }

        $rdo = array ();
        foreach ($jusers as $user)
        {
            $item = array ();
            $item = get_object_vars ($user);
            $item['j_account'] = 1;
            if (!array_key_exists ($user->username, $mu_by_usernames))
            {
                // User not found in Moodle -> not a Joomdle user
                $item['m_account'] = 0;

                if (JoomdleHelperContent::is_admin ($user->id))
                    $item['admin'] = 1;
                else $item['admin'] = 0;

                $item['auth'] = 'N/A';
            }
            else
            {
                // User in Joomla and Moodle
                $item['m_account'] = 1;

                if (!$mu_by_usernames[$user->username]['admin'])
                {
                    if (JoomdleHelperContent::is_admin ($user->id))
                        $item['admin'] = 1;
                    else $item['admin'] = 0;
                }
                else $item['admin'] = 1;

                $item['auth'] = $mu_by_usernames[$user->username]['auth'];
            }

            if (($item['m_account'] == 1) && ($item['auth'] == 'joomdle'))
                continue;

            $rdo[] = $item;
        }

        // Get Moodle only users: those without a Joomla account
        $rdo2 =  array ();
        foreach ($musers as $user)
        {
            $item = array ();
            $item = $user;
            $item['m_account'] = 1;
            if (!array_key_exists ($user['username'], $ju_by_usernames))
            {
                // User not found in Joomla -> not a Joomdle user
                $item['j_account'] = 0;
                $item['m_account'] = 1;

                $item['id'] = - $user['id'];

                $rdo2[] = $item;
            }
        }

        $merged = array_merge ($rdo, $rdo2);
        $all = JoomdleHelperContent::multisort ($merged, $order_dir, $order, 'id', 'name', 'username', 'email', 'm_account', 'j_account', 'auth', 'admin');
        if ($limit)
            return array_slice ($all, $limitstart, $limit);
        else
            return $all;
    }

}
