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
$view['slots']->set('headerTitle', $view['translator']->trans('mautic.accountinfo.header.title'));

?>
<!-- start: box layout -->
<div class="box-layout">
    <!-- step container -->
    <div class="col-md-3 bg-white height-auto">
        <div class="pr-lg pl-lg pt-md pb-md">
            <!-- Nav tabs -->
            <ul class="list-group list-group-tabs" role="tablist">
                <li role="presentation" class="list-group-item">
                    <a href="javascript: void(0);" onclick="Mautic.closeModalAndRedirect('.<?php echo $typePrefix; ?>-type-modal', '<?php echo $view['router']->path($actionRoute, ['objectAction' => 'edit']) ?>');" aria-controls="accountinfo" role="tab" data-toggle="tab">
                        <?php echo $view['translator']->trans('mautic.accountinfo.tab.accountinfo'); ?>
                    </a>
                </li>
                <li role="presentation" class="list-group-item">
                    <a href="javascript: void(0);" onclick="Mautic.closeModalAndRedirect('.<?php echo $typePrefix; ?>-type-modal', '<?php echo $view['router']->path($actionRoute, ['objectAction' => 'billing']) ?>');" aria-controls="billinginfo" role="tab" data-toggle="tab">
                        <?php echo $view['translator']->trans('mautic.accountinfo.tab.billinginfo'); ?>
                    </a>
                </li>
                <li role="presentation" class="list-group-item in active">
                    <a href="javascript: void(0);" onclick="Mautic.closeModalAndRedirect('.<?php echo $typePrefix; ?>-type-modal', '<?php echo $view['router']->path($actionRoute, ['objectAction' => 'payment']) ?>');" aria-controls="paymenthistory" role="tab" data-toggle="tab">
                        <?php echo $view['translator']->trans('mautic.accountinfo.tab.paymenthistory'); ?>
                    </a>
                </li>
                <li role="presentation" class="list-group-item hide">
                    <a href="javascript: void(0);" onclick="Mautic.closeModalAndRedirect('.<?php echo $typePrefix; ?>-type-modal', '<?php echo $view['router']->path($actionRoute, ['objectAction' => 'cancel']) ?>');" aria-controls="cancelsubscription" role="tab" data-toggle="tab">
                        <?php echo $view['translator']->trans('mautic.accountinfo.tab.cancelsubs'); ?>
                    </a>
                </li>
            </ul>

        </div>
    </div>

    <!-- container -->
    <div class="col-md-9 bg-auto height-auto bdr-l accountinfo">

        <!-- Tab panes -->
        <div class="tab-content">

            <div role="tabpanel" class="tab-pane fade in active bdr-w-0" id="paymenthistory">
                <div class="pt-md pr-md pl-md pb-md">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $view['translator']->trans('mautic.payment.history.title'); ?></h3>
                        </div>
                        <div class="panel-body">
                            <?php echo $view->render('MauticCoreBundle:AccountInfo:payment_history.html.php', [
                                'payments'         => $payments,
                            ]); ?>
                    </div>
                </div>
                </div>
            </div>

        </div>
    </div>
</div>
