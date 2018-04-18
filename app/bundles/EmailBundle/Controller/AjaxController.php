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

use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Mautic\CoreBundle\Controller\AjaxLookupControllerTrait;
use Mautic\CoreBundle\Controller\VariantAjaxControllerTrait;
use Mautic\EmailBundle\Helper\PlainTextHelper;
use Mautic\EmailBundle\Model\EmailModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController.
 */
class AjaxController extends CommonAjaxController
{
    use VariantAjaxControllerTrait;
    use AjaxLookupControllerTrait;

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function getAbTestFormAction(Request $request)
    {
        return $this->getAbTestForm(
            $request,
            'email',
            'email_abtest_settings',
            'emailform',
            'MauticEmailBundle:AbTest:form.html.php',
            ['MauticEmailBundle:AbTest:form.html.php', 'MauticEmailBundle:FormTheme\Email']
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function sendBatchAction(Request $request)
    {
        $dataArray = ['success' => 0];

        /** @var \Mautic\EmailBundle\Model\EmailModel $model */
        $model    = $this->getModel('email');
        $objectId = $request->request->get('id', 0);
        $pending  = $request->request->get('pending', 0);
        $limit    = $request->request->get('batchlimit', 100);

        if ($objectId && $entity = $model->getEntity($objectId)) {
            $dataArray['success'] = 1;
            $session              = $this->get('session');
            $progress             = $session->get('mautic.email.send.progress', [0, (int) $pending]);
            $stats                = $session->get('mautic.email.send.stats', ['sent' => 0, 'failed' => 0, 'failedRecipients' => []]);

            if ($pending && !$inProgress = $session->get('mautic.email.send.active', false)) {
                $session->set('mautic.email.send.active', true);
                list($batchSentCount, $batchFailedCount, $batchFailedRecipients) = $model->sendEmailToLists($entity, null, $limit);

                $progress[0] += ($batchSentCount + $batchFailedCount);
                $stats['sent'] += $batchSentCount;
                $stats['failed'] += $batchFailedCount;

                foreach ($batchFailedRecipients as $list => $emails) {
                    $stats['failedRecipients'] = $stats['failedRecipients'] + $emails;
                }

                $session->set('mautic.email.send.progress', $progress);
                $session->set('mautic.email.send.stats', $stats);
                $session->set('mautic.email.send.active', false);
            }

            $dataArray['percent'] = ($progress[1]) ? ceil(($progress[0] / $progress[1]) * 100) : 100;

            $dataArray['progress'] = $progress;
            $dataArray['stats']    = $stats;
        }

        return $this->sendJsonResponse($dataArray);
    }

    /**
     * Called by parent::getBuilderTokensAction().
     *
     * @param $query
     *
     * @return array
     */
    protected function getBuilderTokens($query)
    {
        /** @var \Mautic\EmailBundle\Model\EmailModel $model */
        $model = $this->getModel('email');

        return $model->getBuilderComponents(null, ['tokens'], $query, false);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function generatePlaintTextAction(Request $request)
    {
        $custom = $request->request->get('custom');
        $id     = $request->request->get('id');

        $parser = new PlainTextHelper(
            [
                'base_url' => $request->getSchemeAndHttpHost().$request->getBasePath(),
            ]
        );

        $dataArray = [
            'text' => $parser->setHtml($custom)->getText(),
        ];

        return $this->sendJsonResponse($dataArray);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function getAttachmentsSizeAction(Request $request)
    {
        $assets = $request->get('assets', [], true);
        $size   = 0;
        if ($assets) {
            /** @var \Mautic\AssetBundle\Model\AssetModel $assetModel */
            $assetModel = $this->getModel('asset');
            $size       = $assetModel->getTotalFilesize($assets);
        }

        return $this->sendJsonResponse(['size' => $size]);
    }

    /**
     * Tests monitored email connection settings.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    protected function testMonitoredEmailServerConnectionAction(Request $request)
    {
        $dataArray = ['success' => 0, 'message' => ''];

        if ($this->user->isAdmin()) {
            $settings = $request->request->all();

            if (empty($settings['password'])) {
                $existingMonitoredSettings = $this->coreParametersHelper->getParameter('monitored_email');
                if (is_array($existingMonitoredSettings) && (!empty($existingMonitoredSettings[$settings['mailbox']]['password']))) {
                    $settings['password'] = $existingMonitoredSettings[$settings['mailbox']]['password'];
                }
            }

            /** @var \Mautic\EmailBundle\MonitoredEmail\Mailbox $helper */
            $helper = $this->factory->getHelper('mailbox');

            try {
                $helper->setMailboxSettings($settings, false);
                $folders = $helper->getListingFolders('');
                if (!empty($folders)) {
                    $dataArray['folders'] = '';
                    foreach ($folders as $folder) {
                        $dataArray['folders'] .= "<option value=\"$folder\">$folder</option>\n";
                    }
                }
                $dataArray['success'] = 1;
                $dataArray['message'] = $this->translator->trans('mautic.core.success');
            } catch (\Exception $e) {
                $dataArray['message'] = $e->getMessage();
            }
        }

        return $this->sendJsonResponse($dataArray);
    }

    /**
     * Tests mail transport settings.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    protected function testEmailServerConnectionAction(Request $request)
    {
        $dataArray = ['success' => 0, 'message' => '', 'to_address_empty'=>false];
        $user      = $this->get('mautic.helper.user')->getUser();

        if ($user->isAdmin() || $user->isCustomAdmin()) {
            $settings = $request->request->all();

            $transport = $settings['transport'];

            switch ($transport) {
                case 'gmail':
                    $mailer = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
                    break;
                case 'smtp':
                    $mailer = new \Swift_SmtpTransport($settings['host'], $settings['port'], $settings['encryption']);
                    break;
                default:
                    if ($this->container->has($transport)) {
                        $mailer = $this->container->get($transport);

                        if ('mautic.transport.amazon' == $transport) {
                            $mailer->setHost($settings['amazon_region']);
                        }
                    }
            }

            if (method_exists($mailer, 'setMauticFactory')) {
                $mailer->setMauticFactory($this->factory);
            }

            if (!empty($mailer)) {
                try {
                    if (method_exists($mailer, 'setApiKey')) {
                        if (empty($settings['api_key'])) {
                            $settings['api_key'] = $this->get('mautic.helper.core_parameters')->getParameter('mailer_api_key');
                        }
                        $mailer->setApiKey($settings['api_key']);
                    }
                } catch (\Exception $exception) {
                    // Transport had magic method defined and threw an exception
                }

                try {
                    if (is_callable([$mailer, 'setUsername']) && is_callable([$mailer, 'setPassword'])) {
                        if (empty($settings['password'])) {
                            $settings['password'] = $this->get('mautic.helper.core_parameters')->getParameter('mailer_password');
                        }
                        $mailer->setUsername($settings['user']);
                        $mailer->setPassword($settings['password']);
                    }
                } catch (\Exception $exception) {
                    // Transport had magic method defined and threw an exception
                }

                $logger = new \Swift_Plugins_Loggers_ArrayLogger();
                $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

                try {
                    $mailer->start();
                    $translator = $this->get('translator');

                    if ($settings['send_test'] == 'true' || $settings['toemail'] != '') {
                        if ($settings['toemail'] != '') {
                            $trackingcode = $settings['trackingcode'];
                            $mailbody     = $translator->trans('mautic.email.website_tracking.body');
                            $mailbody     = str_replace('|FROM_EMAIL|', $settings['from_email'], nl2br($mailbody));
                            $mailbody     = str_replace('|Tracking|', $trackingcode, nl2br($mailbody));
                            if ($settings['additionalinfo'] != '') {
                                $additioninfo = $settings['additionalinfo'];
                                $mailbody     = str_replace('|USER_CONTENT|', $additioninfo, nl2br($mailbody));
                                //$mailbody .= "$additioninfo<br>";
                            }
                            $mailbody .= '</body></html>';

                            $message = \Swift_Message::newInstance()
                                ->setSubject($translator->trans('mautic.email.config.mailer.transport.tracking_send.subject'));
                            $message->setBody($mailbody, 'text/html');
                        } else {
                            $message = new \Swift_Message(
                                $translator->trans('mautic.email.config.mailer.transport.test_send.subject'),
                                $translator->trans('mautic.email.config.mailer.transport.test_send.body')
                            );
                        }

                        $userFullName = trim($user->getFirstName().' '.$user->getLastName());
                        if (empty($userFullName)) {
                            $userFullName = null;
                        }
                        $message->setFrom([$settings['from_email'] => $settings['from_name']]);
                        if ($settings['toemail'] != '') {
                            $message->setTo([$settings['toemail']]);
                        } else {
                            $message->setTo([$user->getEmail() => $userFullName]);
                        }
                        $mailer->send($message);
                        $dataArray['success'] = 1;
                        $dataArray['message'] = $translator->trans('mautic.core.success');
                    } else {
                        $dataArray['success']         = 0;
                        $dataArray['to_address_empty']=true;
                        $dataArray['message']         = $translator->trans('mautic.core.failed');
                    }
                } catch (\Exception $e) {
                    $dataArray['success'] = 0;
                    $dataArray['message'] = $e->getMessage().'<br />'.$logger->dump();
                }
            }
        }

        return $this->sendJsonResponse($dataArray);
    }

    /**
     * @param Request $request
     */
    protected function getEmailCountStatsAction(Request $request)
    {
        /** @var EmailModel $model */
        $model = $this->getModel('email');

        $data = [];
        if ($id = $request->get('id')) {
            if ($email = $model->getEntity($id)) {
                $pending     = $model->getPendingLeads($email, null, true);
                $queued      = $model->getQueuedCounts($email);
                $sentCount   =$email->getSentCount(true);
                $failureCount= $email->getFailureCount(true);
                $unsubCount  = $email->getUnsubscribeCount(true);
                $bounceCount =$email->getBounceCount(true);
                $totalCount  = $pending + $sentCount;

                $clickCount = $model->getEmailClickCount($email->getId());
                if ($sentCount > 0 || $totalCount > 0) {
                    $totalSentPec = round($sentCount / $totalCount * 100);
                } else {
                    $totalSentPec = 0;
                }
                if ($failureCount > 0 || $totalCount > 0) {
                    $failurePercentage = round($failureCount / $totalCount * 100);
                } else {
                    $failurePercentage = 0;
                }
                if ($unsubCount > 0 || $totalCount > 0) {
                    $unSubPercentage = round($unsubCount / $sentCount * 100);
                } else {
                    $unSubPercentage = 0;
                }
                if ($bounceCount > 0 || $sentCount > 0) {
                    $bouncePercentage = round($bounceCount / $sentCount * 100);
                } else {
                    $bouncePercentage = 0;
                }
                if ($clickCount > 0 || $sentCount > 0) {
                    $clickCountPercentage = round($clickCount / $sentCount * 100);
                } else {
                    $clickCountPercentage = 0;
                    $clickCount           =0;
                }

                $data = [
                    'success' => 1,
                    'pending' => 'list' === $email->getEmailType() && $pending ? $this->translator->trans(
                        'mautic.email.stat.leadcount',
                        ['%count%' => $pending]
                    ) : 0,
                    'queued'           => ($queued) ? $this->translator->trans('mautic.email.stat.queued', ['%count%' => $queued]) : 0,
                    'sentCount'        => $this->translator->trans('mautic.email.stat.sentcount', ['%count%' =>$sentCount, '%percentage%'=>$totalSentPec]),
                    'readCount'        => $this->translator->trans('mautic.email.stat.readcount', ['%count%' => $email->getReadCount(true), '%percentage%' => round($email->getReadPercentage(true))]),
                    'readPercent'      => $this->translator->trans('mautic.email.stat.readpercent', ['%count%' => $clickCount, '%percentage%'=>$clickCountPercentage]),
                    'failureCount'     => $this->translator->trans('mautic.email.stat.failurecount', ['%count%' => $failureCount, '%percentage%'=>$failurePercentage]),
                    'unsubscribeCount' => $this->translator->trans('mautic.email.stat.unsubscribecount', ['%count%' =>$unsubCount, '%percentage%'=>$unSubPercentage]),
                    'bounceCount'      => $this->translator->trans('mautic.email.stat.bouncecount', ['%count%' => $bounceCount, '%percentage%' => $bouncePercentage]),
                ];
            }
        }

        return new JsonResponse($data);
    }
}
