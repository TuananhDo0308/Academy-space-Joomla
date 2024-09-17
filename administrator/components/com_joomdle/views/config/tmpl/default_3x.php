<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */


defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');

?>
<style>
.myDiv{
    background:white;
}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_joomdle&view=config');?>" id="adminForm" method="post" name="adminForm" class="form-validate">
  <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>

    <div class="clearfix"> </div>

    <div>
        <div class="col-md-10 myDiv" id="config">
        <div id="j-main-container" class="j-main-container">
            <ul class="nav nav-tabs flex-wrap">
                <li class="nav-link active"><a href="#page-general" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_GENERAL_CONFIG');?></a></li>
                <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#page-links"><?php echo JText::_('COM_JOOMDLE_LINKS_BEHAVIOUR');?></a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#page-views" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_CONFIG_VIEWS');?></a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#page-shop" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_SHOP');?></a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#page-userprofiles" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_USER_PROFILES');?></a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#page-integrations" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_INTEGRATIONS');?></a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#page-applications" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_APPLICATIONS');?></a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="#page-license" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_LICENSE');?></a>
                </li>
            </ul>
            <div id="config-document" class="tab-content">
                <div id="page-general" class="tab-pane active">
                    <div class="row-fluid">
                        <div class="span6">
                            <?php echo $this->loadTemplate('general'); ?>
                        </div>
                        <div class="span6">
                        </div>
                    </div>
                </div>
                <div id="page-links" class="tab-pane">
                    <div class="row-fluid">
                        <div class="span12">
                            <?php echo $this->loadTemplate('links'); ?>
                        </div>
                    </div>
                </div>
                <div id="page-views" class="tab-pane">
                    <div class="row-fluid">
                        <div class="span6">
                            <?php echo $this->loadTemplate('actionbuttons'); ?>
                            <?php echo $this->loadTemplate('topics'); ?>
                            <?php echo $this->loadTemplate('coursecategory'); ?>
                            <?php echo $this->loadTemplate('course'); ?>
                            <?php echo $this->loadTemplate('backlinks'); ?>
                        </div>
                        <div class="span6">
                            <?php echo $this->loadTemplate('detailview'); ?>
                            <?php echo $this->loadTemplate('coursesabc'); ?>
                        </div>
                    </div>
                </div>
                <div id="page-shop" class="tab-pane">
                    <div class="row-fluid">
                        <div class="span12">
                            <?php echo $this->loadTemplate('shop'); ?>
                        </div>
                    </div>
                </div>
                <div id="page-userprofiles" class="tab-pane">
                    <div class="row-fluid">
                        <div class="span6">
                            <?php echo $this->loadTemplate('datasource'); ?>
                            <?php echo $this->loadTemplate('profiletypes'); ?>
                            <?php echo $this->loadTemplate('usergroups'); ?>
                        </div>
                        <div class="span6">
                            <?php echo $this->loadTemplate('activities'); ?>
                            <?php echo $this->loadTemplate('points'); ?>
                            <?php echo $this->loadTemplate('socialgroups'); ?>
                        </div>
                    </div>
                </div>
                <div id="page-integrations" class="tab-pane">
                    <div class="row-fluid">
                        <div class="span6">
                            <?php echo $this->loadTemplate('mailinglist'); ?>
                            <?php echo $this->loadTemplate('pdf'); ?>
                            <?php echo $this->loadTemplate('kunena'); ?>
                        </div>
                        <div class="span6">
                        </div>
                    </div>
                </div>
                <div id="page-applications" class="tab-pane">
                    <div class="row-fluid">
                        <div class="span6">
                            <?php echo $this->loadTemplate('applications'); ?>
                        </div>
                        <div class="span6">
                        </div>
                    </div>
                </div>
                <div id="page-license" class="tab-pane">
                    <div class="row-fluid">
                        <div class="span6">
                            <?php echo $this->loadTemplate('license'); ?>
                        </div>
                        <div class="span6">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="task" value="" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
            </div>
        <!-- End Content -->
    </div>
</form>
