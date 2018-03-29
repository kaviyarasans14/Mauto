<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$header = $view['translator']->trans('leadsengage.kyc.profile_completion');
?>
<div class="type-modal-backdrop" style="width: 100%; height: 100%; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: #000000; opacity: 0.9; z-index: 1500"></div>

<div class="modal fade in " style="display: block;z-index: 1500;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <a href="javascript: void(0);" onclick="Mautic.closeModalAndRedirect('.<?php echo $typePrefix; ?>-type-modal', '<?php echo $view['router']->path('mautic_dashboard_index'); ?>');" class="close" ><span aria-hidden="true">&times;</span></a>
                <h4 class="modal-title">
                    <?php echo $view['translator']->trans($header); ?>
                </h4>
            </div>
            <div class="modal-body form-select-modal">
                <div class="first_time_setup_basic">
                    <form class="form-group login-form" name="terms_condition" data-toggle="ajax" role="form" action="<?php echo $view['router']->path('mautic_kyc_action', ['objectAction' => 'signup']) ?>" method="post">
                        <div role="tabpanel" class="tab-pane fade in active bdr-w-0">
                            <div class="pt-md pr-md pl-md pb-md">
                                <div class="panel-body" style="width:100%;">
                                    <div class="row">
                                        <div class="row">
                                            <div style="display:none;">
                                            <div class="row" style="display:none;">
                                                <div class="col-md-6">
                                                    <?php echo $view['form']->label($userform['firstName']); ?>
                                                    <?php echo $view['form']->widget($userform['firstName']); ?>
                                                </div>
                                                <div class="col-md-6">

                                                    <?php echo $view['form']->label($userform['lastName']); ?>
                                                    <?php echo $view['form']->widget($userform['lastName']); ?>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row" style="display:none;">
                                                <div class="col-md-6">
                                                    <?php echo $view['form']->label($form['phonenumber']); ?>
                                                    <?php echo $view['form']->widget($form['phonenumber']); ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <?php echo $view['form']->label($userform['email']); ?>
                                                    <?php echo $view['form']->widget(
                                                        $userform['email'],
                                                        ['attr' => ['style' => 'pointer-events: none;background-color: #ebedf0;opacity: 1;']]
                                                    ); ?>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php echo $view['form']->label($billform['companyname']); ?>
                                                <?php echo $view['form']->widget($billform['companyname']); ?>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php echo $view['form']->label($billform['companyaddress']); ?>
                                                <?php echo $view['form']->widget($billform['companyaddress']); ?>
                                                <label>Will be Used in Email Footer for CAN-SPAN Compliance</label>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php echo $view['form']->label($billform['postalcode']); ?>
                                                <?php echo $view['form']->widget($billform['postalcode']); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?php echo $view['form']->label($billform['state']); ?>
                                                <?php echo $view['form']->widget($billform['state']); ?>
                                            </div>

                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php echo $view['form']->label($billform['city']); ?>
                                                <?php echo $view['form']->widget($billform['city']); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?php echo $view['form']->label($billform['country']); ?>
                                                <?php echo $view['form']->widget($billform['country']); ?>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php echo $view['form']->row($form['timezone']); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?php echo $view['form']->label($billform['gstnumber']); ?>
                                                <?php echo $view['form']->widget($billform['gstnumber']); ?>
                                            </div>
                                        </div>
                                        <button class="signup_next" type="submit" value="Next"><?php echo $view['translator']->trans('leadsengage.kyc.submit'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

