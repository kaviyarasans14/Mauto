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
$view['slots']->set('mauticContent', 'pricingplans');
$view['slots']->set('headerTitle', $view['translator']->trans('mautic.core.prepaidplans'));
?>

<div style="display: block;text-align: center" class="alert alert-danger hide" id="pricing-plan-alert-info" role="alert">First configure your own amazon ses settings then continue to upgrade.<a href="<?php echo $view['router']->path('mautic_config_action', ['objectAction' => 'edit']); ?>">
        Click Here
    </a></div>
<div class="pricing-plan-holder" data-email-transaport="<?php echo $transport ?>">

    <div class="col-md-4 pricing-plan-list">
        <div class="panel panel-default">
            <div class="panel-body">
                <h4 class="plan-header">Emails Via Amazon SES</h4>
                <div class="sub-plan-header">10,000 contacts</div>
                <div class="price">
                    <span>$</span>
                    <span>9</span>
                </div>
                <div class="price-desc">Per Month,Billed Monthly</div>
                <div class="details-list">
                    <span>All Features Included</span>
                    <span>Priority Support Via Email</span>
                    <span>$9 extra for every 10,000 additional contacts</span>
                    <span>AWS SES charges are extra and paid to Amazon</span>
                </div>
                <a href="#" type="button" data-planname="viaaws" data-plancurrency="$" data-planamount="9" data-plancredits="10000" class="btn btn-success plan-btn">
                    Upgrade
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4 pricing-plan-list">
        <div class="panel panel-default">
            <div class="panel-body">
                <h4 class="plan-header">Emails Via LeadsEngage</h4>
                <div class="sub-plan-header">10,000 contacts</div>
                <div class="price">
                    <span>$</span>
                    <span>29</span>
                </div>
                <div class="price-desc">Per Month,Billed Monthly</div>
                <div class="details-list">
                    <span>All Features Included</span>
                    <span>Priority Support Via Email</span>
                    <span>$29 extra for every 10,000 additional contacts</span>
                    <span>Charges include email deliveries</span>
                </div>
                <a href="#" type="button"  data-planname="viale" data-plancurrency="$" data-planamount="29" data-plancredits="10000" class="btn btn-success plan-btn">
                    Upgrade
                </a>
            </div>
        </div>
    </div>
</div>
<div class="pricing-type-modal-backdrop hide" style="width: 100%; height: 100%; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: #000000; opacity: 0.9; z-index: 9000"></div>

<div class="modal fade in pricing-type-modal hide" style="display: block; z-index: 9999;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="javascript: void(0);" onclick="Mautic.closeModalAndRedirect('.pricing-type-modal', '<?php echo $view['router']->path('le_pricing_index'); ?>');" class="close" ><span aria-hidden="true">&times;</span></a>
                <h4 class="modal-title">
                    <?php echo $view['translator']->trans('le.pricing.model.header'); ?>
                </h4>
                <div class="modal-loading-bar"></div>
            </div>
            <div class="modal-body form-select-modal">
                <div class="alert alert-info hide" id="card-holder-info" role="alert"></div>
                <div class="card-holder-title">
                    Credit Card
                </div>
                <div id="card-holder-widget" data-le-token="<?php echo $letoken?>">
                    <!-- A Stripe Element will be inserted here. -->
                </div>
                <!-- Used to display form errors. -->
                <div id="card-holder-errors" role="alert"></div>
                <button type="button" class="btn btn-default pay-now-btn">
                </button>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
