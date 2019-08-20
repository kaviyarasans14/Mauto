<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use Symfony\Component\Form\FormView;

$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'email');

$dynamicContentPrototype = $form['dynamicContent']->vars['prototype'];

if (empty($form['dynamicContent']->children[0]['filters']->vars['prototype'])) {
    $filterBlockPrototype = null;
} else {
    $filterBlockPrototype = $form['dynamicContent']->children[0]['filters']->vars['prototype'];
}

if (empty($form['dynamicContent']->children[0]['filters']->children[0]['filters']->vars['prototype'])) {
    $filterSelectPrototype = null;
} else {
    $filterSelectPrototype = $form['dynamicContent']->children[0]['filters']->children[0]['filters']->vars['prototype'];
}

$variantParent = $email->getVariantParent();
$isExisting    = $email->getId();
$isCloneOp     =!empty($isClone) && $isClone;
$subheader     = ($variantParent) ? '<div><span class="small">'.$view['translator']->trans('mautic.core.variant_of', [
        '%name%'   => $email->getName(),
        '%parent%' => $variantParent->getName(),
    ]).'</span></div>' : '';

$header = $isExisting ?
    $view['translator']->trans('mautic.email.header.edit',
        ['%name%' => $email->getName()]) :
    $view['translator']->trans('mautic.email.header.new');

$view['slots']->set('headerTitle', $header.$subheader);

$emailType = $form['emailType']->vars['data'];

if (!isset($attachmentSize)) {
    $attachmentSize = 0;
}

$templates = [
    'select'    => 'select-template',
    'countries' => 'country-template',
    'regions'   => 'region-template',
    'timezones' => 'timezone-template',
    'stages'    => 'stage-template',
    'locales'   => 'locale-template',
];

