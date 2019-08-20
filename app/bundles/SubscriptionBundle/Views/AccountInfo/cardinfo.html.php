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
        'step'                => 'cardinfo',
        'typePrefix'          => $typePrefix,
        'actionRoute'         => $actionRoute,
        'planType'            => $planType,
    ]); ?>
    <!-- container -->
    <div class="col-md-9 bg-auto height-auto bdr-l accountinfo">

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active bdr-w-0" id="cardinfo">
                <div class="pt-md pr-md pl-md pb-md">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $view['translator']->trans('leadsengage.cardinfo.history.title'); ?></h3>
                        </div>
                            <div class="cardholder-panel">
                                <div class="alert alert-info hide" id="card-holder-info" role="alert"></div>
                                <div>
                                    <div class="card-holder-title">
                                        Credit Card
                                    </div>
                                    <div class="card-holder-sub-title <?php echo empty($stripecard->getlast4digit()) ? 'hide' : ''?>">
                                        <?php echo 'Card ending in '.$stripecard->getlast4digit().' on file.' ?>
                                    </div>

                                <div id="card-holder-widget" data-le-token="<?php echo $letoken?>">
                                    <!-- A Stripe Element will be inserted here. -->
                                </div>
                                <!-- Used to display form errors. -->
                                <div id="card-holder-errors" role="alert"></div>
                                <button type="button" class="btn btn-default card-update-btn">
                                    Update Card
                                </button>
                            </div>

                </div>
                </div>
            </div>

        </div>
    </div>
</div>
