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
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
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
                    $apikey                    =$this->coreParametersHelper->getParameter('razoparpay_apikey');
                    $apisecret                 =$this->coreParametersHelper->getParameter('razoparpay_apisecret');
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
                $clientid        =$this->coreParametersHelper->getParameter('paypal_clientid');
                $clientsecret    =$this->coreParametersHelper->getParameter('paypal_clientsecret');
                $paypalmode      =$this->coreParametersHelper->getParameter('paypal_mode');
                $paypallogpath   =$this->coreParametersHelper->getParameter('paypal_logpath');
                $paypalloglevel  =$this->coreParametersHelper->getParameter('paypal_loglevel');
                $paypallogenabled=$this->coreParametersHelper->getParameter('paypal_log_enabled');
                $paypalcachepath =$this->coreParametersHelper->getParameter('paypal_cachepath');
                $paypalrootpath  =$this->coreParametersHelper->getParameter('paypal_rootpath');
                if (!is_dir($paypalrootpath) && !file_exists($paypalrootpath)) {
                    mkdir($paypalrootpath, 0777);
                }
                if (!is_dir($paypallogpath) && !file_exists($paypallogpath)) {
                    mkdir($paypallogpath, 0777);
                }
                if (!is_dir($paypalcachepath) && !file_exists($paypalcachepath)) {
                    mkdir($paypalcachepath, 0777);
                }
                $dataArray['provider'] ='paypal';
                $apiContext            = new ApiContext(
                    new OAuthTokenCredential(
                        $clientid,
                        $clientsecret
                    )
                );
                $apiContext->setConfig(
                    [
                        'mode'           => $paypalmode,
                        'log.LogEnabled' => $paypallogenabled,
                        'log.FileName'   => $paypallogpath.'/paypal.log',
                        'log.LogLevel'   => $paypalloglevel, // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                        'cache.enabled'  => true,
                        'cache.FileName' => $paypalcachepath.'/auth.cache', // for determining paypal cache directory
                        // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                        // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                        //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
                    ]
                );
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
                    $agreement                = $agreement->create($apiContext);
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
        $clienthost        =$request->getHost();
        $clientprotocal    =$request->getScheme();
        $paymentid         = $request->request->get('paymentid');
        $subscriptionid    = $request->request->get('subscriptionid');
        $signature         = $request->request->get('signature');
        $expectedSignature = hash_hmac('sha256', $paymentid.'|'.$subscriptionid, $this->RAZOR_PAY_APP_SECRET);
        $dataArray         = ['success' => true];
        $parameters        =['provider' => 'razorpay', 'paymentid' => $paymentid, 'subscriptionid' => $subscriptionid];
        if ($expectedSignature === $signature) {
            $parameters['status']=true;
        } else {
            $parameters['status']=false;
        }
        $returnUrl            = $this->generateUrl('le_subscription_status', $parameters);
        $dataArray['redirect']=$returnUrl;

        return $this->sendJsonResponse($dataArray);
    }
}
