<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
?>
<form action="index.php?option=com_joomdle&view=mappings" method="POST"  id="adminForm" name="adminForm">

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
        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>

    <div class="clearfix"> </div>
       <table class="table table-striped">
             <thead>
                    <tr>
                        <th width="10">ID</th>
                        <th width="10"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" /></th>
                        <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_JOOMDLE_JOOMLA_COMPONENT', 'joomla_app', $listDirn, $listOrder); ?></th>
                        <th><?php echo JText::_('COM_JOOMDLE_JOOMLA_FIELD'); ?></th>
                        <th><?php echo HTMLHelper::_('searchtools.sort', 'COM_JOOMDLE_MOODLE_FIELD', 'moodle_field', $listDirn, $listOrder); ?></th>
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
                    foreach ($this->items as $row){
                           $checked = JHTML::_('grid.id', $i, $row['id']);
                   ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row['id'];?></td>
                                  <td><?php  echo $checked; ?></td>
                                  <td><?php echo $row['joomla_app'];?></td>
                                  <td><a href='index.php?option=com_joomdle&task=mapping.edit&id=<?php echo $row['id'];?>'><?php echo JText::_ ($row['joomla_field_name']); ?></a></td>
                                  <td><a href='index.php?option=com_joomdle&task=mapping.edit&id=<?php echo $row['id'];?>'><?php echo $row['moodle_field_name']; ?></a></td>
                           </tr>
                    <?php
                    $k = 1 - $k;
                    $i++;
                    }
                    ?>
             </tbody>
       </table>
      
       <input type="hidden" name="option" value="com_joomdle"/>
       <input type="hidden" name="task" value=""/>
       <input type="hidden" name="boxchecked" value="0"/>   
       <input type="hidden" name="hidemainmenu" value="0"/> 
       <?php echo JHTML::_( 'form.token' ); ?>
    </div>
</form>
