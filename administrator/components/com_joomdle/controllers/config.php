<?php
/**
 * @version     1.0.0
 * @package     com_joomdle
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Item controller class.
 */
class JoomdleControllerConfig extends JControllerForm
{
    protected $text_prefix = 'COM_JOOMDLE_CONFIG';

    function __construct() {
        $this->view_list = 'config';
        parent::__construct();
    }

    function regenerate_joomla_token ()
    {
        $this->getModel()->regenerate_joomla_token ();
        $this->setMessage (JText::_ ('COM_JOOMDLE_NEW_TOKEN_GENERATED'));
        $this->setRedirect ('index.php?option=com_joomdle&view=config');
        return true;
    }

}
