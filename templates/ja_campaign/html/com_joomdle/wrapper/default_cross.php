<?php
/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */


// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<script src="<?php echo JURI::root(); ?>/components/com_joomdle/js/iframeResizer.min.js" type="text/javascript"></script>

<style>iframe{width:100%; border:0;}</style>
<iframe id="myIframe" src="<?php echo $this->wrapper->url; ?>" scrolling="no"></iframe>
<?php $crossdomain_autoheight_calculation_method = $this->params->get( 'crossdomain_autoheight_calculation_method', 'bodyOffset' ); ?>
<script>iFrameResize({log:true, heightCalculationMethod:'<?php echo $crossdomain_autoheight_calculation_method;?>'}, '#myIframe')</script>
