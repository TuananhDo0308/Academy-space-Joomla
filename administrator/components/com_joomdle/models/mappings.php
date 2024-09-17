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
class JoomdleModelMappings extends JModelList
{
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
                'joomla_app', 'joomla_app',
                'moodle_field', 'moodle_field',
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
    protected function populateState($ordering = 'joomla_app', $direction = 'asc')
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


        $state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state');
        $this->setState('filter.state', $state);

        // Load the parameters.
        $params     = JComponentHelper::getParams('com_joomdle');
        $this->setState('params', $params);

        // List state information.
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
        $id .= ':'.$this->getState('filter.joomla_app');

        return parent::getStoreId($id);
    }

    /**
     * Gets the list of users
     *
     * @return  mixed   An array of data items on success, false on failure.
     * @since   1.6
     */
    public function getItems()
    {
        $db           = JFactory::getDBO();

        $search = $this->getState ('filter.search');
        if ($search)
            $searchEscaped = $db->Quote( '%'.$db->escape( $search, true ).'%', false );
        else $searchEscaped = "";

        $pagination = $this->getPagination ();
        $limitstart = $pagination->limitstart;
        $limit = $pagination->limit;


        $listOrder  = $this->state->get('list.ordering');
        $listDirn   = $this->state->get('list.direction');
        $filter_order = $listOrder;
        $filter_order_Dir = $listDirn;

        $filter_type = $this->getState ('filter.state');
        if ($filter_type == 'no') // Kludge to use datasource field type
            $filter_type = '';

        $mappings = JoomdleHelperMappings::getMappings ($filter_type, $limitstart, $limit,$filter_order, $filter_order_Dir, $searchEscaped);

        return $mappings;
    }

    protected function getListQuery()
    {
        // Used for getTotal

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

        $query->from('#__joomdle_field_mappings AS a');

        $search = $this->getState('filter.search');
        if ($search) {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where('(a.joomla_field LIKE '. $search . ' OR a.moodle_field LIKE '. $search . ')');
        }

        $filter_type = $this->getState ('filter.state');
        if ($filter_type) {
                $query->where('a.joomla_app = ' . $db->quote ($filder_type));
        }

        return $query;
    }

}
