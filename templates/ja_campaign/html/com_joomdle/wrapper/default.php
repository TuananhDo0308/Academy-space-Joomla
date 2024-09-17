<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */


// no direct access
defined('_JEXEC') or die('Restricted access');

$params = JComponentHelper::getParams( 'com_joomdle' );
$min_height = $params->get( 'min_height' );

?>
<script src="<?php echo JURI::root(); ?>/components/com_joomdle/js/autoheight.js" type="text/javascript"></script>


<div class="contentpane"> 
    <iframe 
        id="blockrandom" 
        class="autoHeight"
        src="<?php echo $this->wrapper->url; ?>"
        width="<?php echo $this->params->get('width', '100%'); ?>"
        scrolling="<?php echo $this->params->get('scrolling', 'auto'); ?>"

        <?php if (!$this->params->get('autoheight', 1)) { ?>
            height="<?php echo $this->params->get('height', '500'); ?>"
            onload="scroll(0,0);"
        <?php
        }
        ?>

        align="top" 
        frameborder="0"
        <?php if ($this->params->get('autoheight', 1)) { ?>
            onload='itspower(this, false, true, 20, <?php echo $min_height; ?>)'
        <?php
        }
        ?>

 
        webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
</div>

