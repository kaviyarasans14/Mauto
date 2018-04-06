<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$request     = $app->getRequest();
$contentOnly = $request->get('contentOnly', false) || $view['slots']->get('contentOnly', false) || !empty($contentOnly);
$modalView   = $request->get('modal', false) || $view['slots']->get('inModal', false) || !empty($modalView);

if (!$request->isXmlHttpRequest() && !$modalView):
    //load base template
    $template = ($contentOnly) ? 'slim' : 'base';
    $view->extend("MauticCoreBundle:Default:$template.html.php");
endif;
?>

<?php if (!$modalView): ?>
<div class="content-body">
    <?php if ($view['slots']->get('mauticContent', '') == 'dashboard' && $showvideo): ?>
        <div id="dashboard-widgets" class="dashboard-widgets cards">
            <div class="card-flex widget" style="width:100%;" role="document">
                <div class="card" style="height:550px;">
                    <div class="card-header">
                        <p style="padding:10px 15px;font-size:16px;">
                            <?php echo $view['translator']->trans('leadsengage.kyc.video_header'); ?>
                        </p>
                        <div class="dropdown">
                            <a href="javascript: void(0);" onclick="Mautic.RedirectToGivenURL('<?php echo $view['router']->path('mautic_dashboard_index', ['login' => 'dont_show_again']); ?>');" class="dont_show_again" ><span><i class="fa fa-eye-slash"></i><span style="padding:4px;"><?php echo $view['translator']->trans('leadsengage.kyc.dont_show'); ?></span></span></a>
                        </div>

                    </div>
                    <br>
                    <div class="card-body" style="margin-left:12%;">
                        <iframe width="87%" height="450px"
                                src="<?php echo $videoURL; ?>">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php echo $view->render('MauticCoreBundle:Default:pageheader.html.php'); ?>
	<?php $view['slots']->output('_content'); ?>
</div>

<?php $view['slots']->output('modal'); ?>
<?php echo $view['security']->getAuthenticationContent(); ?>
<?php else: ?>
<?php $view['slots']->output('_content'); ?>
<?php endif; ?>
