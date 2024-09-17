<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die('Restricted access');

//JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('formbehavior.chosen', 'select');

/*
use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\String\PunycodeHelper;
*/
use Joomla\CMS\HTML\HTMLHelper;

$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
?>
<form action="index.php?option=com_joomdle&amp;view=users"  id="adminForm" method="POST" name="adminForm">
  <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>
        <?php
        // Search tools bar
        //echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filtersHidden' => 0)));
        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>

        <div class="clearfix"> </div>
        <table class="table">
             <thead>
                    <tr>
                           <th width="10">ID</th>
                           <th width="10"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" /></th>
                           <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_JOOMDLE_USERNAME', 'username', $listDirn, $listOrder); ?></th>
                           <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_JOOMDLE_NAME', 'name', $listDirn, $listOrder); ?></th>

                           <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_JOOMDLE_EMAIL', 'email', $listDirn, $listOrder); ?></th>
                           <th class="center"><?php echo JText::_('COM_JOOMDLE_JOOMLA_ACCOUNT'); ?></th>
                           <th class="center"><?php echo JText::_('COM_JOOMDLE_MOODLE_ACCOUNT'); ?></th>
                           <th class="center"><?php echo JText::_('COM_JOOMDLE_JOOMDLE_USER'); ?></th>
                    </tr>              
            </thead>
            <tfoot>
                        <tr>
                                <td colspan="10">
                                        <?php echo $this->pagination->getListFooter(); ?>
                                </td>
                        </tr>
            </tfoot>
            <tbody>
                        <?php
                    $k = 0;
                    $i = 0;
                    foreach ($this->users as $row){
                        if ($row['admin'])
                           $checked = $this->get_disabled_checkbox ($i, $row['id']);
                        else
                           $checked = JHTML::_('grid.id', $i, $row['id']);
                   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row['id'];?></td>
                                  <td><?php /* if (!$row['admin']) */ echo $checked; ?></td>
                                  <td><?php echo $row['username'];?></td>
                                  <td><?php echo $row['name']; ?></td>
                                  <td><?php echo $row['email']; ?></td>
                                  <?php $text = "Ticked"; $image = "tick.png"; ?>
                                  <td class="center"><?php echo $row['j_account'] ? JHTML::_('image', 'joomdle/' . $image , NULL, NULL, $text ): ''; ?></td>
                                  <td class="center"><?php echo $row['m_account'] ? JHTML::_('image', 'joomdle/' . $image , NULL, NULL, $text ): ''; ?></td>
                                  <td class="center"><?php echo ($row['auth'] == 'joomdle') ? JHTML::_('image', 'joomdle/' . $image , NULL, NULL, $text ): ''; ?></td>
                           </tr>
                    <?php
                    $k = 1 - $k;
                    $i++;
                    }
                    ?>
             </tbody>
       </table>
      
       <input type="hidden" name="task" value=""/>
       <input type="hidden" name="boxchecked" value="0"/>   
       <?php echo JHTML::_( 'form.token' ); ?>
    </div>
</form>
