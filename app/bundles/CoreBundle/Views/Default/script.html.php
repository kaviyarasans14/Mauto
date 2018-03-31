<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$mauticContent = $view['slots']->get(
    'mauticContent',
    isset($mauticTemplateVars['mauticContent']) ? $mauticTemplateVars['mauticContent'] : ''
);
?>

<script>
    var mauticBasePath    = '<?php echo $app->getRequest()->getBasePath(); ?>';
    var mauticBaseUrl     = '<?php echo $view['router']->path('mautic_base_index'); ?>';
    var mauticAjaxUrl     = '<?php echo $view['router']->path('mautic_core_ajax'); ?>';
    var mauticAjaxCsrf    = '<?php echo $view['security']->getCsrfToken('mautic_ajax_post'); ?>';
    var mauticAssetPrefix = '<?php echo $view['assets']->getAssetPrefix(true); ?>';
    var mauticContent     = '<?php echo $mauticContent; ?>';
    var mauticEnv         = '<?php echo $app->getEnvironment(); ?>';
    var leClientID        = '<?php echo $view['assets']->getAppid(); ?>';
    var mauticLang        = <?php echo $view['translator']->getJsLang(); ?>;
    document.addEventListener("contextmenu", function(e){
        alert("Right Click Not Supported");
        e.preventDefault();
    }, false);
</script>

<?php $view['assets']->outputSystemScripts(true); ?>
<?php $view['assets']->outputBeeEditorScripts(); ?>
<?php
if ($mauticContent == 'subscription' || $mauticContent == 'prepaidplans') {
    echo '<script src="https://checkout.razorpay.com/v1/checkout.js"></script>';
}
?>
