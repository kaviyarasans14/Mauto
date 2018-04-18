<?php

?>

   <table class="payment-history">
       <thead>
<tr>
       <th>
           <span class="header">Plan Name<span>
       </th>
    <th>
         <span class="header">Payment Date<span>
    </th>
    <th>
         <span class="header">Amount<span>
    </th>
    <th>
         <span class="header">Status<span>
    </th>
    <th>
         <span class="header"><span>
    </th>
</tr>
       </thead>
       <tbody>
       <?php foreach ($payments as $payment): ?>
       <tr>
           <td class="data">
           <span><?php echo $payment->getPlanLabel()?><span>
           </td>
           <td>
         <span class="data"><?php echo $view['date']->toFull($payment->getcreatedOn()); ?><span>
           </td>
           <td>
         <span class="data"><?php echo $payment->getCurrency().($payment->getProvider() == 'razorpay' ? number_format($payment->getAmount()) : $payment->getAmount())?><span>
           </td>
           <td>
         <span class="data"><?php echo $payment->getPaymentStatus()?><span>
           </td>
           <td>
         <a class="data <?php echo ($payment->getPaymentStatus() == 'Paid') ? '' : 'hide'; ?>" href="<?php echo $view['router']->generate('mautic_viewinvoice_action', ['id' => $payment->getId()]); ?>">View Invoice</a>
           </td>
       </tr>
       <?php endforeach; ?>
       </tbody>
   </table>


