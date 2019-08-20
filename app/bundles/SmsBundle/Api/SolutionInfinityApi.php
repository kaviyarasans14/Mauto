<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SmsBundle\Api;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\CoreBundle\Helper\PhoneNumberHelper;
use Mautic\PageBundle\Model\TrackableModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Monolog\Logger;

class SolutionInfinityApi extends AbstractSmsApi
{
    /**
     * @var \Services_Twilio
     */
    protected $client;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $senderid;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $sendingPhoneNumber;

    /**
     * TwilioApi constructor.
     *
     * @param TrackableModel    $pageTrackableModel
     * @param PhoneNumberHelper $phoneNumberHelper
     * @param IntegrationHelper $integrationHelper
     * @param Logger            $logger
     */
    public function __construct(TrackableModel $pageTrackableModel, PhoneNumberHelper $phoneNumberHelper, IntegrationHelper $integrationHelper, Logger $logger)
    {
        $this->logger = $logger;

        $integration = $integrationHelper->getIntegrationObject('SolutionInfinity');

        if ($integration && $integration->getIntegrationSettings()->getIsPublished()) {
            //$this->sendingPhoneNumber = $integration->getIntegrationSettings()->getFeatureSettings()['sending_phone_number'];

            $keys           = $integration->getDecryptedApiKeys();
            $this->username = $keys['apikey'];
            //$this->password = $keys['password'];
            $this->senderid = $keys['senderid'];
            $this->url      = $keys['url'];
        }

        parent::__construct($pageTrackableModel);
    }

    /**
     * @param string $number
     *
     * @return string
     */
    protected function sanitizeNumber($number)
    {
        $util   = PhoneNumberUtil::getInstance();
        $parsed = $util->parse($number, 'US');

        return $util->format($parsed, PhoneNumberFormat::E164);
    }

    /**
     * @param string $number
     * @param string $content
     *
     * @return bool|string
     */
    public function sendSms($number, $content)
    {
        if ($number === null) {
            return false;
        }

        try {
            $content = urlencode($content);
            $sendurl = $this->url;
            $baseurl = $sendurl.'?method=sms&api_key='.$this->username.'&sender='.$this->senderid;
            $sendurl =$baseurl.'&to='.$number.'&message='.$content;
            $handle  = curl_init($sendurl);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($handle);
            $response = json_decode($response);
            $status   =$response->{'status'};
            $message  =$response->{'message'};
            if ($status == 'OK') {
                return true;
            } else {
                $this->logger->addWarning(
                    $message
                );

                return false;
            }
        } catch (NumberParseException $e) {
            $this->logger->addWarning(
                $e->getMessage(),
                ['exception' => $e]
            );

            return $e->getMessage();
        }
    }
}
