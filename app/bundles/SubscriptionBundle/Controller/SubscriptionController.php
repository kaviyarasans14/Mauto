<?php

namespace Mautic\SubscriptionBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use PayPal\Api\Agreement;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

/**
 * Class SubscriptionController.
 */
class SubscriptionController extends CommonController
{
    public function indexAction()
    {
        $clientip        = $this->request->getClientIp();
        $dataArray       = json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip='.$clientip));
        $countrycode     =$dataArray->{'geoplugin_countryCode'};
        $isIndianCurrency=false;
        if ($countrycode == '' || $isIndianCurrency == 'IN') {
            $isIndianCurrency=true;
        }

        return $this->delegateView([
            'viewParameters' => [
                'security'        => $this->get('mautic.security'),
                'contentOnly'     => 0,
                'plans'           => $this->factory->getAvailablePlans(),
                'isIndianCurrency'=> $isIndianCurrency,
                           ],
            'contentTemplate' => 'MauticSubscriptionBundle:Subscription:index.html.php',
            'passthroughVars' => [
                'activeLink'    => '#le_subscription_index',
                'mauticContent' => 'subscription',
                'route'         => $this->generateUrl('le_subscription_index'),
            ],
        ]);
    }

    public function statusAction()
    {
        $paymentid       = $this->request->get('paymentid');
        $subscriptionid  = $this->request->get('subscriptionid');
        $provider        = $this->request->get('provider');
        $status          = $this->request->get('status');
        if ($provider == 'paypal') {
            $clientid        =$this->coreParametersHelper->getParameter('paypal_clientid');
            $clientsecret    =$this->coreParametersHelper->getParameter('paypal_clientsecret');
            $paypalmode      =$this->coreParametersHelper->getParameter('paypal_mode');
            $paypallogpath   =$this->coreParametersHelper->getParameter('paypal_logpath');
            $paypalloglevel  =$this->coreParametersHelper->getParameter('paypal_loglevel');
            $paypallogenabled=$this->coreParametersHelper->getParameter('paypal_log_enabled');
            $paypalcachepath =$this->coreParametersHelper->getParameter('paypal_cachepath');
            $ectoken         = $this->request->get('token');
            $apiContext      = new ApiContext(
        new OAuthTokenCredential(
            $clientid,
            $clientsecret
        )
    );
            $apiContext->setConfig(
        [
            'mode'           => $paypalmode,
            'log.LogEnabled' => $paypallogenabled,
            'log.FileName'   => $paypallogpath,
            'log.LogLevel'   => $paypalloglevel, // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
            'cache.enabled'  => true,
            'cache.FileName' => $paypalcachepath, // for determining paypal cache directory
            // 'http.CURLOPT_CONNECTTIMEOUT' => 30
            // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
            //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
        ]
    );
            if ($status) {
                $agreement = new Agreement();
                try {
                    $agreement->execute($ectoken, $apiContext);
                    $subscriptionid=$agreement->getId();
                    $paymentid     ='NA';
                } catch (Exception $ex) {
                    $subscriptionid='NA';
                    $paymentid     ='NA';
                    $status        =false;
                }
            } else {
                $subscriptionid='NA';
                $paymentid     ='NA';
            }
        }

        return $this->delegateView([
        'viewParameters' => [
            'security'       => $this->get('mautic.security'),
            'contentOnly'    => 0,
            'paymentid'      => $paymentid,
            'subscriptionid' => $subscriptionid,
            'status'         => $status,
        ],
        'contentTemplate' => 'MauticSubscriptionBundle:Subscription:status.html.php',
        'passthroughVars' => [
            'activeLink'    => '#le_subscription_status',
            'mauticContent' => 'subscription-status',
            'route'         => $this->generateUrl('le_subscription_status'),
        ],
    ]);
    }
}
