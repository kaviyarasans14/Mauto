<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<!DOCTYPE html>
<html>
<?php echo $view->render('MauticCoreBundle:Default:head.html.php'); ?>

<body class="header-fixed">
<section id="app-wrapper">

<div id="complete_profile_header">
    <p id="complete_profile"><?php echo $view['translator']->trans('leadsengage.kyc.anti_spam'); ?>(3/3)</p>
</div>
    <div class="first_time_setup">
    <form class="form-group login-form" name="terms_condition" data-toggle="ajax" role="form" action="<?php echo $view['router']->path('mautic_kyc_action', ['objectAction' => 'condition']) ?>" method="post">
        <div class="pt-md pr-md pl-md pb-md">

            <div class="panel-body" style="width:80%;">
                <p id="anti_spam_policy">
                    <?php echo $view['translator']->trans('leadsengage.kyc.anti_spam_desc'); ?>
                </p>
                <div id="terms">

                    <h4><b>What is SPAM?</b></h4>
                    <p>Spam is any email you send to someone who hasn’t given you their direct permission to contact them on the topic of the email.</p>
                    <p>When you send an email to someone you don't know, that's an "unsolicited" email. Sending one unsolicited message to someone is obviously not spam. But when you send an unsolicited email to an entire list of people you don't know, that's spam.</p>

                    <h4><b>What kind of email addresses are OK to send to with Leadsengage?</b></h4>
                    <p>To send email to anyone, you need to have their permission. This could be done through:</p>
                    <ul>
                        <li>An email newsletter subscribe form on your web site.</li>
                        <li>An opt-in checkbox on a form. This checkbox must not be checked by default, the person completing the form must willingly select the checkbox to indicate they want to hear from you.</li>
                        <li>If someone completes an offline form like a survey or enters a competition, you can only contact them if it was explained to them that they would be contacted by email AND they ticked a box indicating they would like to be contacted.</li>
                        <li>Customers who have purchased from you within the last 2 years.</li>
                    </ul>

                    <h4><b>What kind of email address ARE NOT OK to send to with Leadsengage?</b></h4>
                    <p>You can't import or send to any email address which:</p>
                    <ul>
                        <li>You do not have explicit, provable permission to contact in relation to the topic of the email you’re sending.</li>
                        <li>You bought, loaned, rented or in any way acquired from a third party, no matter what they claim about quality or permission. You need to obtain permission yourself.</li>
                        <li>You haven’t contacted them via email in the last 2 years. Permission doesn’t age well and these people have either changed email address or won’t remember giving their permission in the first place.</li>
                        <li>You scraped or copy and pasted from the web. Just because people publish their email address doesn’t mean they want to hear from you.</li>
                    </ul>

                    <h4><b>What content MUST I include in my email?</b></h4>
                    <p>Every email you send using Leadsengage must include a single-click unsubscribe link that instantly removes the subscriber from your list. Once they unsubscribe, you can never email them again.</p>
                    <h4><b>Account Suspension</b></h4>
                    <p>We reserve the right to suspend your account immediately and start investigating your activity if your campaigns have high percentage of spam complaints (more than 0.2%), bounces (more than 5%), unsubscribes (more then 1%) or very small open rate (less than 3%). If it turns out that you were sending emails without permission - we will terminate your account. We can ask you to prove that you have permission from your recipients and we can close your account if you do not have such proof. Otherwise, we will activate your account and you will be able to use the service again. </p>
                    <h4><b>Multiple account abuse</b></h4>
                    <p>Creating multiple accounts with overlapping uses (similar email content, same links or same FROM email) or in order to evade the permanent suspension of a separate account is strictly forbidden.</p>

                </div>
                <br>
                <label class="control control-checkbox">
                    <?php echo $view['translator']->trans('leadsengage.kyc.terms_condition'); ?>
                    <input type="checkbox" name="kycinfo[conditionsagree]" required/>
                    <div class="control_indicator"></div>
                </label>
                <br>
                <label class="control control-checkbox">
                    <?php echo $view['translator']->trans('leadsengage.kyc.leadsengage_condition'); ?>
                    <input type="checkbox" name="kycinfo[leadsengage_conditionsagree]" required/>
                    <div class="control_indicator"></div>
                </label>
                <br>
                <button class="signup_next" type="submit" value="Next"><?php echo $view['translator']->trans('leadsengage.kyc.next_button'); ?></button>
                <span>&nbsp;&nbsp;&nbsp;&nbsp;Or&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <a class="signup_back" onclick="Mautic.RedirectToGivenURL('<?php echo $view['router']->path('mautic_dashboard_action', ['objectAction' => 'kyc']); ?>');"><?php echo $view['translator']->trans('leadsengage.kyc.go_back'); ?></a>
            </div>
        </div>
    </form>
    </div>
</section>
</body>
</html>