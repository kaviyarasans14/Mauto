<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<!DOCTYPE html>
<html>
<?php echo $view->render('MauticCoreBundle:Default:head.html.php'); ?>
<body class="header-fixed">
<!-- start: app-wrapper -->
<section id="app-wrapper">
    <?php $view['assets']->outputScripts('bodyOpen'); ?>

    <!-- start: app-sidebar(left) -->
    <aside class="app-sidebar sidebar-left">
        <?php echo $view->render('MauticCoreBundle:LeftPanel:index.html.php'); ?>
    </aside>
    <!--/ end: app-sidebar(left) -->

    <!-- start: app-sidebar(right) -->
    <aside class="app-sidebar sidebar-right">
        <?php echo $view->render('MauticCoreBundle:RightPanel:index.html.php'); ?>
    </aside>
    <!--/ end: app-sidebar(right) -->

    <!-- start: app-header -->
   <header id="app-header" class="navbar">

       <?php if (!empty($licenseRemCount)) : ?>
           <?php if ($licenseRemCount <= 7 && $licenseRemCount > 1) : ?>
               <?php $message = $view['translator']->trans('leadsengage.license.expired', ['%licenseRemCount%' => $licenseRemCount]); ?>
           <?php elseif ($licenseRemCount <= 1) : ?>
               <?php $message = $view['translator']->trans('leadsengage.license.expired.tommorow'); ?>
           <?php endif; ?>
       <?php endif; ?>

       <?php $emailUssage    = false; ?>
       <?php $bouceUsage     = false; ?>
       <?php $emailsValidity = false; ?>

       <?php if ($emailUsageCount > 85): ?>
           <?php $emailUssage=true; ?>
       <?php endif; ?>
       <?php if ($bounceUsageCount > 5): ?>
           <?php $bouceUsage=true; ?>
       <?php endif; ?>
       <?php if (!$emailValidity): ?>
           <?php $emailsValidity=true; ?>
       <?php endif; ?>

       <?php if ($emailUssage && $bouceUsage && $emailsValidity): ?>
           <?php $usageMsg = $view['translator']->trans('leadsengage.email.bounce.validity.expired'); ?>
       <?php elseif ($emailUssage && $bouceUsage): ?>
           <?php $usageMsg = $view['translator']->trans('leadsengage.bounce.email.usage.exceeds'); ?>
       <?php elseif ($bouceUsage && $emailsValidity): ?>
           <?php $usageMsg = $view['translator']->trans('leadsengage.bounce.validity.expired'); ?>
       <?php elseif ($emailUssage && $emailsValidity): ?>
           <?php $usageMsg = $view['translator']->trans('leadsengage.email.validity.exceeds'); ?>
       <?php elseif ($emailUssage): ?>
           <?php $usageMsg = $view['translator']->trans('leadsengage.email.usage.exceeds'); ?>
       <?php elseif ($bouceUsage): ?>
           <?php $usageMsg = $view['translator']->trans('leadsengage.bounce.usage.exceeds'); ?>
       <?php elseif ($emailsValidity): ?>
           <?php $usageMsg = $view['translator']->trans('leadsengage.email.validity.expired'); ?>
       <?php endif; ?>

       <?php  if (!empty($message)) : ?>
           <?php if (!empty($usageMsg)) : ?>
               <?php  $message = "$message $usageMsg" ?>
           <?php else : ?>
               <?php  $message = $message  ?>
           <?php endif; ?>
           <span class="license-notifiation" id="licenseclosebutton"><?php echo $message ?> <img class="button-notification" src="<?php echo $view['assets']->getUrl('media/images/button.png') ?>" onclick="licenseCloseButton()" width="10" height="10"> </span>
       <?php else: ?>
           <?php if (!empty($usageMsg)) : ?>
               <span class="license-notifiation" id="licenseclosebutton"><?php echo $usageMsg ?> <img class="button-notification" src="<?php echo $view['assets']->getUrl('media/images/button.png') ?>" onclick="licenseCloseButton()" width="10" height="10"> </span>
           <?php endif; ?>
       <?php endif; ?>

        <?php echo $view->render('MauticCoreBundle:Default:navbar.html.php'); ?>
        <?php echo $view->render('MauticCoreBundle:Notification:flashes.html.php'); ?>
    </header>
    <!--/ end: app-header -->

    <!-- start: app-footer(need to put on top of #app-content)-->
    <footer id="app-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-6 text-muted"><?php echo $view['translator']->trans('mautic.core.copyright', ['%date%' => date('Y')]); ?></div>
                <!--<div class="col-xs-6 text-muted text-right small">v<?php
                /** @var \Mautic\CoreBundle\Templating\Helper\VersionHelper $version */
                $version = $view['version'];
                echo $version->getVersion(); ?>
                        </div>-->
            </div>
        </div>
    </footer>
    <!--/ end: app-content -->

    <!-- start: app-content -->
    <section id="app-content">
        <?php $view['slots']->output('_content'); ?>
    </section>
    <!--/ end: app-content -->

</section>
<!--/ end: app-wrapper -->

<script>

    Mautic.onPageLoad('body');
    <?php if ($app->getEnvironment() === 'dev'): ?>
    mQuery( document ).ajaxComplete(function(event, XMLHttpRequest, ajaxOption){
        if(XMLHttpRequest.responseJSON && typeof XMLHttpRequest.responseJSON.ignore_wdt == 'undefined' && XMLHttpRequest.getResponseHeader('x-debug-token')) {
            if (mQuery('[class*="sf-tool"]').length) {
                mQuery('[class*="sf-tool"]').remove();
            }

            mQuery.get(mauticBaseUrl + '_wdt/'+XMLHttpRequest.getResponseHeader('x-debug-token'),function(data){
                mQuery('body').append('<div class="sf-toolbar-reload">'+data+'</div>');
            });
        }
    });
    <?php endif; ?>
    function licenseCloseButton() {
        var x = document.getElementById("licenseclosebutton");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }
</script>
<?php $view['assets']->outputScripts('bodyClose'); ?>
<?php echo $view->render('MauticCoreBundle:Helper:modal.html.php', [
    'id'            => 'MauticSharedModal',
    'footerButtons' => true,
]); ?>
</body>
</html>
