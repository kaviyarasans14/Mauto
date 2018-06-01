<?php

?>
<style>
    #errors {
        font-size: 1.2rem;
        color: #a94442;
        line-height: 2.2;
        font-style: normal;
        letter-spacing: 0;
    }
</style>
<table class="payment-history">
    <thead>
    <button id="open-model-btn" type="button" class="btn btn-info" style="float:right;margin-right:70px;" data-toggle="modal" data-target="#emailVerifyModel">Verify Email</button>
    <div class="modal fade" id="emailVerifyModel">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <h4 class="modal-title">AWS Email Verification</h4>
                </div>
                <!-- body -->
                <div class="modal-body">
                        <div class="form-group" id ="user_email">
                            <label class="control-label required" for="email">E-mail address</label>
                            <input type="email" class="form-control" id="aws_email_verification" placeholder="Enter valid email" name="email" required="required">
                            <div class="help-block" id ="errors"></div>
                        </div>
                        <br>
                        <div class="modal-footer">
                            <div class="button_container" id="aws_email_verification_button">
                            <button type="button"  class="btn btn-default aws-verification-btn" id="aws_emailverification-btn"> Verify Email</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                       </div>
                </div>
            </div>
        </div>
    </div>

    <tr>
        <th>
          <span class="header">Verified Emails<span>
        </th>
        <th>
         <span class="header">Status<span>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($verifiedEmails as $verifiedEmail): ?>
        <tr>
          <td>
         <span class="data"><?php echo $verifiedEmail->getVerifiedEmails()?><span>
          </td>
          <td>
           <span class="data"><?php echo $verifiedEmail->getVerificationStatus() ?><span>
          </td>
        </tr>
    <?php endforeach; ?>
    <tr>
    </tr>
    </tbody>
</table>

