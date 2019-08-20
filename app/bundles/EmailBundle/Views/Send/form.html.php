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

$view['slots']->set('mauticContent', 'emailSend');
$view['slots']->set('headerTitle', $view['translator']->trans('mautic.email.send.list', ['%name%' => $email->getName()]));
$isAdmin=$view['security']->isAdmin();
$style  = [];
if (!$isAdmin) {
    $style =  ['attr' => ['tabindex' => '-1', 'style' => 'pointer-events: none;background-color: #ebedf0;opacity: 1;display:none;']];
}
$btnclass = 'btn btn-primary send-btn'.((!$pending) ? ' disabled' : '');
?>
<div class="row">
    <div class="col-sm-offset-3 col-sm-6">
        <div class="ml-lg mr-lg mt-md pa-lg">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">
                        <p><?php echo $view['translator']->transChoice('le.email.send.instructions', $pending, ['%pending%' => $pending]); ?></p>
                    </div>
                </div>
                <div class="panel-body">
                    <?php echo $view['form']->start($form); ?>
                    <div class="col-xs-8 col-xs-offset-2">
                        <div class="well mt-lg">
                            <div class="input-group">
                                <?php echo $view['form']->widget($form['batchlimit'], $style); ?>
                                <span class="input-group-btn" style="text-align:center;">
                                    <!--<?php echo $view->render('MauticCoreBundle:Helper:confirm.html.php', [
                                        'message'         => $view['translator']->trans('mautic.email.form.confirmsend', ['%name%' => $email->getName()]),
                                        'confirmText'     => $view['translator']->trans('mautic.email.send'),
                                        'confirmCallback' => 'submitSendForm',
                                        'iconClass'       => 'fa fa-send-o',
                                        'btnText'         => $view['translator']->trans('mautic.email.send'),
                                        'btnClass'        => 'btn btn-primary send-btn'.((!$pending) ? ' disabled' : ''),
                                    ]);
                                    ?>-->
                                    <a class="<?php echo $btnclass; ?>" style="margin-left:2px;border-radius: 3px;" href="javascript: void(0);" onclick="Mautic.submitSendForm();" ><span><i class="fa fa-send-o"></i><span style="padding:4px;"><?php echo $view['translator']->trans('mautic.email.send'); ?></span></span></a>
                                    <a class="btn btn-primary" style="margin-left:2px;border-radius: 3px;" href="<?php echo $view['router']->path($actionRoute, ['objectAction' => 'view', 'objectId' => $email->getId()]); ?>" data-toggle="ajax"><?php echo $view['translator']->trans('mautic.core.form.cancel'); ?></a>
                                </span>

                            </div>
                            <?php echo $view['form']->errors($form['batchlimit']); ?>
                            <div class="text-center">
                                <span class="label label-primary mt-lg hide"><?php echo $view['translator']->transChoice('mautic.email.send.pending', $pending, ['%pending%' => $pending]); ?></span>

                            </div>
                        </div>
                    </div>
                    <?php echo $view['form']->end($form); ?>
                </div>
            </div>
        </div>
    </div>
</div>