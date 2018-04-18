<?php
/**
 * Created by Kaviarasan S.
 * User: cratio
 * Date: 17/4/18
 * Time: 5:18 PM.
 */
$showcgst  = '';
$showgst   = '';
$taxamount = 0;
if ($billing->getState() == 'Tamil Nadu') {
    $showcgst  = '';
    $showgst   = 'hide';
    $taxamount = $payment->getTaxamount();
    $taxamount = ($taxamount / 2);
} else {
    $showgst   = '';
    $showcgst  = 'hide';
    $taxamount = $payment->getTaxamount();
}
if ($billing->getCountry() != 'India' || $payment->getCurrency() != '₹') {
    $showcgst = $showgst = 'hide';
}
?>
<html>
<head>
    <title>Receipt : <?php echo $payment->getPlanLabel(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" type="image/x-icon" href="<?php echo $view['assets']->getUrl('media/images/favicon.ico') ?>" />
    <link rel="icon" sizes="192x192" href="<?php echo $view['assets']->getUrl('media/images/favicon.ico') ?>">
    <link rel="apple-touch-icon" href="<?php echo $view['assets']->getUrl('media/images/apple-touch-icon.png') ?>" />
    <style>
        body{
            font: normal 16px/24px "GT-Walsheim-Regular", "Poppins-Regular", Helvetica, Arial, sans-serif;
            color: #262626;
            -webkit-font-smoothing: antialiased;
        }
        body.canvas{
            font-family: "GT-Walsheim-Regular", "Poppins-Regular", Helvetica, Arial, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.5;
            position: relative;
            color: #555;
            background: #fff;
        }

        .hide{
            display:none;
        }
        #pageheader{
            font-family: "GT-Walsheim-Regular", "Poppins-Regular", Helvetica, Arial, sans-serif;
            margin:60px auto;
            height:550px;
            padding:20px;
            border-radius: 8px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            background-color: #fff;
            font-size: 14px;
            position: relative;
            width:680px;
        }
        #leadsengage_info{
            float:left;
        }
        #customer_info{
            float:right;
        }
        #invoice_info {
            float: left;
            width: 100%;
        }
        #payment_info{
            width: 100%;
            float:left;
        }
        .table_header{
            border-top: 1px solid #dddddd;
            border-bottom: 1px solid #dddddd;
            padding:5px;
        }
        #description_header{
            width:75%;
            text-align:left;
        }
        #amount_header{
            width:25%;
            text-align:right;
        }
        .description_body{
            width:75%;
            text-align:left;
        }
        .amount_body{
            width:25%;
            text-align:right;
        }
        .gst_body{
            width:75%;
            text-align:right;
        }
        .gstamount_body{
            width:25%;
            text-align:right;
        }
        .table_body{
            border-bottom: 1px solid #dddddd;
            padding:5px;
            height: 50px;
        }
        a {
            text-decoration: underline;
            -webkit-text-decoration-skip: ink;
            text-decoration-skip: ink;
            color: #262626;
            font-family: "GT-Walsheim-Medium", "Poppins-Medium", Helvetica, Arial, sans-serif;
            font-weight: normal;
            -webkit-transition: color 150ms;
            transition: color 150ms;
        }
    </style>
</head>
<body class="canvas">
<div id="pageheader">
    <div id="leadsengage_info">
        <p>
            <b>From:</b><br>
            LeadsEngage<br>
            52/41, New Colony, First Main Road,<br>
            Chromepet, Chennai - 44, TN, INDIA<br>
            <b>GST No:</b> 123456<br>
        </p>
    </div>
    <div id="customer_info">
        <p>
            <b>To:</b><br>
            <?php echo $billing->getCompanyname(); ?><br>
            <?php echo $billing->getCompanyaddress(); ?><br>
            <?php echo $billing->getCity().', '.$billing->getPostalcode().', '.$billing->getState().', '.$billing->getCountry(); ?><br>
            <b style="<?php echo ($billing->getGstnumber() == '') ? 'display:none' : ''; ?>">GST No:</b> <?php $billing->getGstnumber(); ?><br>
        </p>
    </div>

    <br>
    <br>
    <div id="invoice_info">
        <p>
            <b>Invoice #</b>:<?php echo $payment->getOrderID(); ?><br>
            <b>Date:</b> <?php echo $view['date']->toShort($payment->getcreatedOn()); ?><br>
            <b>Transaction #:</b> 289011037<br>
        </p>
    </div>
    <div id="payment_info">
        <table style="width:100%;padding-right:20px;font: inherit;">
            <thead>
            <th id="description_header" class="table_header" >
                Description
            </th>
            <th id="amount_header" class="table_header" >
                Amount (INR/USD)
            </th>
            </thead>
            <tbody>
            <tr>
                <td class="table_body description_body">
                    <?php echo $payment->getPlanLabel(); ?>
                </td>
                <td class="table_body amount_body">
                    <?php echo $payment->getCurrency().($payment->getProvider() == 'razorpay' ? number_format($payment->getNetamount()) : $payment->getNetamount())?>
                </td>
            </tr>
            <tr class="<?php echo $showcgst; ?>">
                <td class="table_body gst_body">
                    <p>Gross Amount
                        <br>SGST (9%)<br>CGST (9%)
                    </p>
                </td>
                <td class="table_body gstamount_body">
                    <p>
                        <?php echo $payment->getCurrency().($payment->getProvider() == 'razorpay' ? number_format($payment->getNetamount()) : $payment->getNetamount())?><br><?php echo $payment->getCurrency(); echo $taxamount; ?><br><?php echo $payment->getCurrency(); echo $taxamount; ?>
                    </p>
                </td>
            </tr>
            <tr class="<?php echo $showgst; ?>">
                <td class="table_body gst_body">
                    <p>Gross Amount<br>GST (18%)</p>
                </td>
                <td class="table_body gstamount_body">
                    <p>
                        <?php echo $payment->getCurrency().($payment->getProvider() == 'razorpay' ? number_format($payment->getAmount()) : $payment->getAmount())?><br><?php echo $payment->getCurrency(); echo $taxamount; ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td class="table_body gst_body">
                    Amount Paid
                </td>
                <td class="table_body amount_body">
                    <?php echo $payment->getCurrency().($payment->getProvider() == 'razorpay' ? number_format($payment->getAmount()) : $payment->getAmount())?>
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <br>
        <p>If you have any questions about this invoice, please contact <a href="mailto:support@leadsengage.com"><b>support@leadsengage.com</b></a>.</p>
        <p>The Leadsengage Team</p>
    </div>
</div>

</body>
</html>