$attr                 = $form->vars['attr'];
$isAdmin              =$view['security']->isAdmin();
$isCodeMode           = ($email->getTemplate() === 'mautic_code_mode');
$isbasiceditor        =$email->getBeeJSON() == null || $email->getBeeJSON() == '';
$formcontainserror    =$view['form']->containsErrors($form);
$activatebasiceditor  =($formcontainserror || $isCloneOp || $isMobile) && $isbasiceditor ? 'active' : '';
$activateadvanceeditor=($formcontainserror || $isCloneOp || !$isMobile) && !$isbasiceditor ? 'active' : '';
$hidebasiceditor      =($formcontainserror || $isCloneOp || !$isMobile) && !$isbasiceditor ? 'hide' : '';
$hideadvanceeditor    =($formcontainserror || $isCloneOp || $isMobile) && $isbasiceditor ? 'hide' : '';
$activateotherconfig  ='';
if ($formcontainserror) {
    $activatebasiceditor  ='';
    $activateadvanceeditor='';
    $activateotherconfig  ='active in';
}
$hideawsemailoptions = '';
$style               ='78%';
$tabindex            ='-1';
$pointereventstyle   = 'pointer-events: none;background-color: #ebedf0;opacity: 1;';
if ($mailertransport != 'mautic.transport.amazon') {
    $hideawsemailoptions  = 'hide';
    $style                = '';
    $pointereventstyle    = '';
    $tabindex             = '';
}
?>
<?php echo $view['form']->start($form, ['attr' => $attr]); ?>
    <div class="box-layout">
        <div class="col-md-9 height-auto bg-white">
            <div class="row">
                <div class="col-xs-12">
                    <table style="width: 100%">
                      <tr>
                        <td style="width: 80%"><div class="form-group col-xs-12" >
                              <?php echo $view['form']->label($form['subject']); ?>
                              <div>
                                <?php echo $view['form']->widget($form['subject']); ?>
                              </div>
                                </div>
                        </td>
                        <td style="width: 20%">
                              <li class="dropdown" style="display: block;">
                                 <a class="btn btn-nospin btn-primary btn-sm hidden-xs" style="margin-top: 10px;font-size: 13px;" data-toggle="dropdown" href="#">
                                        <span>Personalize</span> </span><span><i class="caret" ></i>
                                 </a>
                              <ul class="dropdown-menu dropdown-menu-right">
                                 <li>
                                  <div class="insert-tokens" style="background-color: whitesmoke;/*width: 350px;*/overflow-y: scroll;max-height: 154px;">
                              </div
                            </li>
                               </ul>
                              </li>
                            </td>
                        </tr>
                    </table>
                    <!-- tabs controls -->
                    <ul class="bg-auto nav nav-tabs pr-md pl-md">
                        <!--builder disabled due to bee editor-->
                       <!-- <li class="active">
                            <a href="#email-container" role="tab" data-toggle="tab">
                                <?php //echo $view['translator']->trans('mautic.core.form.theme');?>
                            </a>
                        </li>-->
                        <li <?php echo $activatebasiceditor != '' ? 'class='.$activatebasiceditor : '' ?>>
                            <a <?php echo $hidebasiceditor != '' ? 'class='.$hidebasiceditor : '' ?> id="email-editor-basic" href="#email-basic-container" role="tab" data-toggle="tab">
                                <?php echo $view['translator']->trans('mautic.email.form.editor.basic'); ?>
                            </a>
                        </li>
                        <li <?php echo $activateadvanceeditor != '' ? 'class='.$activateadvanceeditor : '' ?>>
                            <a <?php echo $hideadvanceeditor != '' ? 'class='.$hideadvanceeditor : '' ?> id="email-editor-advance" href="#email-advance-container" role="tab" data-toggle="tab">
                                <?php echo $view['translator']->trans('mautic.email.form.editor.advance'); ?>
                            </a>
                        </li>
                        <li <?php echo $activateotherconfig != '' ? 'class='.$activateotherconfig : '' ?>>
                            <a href="#email-other-container" role="tab" data-toggle="tab">
                                <?php echo $view['translator']->trans('mautic.email.form.editor.other'); ?>
                            </a>
                        </li>
                        <li id="dynamic-content-tab" <?php echo (!$isCodeMode) ? 'class="hidden"' : ''; ?>>
                            <a href="#dynamic-content-container" role="tab" data-toggle="tab">
                                <?php echo $view['translator']->trans('mautic.core.dynamicContent'); ?>
                            </a>
                        </li>
                    </ul>
                    <!--/ tabs controls -->
                    <div class="tab-content pa-md">
                        <!--builder disabled due to bee editor-->
                       <!-- <div class="tab-pane fade in active bdr-w-0" id="email-container">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php //echo $view['form']->row($form['template']);?>
                                </div>
                            </div>
                            <?php //echo $view->render('MauticCoreBundle:Helper:theme_select.html.php', [
                                //'type'   => 'email',
                               // 'themes' => $themes,
                              //  'active' => $form['template']->vars['value'],
                           // ]);?>
                        </div>-->
                        <div class="tab-pane fade in bdr-w-0 <?php echo $activatebasiceditor ?>" id="email-basic-container">
                            <?php echo $view['form']->widget($form['customHtml']); ?>
                        </div>
                        <div class="tab-pane fade in bdr-w-0 <?php echo $activateadvanceeditor ?>" id="email-advance-container">
                            <div class="row">
                                <div class="col-md-12 hide">
                                    <?php echo $view['form']->row($form['template']); ?>
                                </div>
                            </div>
                            <?php echo $view->render('MauticEmailBundle:Email:bee_template_select.html.php', [
                                'beetemplates' => $beetemplates,
                                'active'       => $form['template']->vars['value'],
                            ]); ?>
                        </div>
                        <div class="tab-pane fade bdr-w-0 <?php echo $activateotherconfig ?>" id="email-other-container">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php echo $view['form']->row($form['fromName']); ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="pull-left" style="max-width:<?php echo $style; ?>;">
                                        <?php echo $view['form']->row($form['fromAddress'],
                                            ['attr' => ['tabindex' => $tabindex, 'style' =>$pointereventstyle]]); ?>
                                    </div>
                                        <?php echo $view['form']->widget($form['fromAddress']); ?>
                                    <li class="dropdown <?php echo $hideawsemailoptions; ?>" name="verifiedemails" id="verifiedemails" style="display: block;margin-left: 191px;">
                                        <a class="btn btn-nospin btn-primary btn-sm hidden-xs" style="font-size:13px;margin-top:23px;" data-toggle="dropdown" href="#">
                                            <span><?php echo $view['translator']->trans('le.core.button.aws.load'); ?></span> </span><span><i class="caret" ></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right" id="verifiedemails">
                                            <li>
                                                    <?php foreach ($verifiedemail as $key=> $value): ?>
                                            <li >
                                                <a class="verified-emails" id="data-verified-emails" data-verified-emails="<?php echo $value; ?>"><?php echo $value; ?></a>
                                            </li>
                                                   <?php endforeach; ?>
                                            </li>
                                        </ul>
                                    </li>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <?php echo $view['form']->row($form['replyToAddress']); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo $view['form']->row($form['bccAddress']); ?>
                                </div>
                            </div>
                            <?php if ($isAdmin):?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="pull-left">
                                        <?php echo $view['form']->label($form['assetAttachments']); ?>
                                    </div>
                                    <div class="text-right pr-10">
                                        <span class="label label-info" id="attachment-size"><?php echo $attachmentSize; ?></span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <?php echo $view['form']->widget($form['assetAttachments']); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                             <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <?php echo $view['form']->label($form['plainText']); ?>
                                    </div>
                                    <div class="text-right pr-10">
                                        <i class="fa fa-spinner fa-spin ml-2 plaintext-spinner hide"></i>
                                        <a class="small" onclick="Mautic.autoGeneratePlaintext();"><?php echo $view['translator']->trans('mautic.email.plaintext.generate'); ?></a>
                                    </div>
                                    <div class="clearfix"></div>
                                    <?php echo $view['form']->widget($form['plainText']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade bdr-w-0" id="dynamic-content-container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <?php
                                        $tabHtml = '<div class="col-xs-3 dynamicContentFilterContainer">';
                                        $tabHtml .= '<ul class="nav nav-tabs tabs-left" id="dynamicContentTabs">';
                                        $tabHtml .= '<li><a href="javascript:void(0);" role="tab" class="btn btn-primary" id="addNewDynamicContent"><i class="fa fa-plus text-success"></i> '.$view['translator']->trans('mautic.core.form.new').'</a></li>';
                                        $tabContentHtml = '<div class="tab-content pa-md col-xs-9" id="dynamicContentContainer">';

                                        foreach ($form['dynamicContent'] as $i => $dynamicContent) {
                                            $linkText = $dynamicContent['tokenName']->vars['value'] ?: $view['translator']->trans('mautic.core.dynamicContent').' '.($i + 1);

                                            $tabHtml .= '<li class="'.($i === 0 ? ' active' : '').'"><a role="tab" data-toggle="tab" href="#'.$dynamicContent->vars['id'].'">'.$linkText.'</a></li>';

                                            $tabContentHtml .= $view['form']->widget($dynamicContent);
                                        }

                                        $tabHtml .= '</ul></div>';
                                        $tabContentHtml .= '</div>';

                                        echo $tabHtml;
                                        echo $tabContentHtml;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 bg-white height-auto bdr-l">
            <div class="pr-lg pl-lg pt-md pb-md">

                <?php echo $view['form']->row($form['name']); ?>
                <?php if ($isVariant): ?>
                    <?php echo $view['form']->row($form['variantSettings']); ?>
                    <?php echo $view['form']->row($form['isPublished']); ?>
                    <?php if ($isAdmin): ?>
                       <?php echo $view['form']->row($form['publishUp']); ?>
                       <?php echo $view['form']->row($form['publishDown']); ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div id="leadList"<?php echo ($emailType == 'template') ? ' class="hide"' : ''; ?>>
                        <?php echo $view['form']->row($form['lists']); ?>
                    </div>
                    <?php echo $view['form']->row($form['category']); ?>
                <div class="hide">
                    <?php echo $view['form']->row($form['language']); ?>
                    <div id="segmentTranslationParent"<?php echo ($emailType == 'template') ? ' class="hide"' : ''; ?>>
                        <?php echo $view['form']->row($form['segmentTranslationParent']); ?>
                    </div>
                    <div id="templateTranslationParent"<?php echo ($emailType == 'list') ? ' class="hide"' : ''; ?>>
                        <?php echo $view['form']->row($form['templateTranslationParent']); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($isAdmin):?>
                    <?php if (!$isVariant): ?>
                        <?php echo $view['form']->row($form['isPublished']); ?>
                        <?php echo $view['form']->row($form['publishUp']); ?>
                        <?php echo $view['form']->row($form['publishDown']); ?>
                    <?php endif; ?>

                    <?php echo $view['form']->row($form['unsubscribeForm']); ?>
                    <?php if (!(empty($permissions['page:preference_center:viewown']) &&
                        empty($permissions['page:preference_center:viewother']))): ?>
                        <?php echo $view['form']->row($form['preferenceCenter']); ?>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (!$isVariant): ?>
                        <?php echo $view['form']->row($form['isPublished']); ?>
                    <?php endif; ?>
                    <hr />
                    <h5><?php echo $view['translator']->trans('mautic.email.utm_tags'); ?></h5>
                    <br />
                    <?php
                    foreach ($form['utmTags'] as $i => $utmTag):
                        echo $view['form']->row($utmTag);
                    endforeach;
                    ?>
                <?php endif; ?>
            </div>
            <div class="hide">
                <?php echo $view['form']->rest($form); ?>
            </div>
        </div>
    </div>

<?php echo $view['form']->row($form['customHtml']); ?>
<?php echo $view['form']->end($form); ?>

    <div id="dynamicContentPrototype" data-prototype="<?php echo $view->escape($view['form']->widget($dynamicContentPrototype)); ?>"></div>
<?php if ($filterBlockPrototype instanceof FormView) : ?>
    <div id="filterBlockPrototype" data-prototype="<?php echo $view->escape($view['form']->widget($filterBlockPrototype)); ?>"></div>
<?php endif; ?>
<?php if ($filterSelectPrototype instanceof FormView) : ?>
    <div id="filterSelectPrototype" data-prototype="<?php echo $view->escape($view['form']->widget($filterSelectPrototype)); ?>"></div>
<?php endif; ?>

    <div class="hide" id="templates">
        <?php foreach ($templates as $dataKey => $template): ?>
            <?php $attr = ($dataKey == 'tags') ? ' data-placeholder="'.$view['translator']->trans('mautic.lead.tags.select_or_create').'" data-no-results-text="'.$view['translator']->trans('mautic.lead.tags.enter_to_create').'" data-allow-add="true" onchange="Mautic.createLeadTag(this)"' : ''; ?>
            <select class="form-control not-chosen <?php echo $template; ?>" name="emailform[dynamicContent][__dynamicContentIndex__][filters][__dynamicContentFilterIndex__][filters][__name__][filter]" id="emailform_dynamicContent___dynamicContentIndex___filters___dynamicContentFilterIndex___filters___name___filter"<?php echo $attr; ?>>
                <?php
                if (isset($form->vars[$dataKey])):
                    foreach ($form->vars[$dataKey] as $value => $label):
                        if (is_array($label)):
                            echo "<optgroup label=\"$value\">\n";
                            foreach ($label as $optionValue => $optionLabel):
                                echo "<option value=\"$optionValue\">$optionLabel</option>\n";
                            endforeach;
                            echo "</optgroup>\n";
                        else:
                            if ($dataKey == 'lists' && (isset($currentListId) && (int) $value === (int) $currentListId)) {
                                continue;
                            }
                            echo "<option value=\"$value\">$label</option>\n";
                        endif;
                    endforeach;
                endif;
                ?>
            </select>
        <?php endforeach; ?>
    </div>
<?php echo $view->render('MauticEmailBundle:Email:beeeditor.html.php', ['objectId'      => $email->getSessionId(), 'type'          => 'email']); ?>
<?php //builder disabled due to bee editor
//echo $view->render('MauticCoreBundle:Helper:builder.html.php', [
//    'type'          => 'email',
//    'isCodeMode'    => $isCodeMode,
//    'sectionForm'   => $sectionForm,
//    'builderAssets' => $builderAssets,
//    'slots'         => $slots,
//    'sections'      => $sections,
//    'objectId'      => $email->getSessionId(),
//]);?>
<?php
$type = $email->getEmailType();
if ((empty($updateSelect) && !$isExisting && !$formcontainserror && !$variantParent && empty($type)) || empty($type) || !empty($forceTypeSelection)):
    echo $view->render('MauticCoreBundle:Helper:form_selecttype.html.php',
        [
            'item'       => $email,
            'mauticLang' => [
                'newListEmail'     => 'mautic.email.type.list.header',
                'newTemplateEmail' => 'mautic.email.type.template.header',
            ],
            'typePrefix'          => 'email',
            'cancelUrl'           => 'mautic_email_index',
            'header'              => 'mautic.email.type.header',
            'typeOneHeader'       => 'mautic.email.type.template.header',
            'typeOneIconClass'    => 'fa-cube',
            'typeOneDescription'  => 'mautic.email.type.template.description',
            'typeOneOnClick'      => "Mautic.selectEmailType('template');",
            'typeTwoHeader'       => 'mautic.email.type.list.header',
            'typeTwoIconClass'    => 'fa-pie-chart',
            'typeTwoDescription'  => 'mautic.email.type.list.description',
            'typeTwoOnClick'      => "Mautic.selectEmailType('list');",
            'typeThreeHeader'     => 'mautic.email.editor.codeeditor.header',
            'typeThreeIconClass'  => 'fas fa-code',
            'typeThreeOnClick'    => "Mautic.selectEmailEditor('code');",
            'typeThreeDescription'=> 'mautic.email.editor.codeeditor.description',
        ]);
endif;
?>
<?php
$type    = $email->getEmailType();
if (empty($updateSelect) && !$isCloneOp && !$isExisting && !$formcontainserror && !$variantParent && !$isMobile):
    echo $view->render('MauticCoreBundle:Helper:form_selecttype.html.php',
        [
            'item'                => $email,
            'mauticLang'          => [],
            'typePrefix'          => 'email',
            'cancelUrl'           => $type == 'template' ? 'mautic_email_index' : 'mautic_email_campaign_index',
            'header'              => 'mautic.email.editor.header',
            'typeOneHeader'       => 'mautic.email.editor.basic.header',
            'typeOneIconClass'    => 'fa-cube',
            'typeOneDescription'  => 'mautic.email.editor.basic.description',
            'typeOneOnClick'      => "Mautic.selectEmailEditor('basic');",
            'typeTwoHeader'       => 'mautic.email.editor.advance.header',
            'typeTwoIconClass'    => 'fa-pie-chart',
            'typeTwoDescription'  => 'mautic.email.editor.advance.description',
            'typeTwoOnClick'      => "Mautic.selectEmailEditor('advance');",
            'typeThreeHeader'     => 'mautic.email.editor.codeeditor.header',
            'typeThreeIconClass'  => 'fas fa-code',
            'typeThreeOnClick'    => "Mautic.selectEmailEditor('code');",
            'typeThreeDescription'=> 'mautic.email.editor.codeeditor.description',
        ]);
endif;
?>
