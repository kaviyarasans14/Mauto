<?php
/**
 * Created by PhpStorm.
 * User: prabhu
 * Date: 30/1/18
 * Time: 11:56 AM.
 */
?>


<div id="bee-plugin-container" class="hide">
    <div class="builder-content">
        <input type="hidden" id="builder_url" value="<?php echo $view['router']->path('mautic_'.$type.'_action', ['objectAction' => 'builder', 'objectId' => $objectId]); ?>" />
    </div>
    <div id="bee-plugin-btnpanel">
        <div class="row">
            <div class="col-xs-12">
               <!-- <button type="button" class="btn btn-primary btn-bee-show-preview" onclick="javascript:bee.preview();">
                    <?php echo $view['translator']->trans('mautic.email.beeeditor.showpreview'); ?>
                </button>
                <button type="button" class="btn btn-primary btn-bee-show-structure" onclick="javascript:bee.toggleStructure();">
                    <?php echo $view['translator']->trans('mautic.email.beeeditor.showstructure'); ?>
                </button>
                <button type="button" class="btn btn-primary btn-bee-save" onclick="javascript:bee.save();">
                    <?php echo $view['translator']->trans('mautic.email.beeeditor.save'); ?>
                </button>-->
                <button type="button" class="btn btn-primary btn-bee-close-editor" onclick="Mautic.closeBeeEditor();">
                    <?php echo $view['translator']->trans('mautic.email.beeeditor.closeeditor'); ?>
                </button>
            </div>
        </div>
    </div>
    <div id="bee-plugin-viewpanel">

    </div>
</div>
