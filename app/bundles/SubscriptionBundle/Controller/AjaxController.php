<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SubscriptionBundle\Controller;

use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use PayPal\Api\Agreement;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Plan;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\Error;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController.
 */
class AjaxController extends CommonAjaxController
{
    /**
     * User Subscription.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function makepaymentAction(Request $request)
    {
        $username     = $request->request->get('username');
        $useremail    = $request->request->get('useremail');
        $useraddress  = $request->request->get('useraddress');
        $planname     = $request->request->get('planname');
        $plancycle    = $request->request->get('plancycle');
        $plancurrency = $request->request->get('plancurrency');
        $provider     ='paypal';
        if ($plancurrency == '₹') {
            $provider = 'razorpay';
        }
        $dataArray = ['success' => true];
        $repository=$this->get('le.core.repository.subscription');
        $planinfo  =$repository->getPlanInfo($provider, $planname, $plancycle);
        if (!empty($planinfo)) {
            $planid= $planinfo[0]['planid'];
            if ($provider == 'razorpay') {
                try {
                    $totalcount=120;
                    if ($plancycle == 'year') {
                        $totalcount=10;
                    }
                    //test card details
                    //5104015555555558 any cvv any future expiry date
                    $apikey                      =$this->coreParametersHelper->getParameter('razoparpay_apikey');
                    $apisecret                   =$this->coreParametersHelper->getParameter('razoparpay_apisecret');
                    $api                         = new Api($apikey, $apisecret);
                    $subscription                = $api->subscription->create(['plan_id' => $planid, 'customer_notify' => 1, 'total_count' => $totalcount]);
                    $subscriptionid              =$subscription->id;
                    $dataArray['subscriptionid'] =$subscriptionid;
                    $dataArray['provider']       ='razorpay';
                    $dataArray['apikey']         =$apikey;
                    //file_put_contents("/var/www/mauto/app/logs/payment.txt","New Subscription:".$subscriptionid."\n",FILE_APPEND);
                } catch (Error $ex) {
                    $dataArray['success']  =false;
                    $dataArray['errormsg'] ='Payment Error:'.$ex->getMessage();
                }
            } else {
                date_default_timezone_set('Asia/Kolkata');
                $timezone = date_default_timezone_get();
                $start_at = new \DateTime();
                $start_at->add(new \DateInterval('PT25H'));
                $start_at     =$start_at->format(\DateTime::RFC3339);
                $username     =str_replace(' ', '_', $username);
                $agreementname=$planname.'_'.$username.'_'.date('YmdHis');
                $agreement    = new Agreement();
                $agreement->setName($agreementname)
                    ->setDescription($agreementname)
                    ->setStartDate($start_at);
                $plan = new Plan();
                $plan->setId($planid);
                $agreement->setPlan($plan);

                $payer = new Payer();
                $payer->setPaymentMethod('paypal');
                $agreement->setPayer($payer);
                $clienthost          =$request->getHost();
                $clientprotocal      =$request->getScheme();
                $successparameters   =['provider' => 'paypal', 'status' => true];
                $returnUrl           = $this->generateUrl('le_subscription_status', $successparameters);
                $returnUrl           =$clientprotocal.'://'.$clienthost.$returnUrl;
                $cancelparameters    =['provider' => 'paypal', 'status' => false];
                $cancelUrl           = $this->generateUrl('le_subscription_status', $cancelparameters);
                $cancelUrl           =$clientprotocal.'://'.$clienthost.$cancelUrl;
                $merchantPreferences = new MerchantPreferences();
                $merchantPreferences->setReturnUrl($returnUrl)
                    ->setCancelUrl($cancelUrl)
                    ->setAutoBillAmount('yes')
                    ->setInitialFailAmountAction('CONTINUE')
                    ->setMaxFailAttempts('3')
//   ->setSetupFee(new Currency(array('value' => 1, 'currency' => 'USD')))
                ;
                $agreement->setOverrideMerchantPreferences($merchantPreferences);
                $cloneagreement = clone $agreement;
                try {
                    $paymenthelper            =$this->get('le.helper.payment');
                    $agreement                = $agreement->create($paymenthelper->getPayPalApiContext());
                    $approvalUrl              = $agreement->getApprovalLink();
                    $dataArray['approvalurl'] =$approvalUrl;
                } catch (Exception $ex) {
                    $dataArray['success']  =false;
                    $dataArray['errormsg'] ='Payment Error:'.$ex->getMessage();
                }
                $userTimeZone =$this->user->getTimezone();
                date_default_timezone_set($userTimeZone);
            }
        } else {
            $dataArray['success']  =false;
            $dataArray['errormsg'] ='Payment Error: Oops! Technical Error Found.Please contact support';
        }

        return $this->sendJsonResponse($dataArray);
    }

    public function validatepaymentAction(Request $request)
    {
        $paymentid                 = $request->request->get('paymentid');
        $subscriptionid            = $request->request->get('subscriptionid');
        $signature                 = $request->request->get('signature');
        $apisecret                 =$this->coreParametersHelper->getParameter('razoparpay_apisecret');
        $expectedSignature         = hash_hmac('sha256', $paymentid.'|'.$subscriptionid, $apisecret);
        $dataArray                 = ['success' => true];
        $parameters                =['provider' => 'razorpay', 'paymentid' => $paymentid, 'subscriptionid' => $subscriptionid];
        if ($expectedSignature === $signature) {
            $parameters['status']=true;
        } else {
            $parameters['status']=false;
        }
        $returnUrl            = $this->generateUrl('le_subscription_status', $parameters);
        $dataArray['redirect']=$returnUrl;

        return $this->sendJsonResponse($dataArray);
    }

    public function purchaseplanAction(Request $request)
    {
        $dataArray    = ['success' => true];
        $plancurrency = $request->request->get('plancurrency');
        $planamount   = $request->request->get('planamount');
        $planame      = $request->request->get('planname');
        $plankey      = $request->request->get('plankey');
        $provider     ='paypal';
        if ($plancurrency == '₹') {
            $provider = 'razorpay';
        }
        $username              =$this->user->getName();
        $useremail             =$this->user->getEmail();
        $dataArray['username'] =$username;
        $dataArray['useremail']=$useremail;
        $dataArray['provider'] =$provider;
        if ($provider == 'razorpay') {
            $apikey              =$this->coreParametersHelper->getParameter('razoparpay_apikey');
            $dataArray['apikey'] =$apikey;
            $dataArray['orderid']=uniqid();
        } else {
            $clienthost          =$request->getHost();
            $clientprotocal      =$request->getScheme();
            $payer               = new Payer();
            $payer->setPaymentMethod('paypal');
            $item = new Item();
            $item->setName($planame.' Plan Purchase')
                ->setCurrency('USD')
                ->setQuantity(1)
                ->setSku($plankey) // Similar to `item_number` in Classic API
                ->setPrice($planamount);
            $itemList = new ItemList();
            $itemList->setItems([$item]);
            $details = new Details();
            $details->setShipping(0)
                ->setTax(0)
                ->setSubtotal($planamount);
            $amount = new Amount();
            $amount->setCurrency('USD')
                ->setTotal($planamount)
                ->setDetails($details);
            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($itemList)
                ->setDescription($planame.' Plan Purchase')
                ->setInvoiceNumber(uniqid());
            $successparameters   =['provider' => 'paypal', 'status' => true];
            $returnUrl           = $this->generateUrl('le_payment_status', $successparameters);
            $returnUrl           =$clientprotocal.'://'.$clienthost.$returnUrl;
            $cancelparameters    =['provider' => 'paypal', 'status' => false];
            $cancelUrl           = $this->generateUrl('le_payment_status', $cancelparameters);
            $cancelUrl           =$clientprotocal.'://'.$clienthost.$cancelUrl;
            $redirectUrls        = new RedirectUrls();
            $redirectUrls->setReturnUrl($returnUrl)
                ->setCancelUrl($cancelUrl);
            $payment = new Payment();
            $payment->setIntent('sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions([$transaction]);
            $request = clone $payment;
            try {
                $paymenthelper            =$this->get('le.helper.payment');
                $payment                  =$payment->create($paymenthelper->getPayPalApiContext());
                $approvalUrl              = $payment->getApprovalLink();
                $dataArray['approvalurl'] =$approvalUrl;
            } catch (Exception $ex) {
                $dataArray['success']  =false;
                $dataArray['errormsg'] ='Payment Error:'.$ex->getMessage();
            }
        }

        return $this->sendJsonResponse($dataArray);
    }

    public function getAvailableCountAction()
    {
        $dataArray                  = ['success' => true];
        $availablecount             = $this->get('mautic.helper.licenseinfo')->getAvailableEmailCount();
        $dataArray['availablecount']=$availablecount;

        return $this->sendJsonResponse($dataArray);
    }

    public function capturepaymentAction(Request $request)
    {
        $dataArray['success']  =true;
        $paymentid             = $request->request->get('paymentid');
        $captureamount         = $request->request->get('captureamount');
        $apikey                =$this->coreParametersHelper->getParameter('razoparpay_apikey');
        $apisecret             =$this->coreParametersHelper->getParameter('razoparpay_apisecret');
        $api                   =new Api($apikey, $apisecret);
        try {
            $payment                   = $api->payment->fetch($paymentid)->capture(['amount'=>$captureamount]);
            $paymentstatus             =$payment->status;
            $error_code                =$payment->error_code;
            $error_desc                =$payment->error_description;
            $notes                     =$payment->notes;
            $orderid                   =$notes->merchant_order_id;
            $plankey                   =$notes->plankey;
            $parameters                =['provider' => 'razorpay', 'paymentid' => $paymentid, 'orderid' => $orderid];
            if ($error_code == null) {
                $parameters['status']=true;
                $repository          =$this->get('le.core.repository.subscription');
                $repository->updateEmailCredits($plankey);
            } else {
                $parameters['status']=false;
            }
            $returnUrl            = $this->generateUrl('le_payment_status', $parameters);
            $dataArray['redirect']=$returnUrl;
        } catch (Error $ex) {
            $dataArray['success']  =false;
            $dataArray['errormsg'] ='Payment Error:'.$ex->getMessage();
        }

        return $this->sendJsonResponse($dataArray);
    }
}
