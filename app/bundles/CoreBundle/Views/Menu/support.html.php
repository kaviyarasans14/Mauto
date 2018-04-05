<li class="dropdown">
    <a class="btn btn-nospin btn-primary btn-sm hidden-xs" style="margin-top: 17px;font-weight: 700;padding: 5px 10px;font-size: 11px;line-height: 1.456;border-radius: 2px;color: #fff;background-color: #375695;border-color: #375695;" data-toggle="dropdown" href="#">
        <span><i class="fa fa-question-circle"style="font-size: 13px;" ></i> <span>Help</span></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-right">
        <li>
            <a href="https://leadsengage.com/"  target="_blank">
                <i class="fa  fa-info-circle fs-14"></i><span><?php echo $view['translator']->trans('le.user.account.knowledgebase'); ?></span>
            </a>
        </li>
        <li>
            <a href="https://leadsengage.com/video-tutorials/"  target="_blank">
                <i class="fa fa-video-camera fs-14"></i><span><?php echo $view['translator']->trans('le.user.account.videotutorials'); ?></span>
            </a>
        </li>
        <li>
            <a href="https://leadsengage.freshdesk.com/support/tickets/new" target="_blank">
                <i class="fa fa-support"></i><span><?php echo $view['translator']->trans('le.user.account.submitticket'); ?></span>
            </a>
        </li>
    </ul>
</li>
<a class="btn btn-nospin btn-primary btn-sm hidden-xs buycredits"
   href="<?php echo $view['router']->path('le_plan_index'); ?>">
    <i class="fa fa-plus-circle fs-14"></i>
    <span><?php echo $view['translator']->trans('le.plans.buycredits'); ?></span>
</a>
