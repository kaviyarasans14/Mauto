<?php $isAdmin=$view['security']->isAdmin(); ?>
<div class="row">
    <div class="col-xs-12">
        <h4 class="mb-sm"><?php echo $view['translator']->trans('mautic.lead.lead.update.action.help'); ?></h4>
    </div>
    <?php foreach ($form->children as $child): ?>
        <?php  if (!$isAdmin): ?>
            <?php if ($child->vars['label'] == 'Points'): ?>
                <div class="hidden">
                    <?php echo $view['form']->label($child); ?>
                    <?php echo $view['form']->widget($child); ?>
                </div>
            <?php else:?>
                <div class="form-group col-xs-6">
                    <?php echo $view['form']->label($child); ?>
                    <?php echo $view['form']->widget($child); ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="form-group col-xs-6">
                <?php echo $view['form']->label($child); ?>
                <?php echo $view['form']->widget($child); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>