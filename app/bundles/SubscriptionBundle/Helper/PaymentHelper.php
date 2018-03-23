<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SubscriptionBundle\Helper;

use Mautic\CoreBundle\Factory\MauticFactory;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

/**
 * Class PaymentHelper.
 */
class PaymentHelper
{
    /**
     * @var MauticFactory
     */
    protected $factory;

    public function __construct(MauticFactory $factory)
    {
        $this->factory   = $factory;
    }

    public function getPayPalApiContext()
    {
        $clientid        =$this->factory->getParameter('paypal_clientid');
        $clientsecret    =$this->factory->getParameter('paypal_clientsecret');
        $paypalmode      =$this->factory->getParameter('paypal_mode');
        $paypallogpath   =$this->factory->getParameter('paypal_logpath');
        $paypalloglevel  =$this->factory->getParameter('paypal_loglevel');
        $paypallogenabled=$this->factory->getParameter('paypal_log_enabled');
        $paypalcachepath =$this->factory->getParameter('paypal_cachepath');
        $paypalrootpath  =$this->factory->getParameter('paypal_rootpath');
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

        return $apiContext;
    }
}
