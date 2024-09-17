<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die;


jimport('joomla.application.component.modellist');

require_once( JPATH_COMPONENT.'/helpers/shop.php' );

/**
 * Methods supporting a list of user records.
 *
 */
class JoomdleModelShop extends JModelList
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
                'name', 'name',
                'published', 'published',
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

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $producttype = $this->getUserStateFromRequest($this->context . '.filter.producttype', 'filter_producttype', '');
        $this->setState('filter.producttype', $producttype);

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
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState('filter.producttype');

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
        $pagination = $this->getPagination ();
        $limitstart = $pagination->limitstart;

        $limit = $pagination->limit;

        $listOrder  = $this->state->get('list.ordering');
        $listDirn   = $this->state->get('list.direction');
        $filter_order = $listOrder; // Not really used, always sort by course name
        $filter_order_Dir = $listDirn;

        $filter_type = $this->getState ('filter.state');

        $bundles = JoomdleHelperShop::get_bundles ();
        $courses = JoomdleHelperShop::getShopCourses ();

        $products = array_merge ($bundles, $courses);
        usort($products, array($this, "cmp"));

        if ($filter_order_Dir == 'DESC')
            $products = array_reverse ($products);

        $published = $this->getState('filter.published');
        $search = $this->getState ('filter.search');
        $producttype = $this->getState('filter.producttype');
        $p = array ();
        foreach ($products as $product)
        {
            if ($search != '')
            {
                if (!stristr ($product->fullname, $search))
                    continue;
            }
            if ($published !== '')
            {
                if ($product->published != $published)
                    continue;
            }
            if ($producttype !== '')
            {
                if ($producttype == 'bundles')
                {
                    if (!$product->is_bundle)
                        continue;
                }
                else if ($producttype == 'courses')
                {
                    if ($product->is_bundle)
                        continue;
                }
            }
            $p[] = $product;
        }
        $products = $p;

        $pagination = $this->getPagination ();
        $limitstart = $pagination->limitstart;
        $limit = $pagination->limit;

        return array_slice ($products, $limitstart, $limit, true);
    }

    function cmp($a, $b)
    {
        return strcasecmp($a->fullname, $b->fullname);
    }

    protected function getListQuery()
    {
        //XXX Note: this does nothing useful for us now, as we cannot pull data via a simple DB query,  but it seems needed

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
        $bundles = JoomdleHelperShop::get_bundles ();
        $courses = JoomdleHelperShop::getShopCourses ();

        $products = array_merge ($bundles, $courses);

        $published = $this->getState('filter.published');
        $search = $this->getState ('filter.search');
        $producttype = $this->getState('filter.producttype');
        $p = array ();
        foreach ($products as $product)
        {
            if ($search != '')
            {
                if (!stristr ($product->fullname, $search))
                    continue;
            }
            if ($published !== '')
            {
                if ($product->published != $published)
                    continue;
            }
            if ($producttype !== '')
            {
                if ($producttype == 'bundles')
                {
                    if (!$product->is_bundle)
                        continue;
                }
                else if ($producttype == 'courses')
                {
                    if ($product->is_bundle)
                        continue;
                }
            }
            $p[] = $product;
        }
        $products = $p;

        $total = count ($products);
        return $total;
    }
}
