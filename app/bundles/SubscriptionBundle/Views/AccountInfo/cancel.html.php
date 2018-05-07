<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'accountinfo');
$view['slots']->set('headerTitle', $view['translator']->trans('leadsengage.accountinfo.header.title'));

?>
<!-- start: box layout -->
<div class="box-layout">
    <!-- step container -->
    <?php echo $view->render('MauticSubscriptionBundle:AccountInfo:steps.html.php', [
        'step'                => 'cancelsubscription',
        'typePrefix'          => $typePrefix,
        'actionRoute'         => $actionRoute,
    ]); ?>
    <!-- container -->
    <div class="col-md-9 bg-auto height-auto bdr-l accountinfo">

        <!-- Tab panes -->
        <div class="tab-content">

            <div role="tabpanel" class="tab-pane fade in active bdr-w-0" >
                <div class="pt-md pr-md pl-md pb-md" id="paymenthistory">
                    <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $view['translator']->trans('leadsengage.cancel.subscription.title'); ?></h3>
                    </div>
                    <div class="panel-body">
                        <br>
                        <p style="text-align: left;font-family: 'Open Sans', Helvetica, Arial, sans-serif;font-size:13px;"><?php echo $view['translator']->trans('leadsengage.cancel.description'); ?></p>
                        <br>
                        <a href="javascript: void(0);" onclick="Mautic.closeModalAndRedirect('.<?php echo $typePrefix; ?>-type-modal', '<?php echo $view['router']->path($actionRoute, ['objectAction' => 'cancel']) ?>');" class="cancel-subscription" ><?php echo $view['translator']->trans('leadsengage.cancel.subscription.title'); ?></a>
                        <br>
                        <br>
                    </div>
                </div>
                </div>
            </div>

        </div>
    </div>
</div>
