<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\EmailBundle\Controller;

use Mautic\CoreBundle\Controller\BuilderControllerTrait;
use Mautic\CoreBundle\Controller\FormController;
use Mautic\CoreBundle\Controller\FormErrorMessagesTrait;
use Mautic\LeadBundle\Controller\EntityContactsTrait;

class EmailUsageController extends FormController
{
    use BuilderControllerTrait;
    use FormErrorMessagesTrait;
    use EntityContactsTrait;

    public function emailstatAction()
    {
        $mailTransport = $this->coreParametersHelper->getParameter('mailer_transport');
        $usagedetails  = '';
        if ($mailTransport == 'mautic.transport.elasticemail') {
            $usagedetails = $this->getElasticEmailStat();
        } elseif ($mailTransport == 'mautic.transport.sendgrid_api') {
            $usagedetails = $this->getSendGridStat();
        }
        $username                 = $this->coreParametersHelper->getParameter('mailer_user');
        $usagedetails['username'] = $username;

        return $this->delegateView([
            'viewParameters' => [
                'usagedetails' => $usagedetails,
            ],
            'contentTemplate' => 'MauticEmailBundle:Email:emailusage.html.php',
            'passthroughVars' => [
                'activeLink'    => '#mautic_email_usage',
                'mauticContent' => 'payment-status',
                'route'         => $this->generateUrl('mautic_email_usage'),
            ],
        ]);
    }

    public function getElasticAccountDetails($apikey, $name, $limit = false)
    {
        $data_array['apikey']=$apikey;
        if ($limit) {
            $data_array['limit'] = 1;
        }
        $ch = curl_init("https://api.elasticemail.com/v2/account/$name");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_array));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result     = curl_exec($ch);
        $dataresult = json_decode($result, true);

        return $dataresult['data'];
    }

    public function getElasticEmailStat()
    {
        $apikey                   = $this->coreParametersHelper->getParameter('mailer_password');
        $overviewresult           = $this->getElasticAccountDetails($apikey, 'overview');
        $loadresult               = $this->getElasticAccountDetails($apikey, 'load');
        $loadrepresult            = $this->getElasticAccountDetails($apikey, 'loadreputationhistory', 1);
        $result                   = array_merge($overviewresult, $loadresult);
        $result                   = array_merge($result, $loadrepresult);
        $usageres                 = [];
        $bouncepercent            = ($result['blockedcontactscount'] / $result['totalemailssent']) * 100;
        $usageres['bouncecount']  = $result['blockedcontactscount'];
        $usageres['abusepercent'] = round($bouncepercent, 2);
        $usageres['openpercent']  = round($result[0]['openedpercent'], 2);
        $usageres['spamscore']    = $result[0]['averagespamscore'];
        $usageres['totalemail']   = $result['totalemailssent'];
        $usageres['reputation']   = round($result['reputation'], 2);
        $usageres['status']       = $result['statusformatted'];
        $usageres['clickcount']   = round($result[0]['clickedpercent'], 2);

        return $usageres;
    }

    public function getSendGridStat()
    {
        $username      = $this->coreParametersHelper->getParameter('mailer_user');
        $usagearr      = $this->getSendGridDetails($username);
        $reparr        = $this->getSendGridReputation($username);
        $status        = $this->checkSendGridStatus($username);
        $requestcount  = 0;
        $bouncecount   = 0;
        $opencount     = 0;
        $spamcount     = 0;
        $deliverycount = 0;
        $clickcount    = 0;
        for ($i = 0; $i < sizeof($usagearr); ++$i) {
            $usageresult   = $usagearr[$i]['stats'][0]['metrics'];
            $bouncecount   = $bouncecount + $usageresult['bounces'];
            $requestcount  = $requestcount + $usageresult['requests'];
            $opencount     = $opencount + $usageresult['unique_opens'];
            $spamcount     = $spamcount + $usageresult['spam_reports'];
            $deliverycount = $deliverycount + $usageresult['delivered'];
            $clickcount    = $clickcount + $usageresult['unique_clicks'];
        }
        $openpercent               = ($opencount / $deliverycount) * 100;
        $spampercent               = ($spamcount / $deliverycount) * 100;
        $bouncepercent             = ($bouncecount / $requestcount) * 100;
        $clickpercent              = ($clickcount / $deliverycount) * 100;
        $usageres                  = [];
        $usageres['totalemail']    = $requestcount;
        $usageres['abusepercent']  = round($bouncepercent, 2);
        $usageres['openpercent']   = round($openpercent, 2);
        $usageres['spamscore']     = $spamcount;
        $usageres['deliverycount'] = $deliverycount;
        $usageres['bouncecount']   = $bouncecount;
        $usageres['reputation']    = round($reparr[0]['reputation'], 2);
        $usageres['status']        = $status;
        $usageres['clickcount']    = $clickpercent;

        return $usageres;
    }

    public function getSendGridDetails($username)
    {
        $startdate                  = date('Y-m-d', strtotime('-3 Months'));
        $enddate                    = date('Y-m-d');
        $data_array['start_date']   =$startdate;
        $data_array['end_date']     =$enddate;
        $data_array['aggregated_by']='month';
        $payload                    = json_encode($data_array);
        $ch                         = curl_init("https://api.sendgrid.com/v3/stats?start_date=$startdate&end_date=$enddate&aggregated_by=month");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'authorization: Bearer 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',
            'On-behalf-of: '.$username,
        ]);

        $result = curl_exec($ch);
        $result = json_decode($result, true);

        return $result;
    }

    public function getSendGridReputation($username)
    {
        $data_array['usernames']=$username;
        $payload                = json_encode($data_array);
        $ch                     = curl_init("https://api.sendgrid.com/v3/subusers/reputations?usernames=$username");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'authorization: Bearer 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',
        ]);

        $result = curl_exec($ch);
        $result = json_decode($result, true);

        return $result;
    }

    public function checkSendGridStatus($subusername)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_URL, "https://api.sendgrid.com/v3/subusers?username=$subusername");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer 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', ]);
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        if (isset($result[0]['disabled']) && !$result[0]['disabled']) {
            return 'Active';
        } else {
            return 'InActive';
        }
    }
}
