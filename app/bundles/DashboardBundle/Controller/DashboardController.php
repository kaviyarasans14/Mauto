<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\DashboardBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\DashboardBundle\Entity\Widget;
use Mautic\SubscriptionBundle\Entity\Account;
use Mautic\SubscriptionBundle\Entity\Billing;
use Mautic\SubscriptionBundle\Entity\UserPreference;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DashboardController.
 */
class DashboardController extends FormController
{
    /**
     * Generates the default view.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $videoarg     = $this->request->get('login');
        $loginsession = $this->get('session');
        $loginarg     = $loginsession->get('isLogin');
        $dbhost       = $this->coreParametersHelper->getParameter('db_host');
        $showsetup    = false;
        $billformview = '';
        $accformview  = '';
        $userformview = '';
        if ($dbhost != 'localhost' && $loginarg) {
            /** @var \Mautic\UserBundle\Model\UserModel $usermodel */
            $usermodel     = $this->getModel('user.user');
            $userentity    = $usermodel->getCurrentUserEntity();

            $userform = $usermodel->createForm($userentity, $this->get('form.factory'));

            /** @var \Mautic\SubscriptionBundle\Model\BillingModel $billingmodel */
            $billingmodel  = $this->getModel('subscription.billinginfo');
            $billingrepo   = $billingmodel->getRepository();
            $billingentity = $billingrepo->findAll();
            if (sizeof($billingentity) > 0) {
                $billing = $billingentity[0]; //$model->getEntity(1);
            } else {
                $showsetup = true;
                $billing   = new Billing();
            }
            $countryname = $this->getCountryName();
            $timezone    = '';
            if ($countryname == 'India') {
                $timezone = 'Asia/Kolkata';
                $billing->setCountry($countryname);
            }
            $repository  =$this->get('le.core.repository.subscription');
            $signupinfo  =$repository->getSignupInfo($userentity->getEmail());
            if (!empty($signupinfo)) {
                $billing->setCompanyname($signupinfo[0]['f2']);
                $billing->setAccountingemail($userentity->getEmail());
            }

            $billform = $billingmodel->createForm($billing, $this->get('form.factory'), [], ['isBilling' => false]);

            /** @var \Mautic\SubscriptionBundle\Model\AccountInfoModel $model */
            $model         = $this->getModel('subscription.accountinfo');
            $accrepo       = $model->getRepository();
            $accountentity = $accrepo->findAll();
            if (sizeof($accountentity) > 0) {
                $account = $accountentity[0]; //$model->getEntity(1);
                if (!$account->getMobileverified()) {
                    $showsetup = true;
                }
            } else {
                $showsetup = true;
                $account   = new Account();
            }
            if (!empty($signupinfo)) {
                $account->setPhonenumber($signupinfo[0]['f11']);
            }
            if ($timezone != '') {
                $account->setTimezone($timezone);
            }
            $accform = $model->createForm($account, $this->get('form.factory'));
        }
        /** @var \Mautic\DashboardBundle\Model\DashboardModel $model */
        $model   = $this->getModel('dashboard');
        $widgets = $model->getWidgets();
        //$loginsession->set('isLogin', false);

        // Apply the default dashboard if no widget exists
        if (!count($widgets) && $this->user->getId()) {
            return $this->applyDashboardFileAction('global.leadsengagecustom');
        }

        $humanFormat     = 'M j, Y';
        $mysqlFormat     = 'Y-m-d';
        $action          = $this->generateUrl('mautic_dashboard_index');
        $dateRangeFilter = $this->request->get('daterange', []);

        // Set new date range to the session
        if ($this->request->isMethod('POST')) {
            $session = $this->get('session');
            if (!empty($dateRangeFilter['date_from'])) {
                $from = new \DateTime($dateRangeFilter['date_from']);
                $session->set('mautic.dashboard.date.from', $from->format($mysqlFormat));
            }

            if (!empty($dateRangeFilter['date_to'])) {
                $to = new \DateTime($dateRangeFilter['date_to']);
                $session->set('mautic.dashboard.date.to', $to->format($mysqlFormat));
            }

            $model->clearDashboardCache();
        }

        // Load date range from session
        $filter = $model->getDefaultFilter();

        // Set the final date range to the form
        $dateRangeFilter['date_from'] = $filter['dateFrom']->format($humanFormat);
        $dateRangeFilter['date_to']   = $filter['dateTo']->format($humanFormat);
        $dateRangeForm                = $this->get('form.factory')->create('daterange', $dateRangeFilter, ['action' => $action]);

        $model->populateWidgetsContent($widgets, $filter);

        $usermodel  =$this->getModel('user.user');
        $currentuser= $usermodel->getCurrentUserEntity();
        if ($videoarg == 'CloseVideo') {
            $loginsession->set('CloseVideo', true);

            return $this->redirect($this->generateUrl('mautic_dashboard_index'));
        }
        $close     = $loginsession->get('CloseVideo');

        /** @var \Mautic\SubscriptionBundle\Model\UserPreferenceModel $userprefmodel */
        $userprefmodel  = $this->getModel('subscription.userpreference');
        if ($videoarg == 'dont_show_again') {
            $userprefentity = new UserPreference();
            $userprefentity->setProperty('Dont Show Video again');
            $userprefentity->setUserid($currentuser->getId());
            $userprefmodel->saveEntity($userprefentity);
            //$this->addFlash('Video will be available in Help.');
            return $this->redirect($this->generateUrl('mautic_dashboard_index'));
        }
        $userprefrepo   = $userprefmodel->getRepository();
        $userprefentity = $userprefrepo->findOneBy(['userid' => $currentuser->getId()]);
        $videoURL       = ''; //$this->coreParametersHelper->getParameter('video_url');
        $repository     = $this->get('le.core.repository.subscription');
        $videoconfig    = $repository->getVideoURL();
        if (!empty($videoconfig)) {
            $videoURL = $videoconfig[0]['video_url'];
        }
        $showvideo      = false;
        if ($userprefentity == null && $close == '') {
            $showvideo = true;
        }
        $ismobile = $this->isMobile();
        if ($showsetup) {
            $billformview = $billform->createView();
            $accformview  = $accform->createView();
            $userformview = $userform->createView();
        } else {
            $loginsession->set('isLogin', false);
        }

        return $this->delegateView([
            'viewParameters' => [
                'security'             => $this->get('mautic.security'),
                'widgets'              => $widgets,
                'dateRangeForm'        => $dateRangeForm->createView(),
                'notifymessage'        => $this->getLicenseNotifyMessage(),
                'showvideo'            => $showvideo,
                'videoURL'             => $videoURL,
                'route'                => $this->generateUrl('le_plan_index'),
                'showsetup'            => $showsetup,
                'billingform'          => $billformview,
                'accountform'          => $accformview,
                'userform'             => $userformview,
                'isMobile'             => $ismobile,
            ],
            'contentTemplate' => 'MauticDashboardBundle:Dashboard:index.html.php',
            'passthroughVars' => [
                'activeLink'    => '#mautic_dashboard_index',
                'mauticContent' => 'dashboard',
                'route'         => $this->generateUrl('mautic_dashboard_index'),
            ],
        ]);
    }

    public function getLicenseNotifyMessage()
    {
        $licenseRemDays       = $this->get('mautic.helper.licenseinfo')->getLicenseRemainingDays();
        $licenseRemDate       = $this->get('mautic.helper.licenseinfo')->getLicenseEndDate();
        $emailUsageCount      = $this->get('mautic.helper.licenseinfo')->getTotalEmailUsage();
        $bounceUsageCount     = $this->get('mautic.helper.licenseinfo')->getEmailBounceUsageCount();
        $totalRecordUsage     = $this->get('mautic.helper.licenseinfo')->getTotalRecordUsage();
        $emailValidityEndDate = $this->get('mautic.helper.licenseinfo')->getEmailValidityEndDate();
        $emailCountExpired    = $this->get('mautic.helper.licenseinfo')->emailCountExpired();
        $emailValidity        = $this->get('mautic.helper.licenseinfo')->getEmailValidityDays();

        $emailUssage    = false;
        $bouceUsage     = false;
        $emailsValidity = false;
        $recordUsage    = false;
        $buyCreditRoute =$this->generateUrl('le_plan_index');

        $notifymessage   ='';
        $usageMsg        ='';
        $maxEmailUsage   = 85;
        $maxBounceUsage  =3;
        $maxEmailValidity=7;
        $maxRecordUsage  =85;
        $buyNowButon     = 'Buy Now';

        if (!empty($licenseRemDays)) {
            if ($licenseRemDays < 7) {
                $notifymessage = $this->translator->trans('leadsengage.msg.license.expired', ['%licenseRemDate%' => $licenseRemDate]);
            }
        }
        if (isset($emailUsageCount) && $emailUsageCount > $maxEmailUsage) {
            $emailUssage=true;
        }
        if (isset($bounceUsageCount) && $bounceUsageCount > $maxBounceUsage) {
            $bouceUsage=true;
        }
        if (isset($emailValidity) && $emailValidity != 'UL' && $emailValidity < $maxEmailValidity) {
            $emailsValidity=true;
        }
        if (isset($totalRecordUsage) && $totalRecordUsage > $maxRecordUsage) {
            $recordUsage=true;
        }

        if ($emailUssage) {
            if ($emailCountExpired == 0) {
                $usageMsg=$this->translator->trans('le.emailusage.count.expired');
            } else {
                $usageMsg=$this->translator->trans('le.emailusage.count.exceeds', ['%maxEmailUsage%' => $maxEmailUsage]);
            }
        }
        if ($emailsValidity) {
            if ($emailValidity == 0) {
                $emailMsg=$this->translator->trans('le.emailvalidity.count.expired');
            } else {
                $emailMsg=$this->translator->trans('le.emailvalidity.count.exceeds', ['%emailValidityEndDate%' => $emailValidityEndDate]);
            }
            if ($usageMsg != '') {
                $usageMsg .= ' and ';
            }
            $usageMsg .= $emailMsg;
        }

        if ($emailUssage || $emailsValidity) {
            $usageMsg .= $this->translator->trans('le.buyNow.button', ['%buyNow%' => $buyNowButon, '%url%' => $buyCreditRoute]);
        }
        if ($recordUsage) {
            $usageMsg .= $this->translator->trans('le.record.count.expired', ['%maxRecordUsage%' => $maxRecordUsage]);
        }
        if ($bouceUsage) {
            $usageMsg .= $this->translator->trans('le.bounce.count.exceeds', ['%bounceUsageCount%' => $bounceUsageCount]);
        }

        if ($usageMsg != '') {
            $notifymessage .= ' '.$usageMsg;
        }

        return $notifymessage;
    }

    /**
     * Generate's new dashboard widget and processes post data.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        //retrieve the entity
        $widget = new Widget();

        $model  = $this->getModel('dashboard');
        $action = $this->generateUrl('mautic_dashboard_action', ['objectAction' => 'new']);

        //get the user form factory
        $form       = $model->createForm($widget, $this->get('form.factory'), $action);
        $closeModal = false;
        $valid      = false;

        ///Check for a submitted form and process it
        if ($this->request->getMethod() == 'POST') {
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $closeModal = true;

                    //form is valid so process the data
                    $model->saveEntity($widget);
                }
            } else {
                $closeModal = true;
            }
        }

        if ($closeModal) {
            //just close the modal
            $passthroughVars = [
                'closeModal'    => 1,
                'mauticContent' => 'widget',
            ];

            $filter = $model->getDefaultFilter();
            $model->populateWidgetContent($widget, $filter);

            if ($valid && !$cancelled) {
                $passthroughVars['upWidgetCount'] = 1;
                $passthroughVars['widgetHtml']    = $this->renderView('MauticDashboardBundle:Widget:detail.html.php', [
                    'widget' => $widget,
                ]);
                $passthroughVars['widgetId']     = $widget->getId();
                $passthroughVars['widgetWidth']  = $widget->getWidth();
                $passthroughVars['widgetHeight'] = $widget->getHeight();
            }

            $response = new JsonResponse($passthroughVars);

            return $response;
        } else {
            return $this->delegateView([
                'viewParameters' => [
                    'form' => $form->createView(),
                ],
                'contentTemplate' => 'MauticDashboardBundle:Widget:form.html.php',
            ]);
        }
    }

    /**
     * edit widget and processes post data.
     *
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($objectId)
    {
        $model  = $this->getModel('dashboard');
        $widget = $model->getEntity($objectId);
        $action = $this->generateUrl('mautic_dashboard_action', ['objectAction' => 'edit', 'objectId' => $objectId]);

        //get the user form factory
        $form       = $model->createForm($widget, $this->get('form.factory'), $action);
        $closeModal = false;
        $valid      = false;
        ///Check for a submitted form and process it
        if ($this->request->getMethod() == 'POST') {
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $closeModal = true;

                    //form is valid so process the data
                    $model->saveEntity($widget);
                }
            } else {
                $closeModal = true;
            }
        }

        if ($closeModal) {
            //just close the modal
            $passthroughVars = [
                'closeModal'    => 1,
                'mauticContent' => 'widget',
            ];

            $filter = $model->getDefaultFilter();
            $model->populateWidgetContent($widget, $filter);

            if ($valid && !$cancelled) {
                $passthroughVars['upWidgetCount'] = 1;
                $passthroughVars['widgetHtml']    = $this->renderView('MauticDashboardBundle:Widget:detail.html.php', [
                    'widget' => $widget,
                ]);
                $passthroughVars['widgetId']     = $widget->getId();
                $passthroughVars['widgetWidth']  = $widget->getWidth();
                $passthroughVars['widgetHeight'] = $widget->getHeight();
            }

            $response = new JsonResponse($passthroughVars);

            return $response;
        } else {
            return $this->delegateView([
                'viewParameters' => [
                    'form' => $form->createView(),
                ],
                'contentTemplate' => 'MauticDashboardBundle:Widget:form.html.php',
            ]);
        }
    }

    /**
     * Deletes the entity.
     *
     * @param int $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($objectId)
    {
        $returnUrl = $this->generateUrl('mautic_dashboard_index');
        $success   = 0;
        $flashes   = [];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'contentTemplate' => 'MauticDashboardBundle:Dashboard:index',
            'passthroughVars' => [
                'activeLink'    => '#mautic_dashboard_index',
                'success'       => $success,
                'mauticContent' => 'dashboard',
            ],
        ];

        /** @var \Mautic\DashboardBundle\Model\DashboardModel $model */
        $model  = $this->getModel('dashboard');
        $entity = $model->getEntity($objectId);
        if ($entity === null) {
            $flashes[] = [
                'type'    => 'error',
                'msg'     => 'mautic.api.client.error.notfound',
                'msgVars' => ['%id%' => $objectId],
            ];
        } else {
            $model->deleteEntity($entity);
            $name      = $entity->getName();
            $flashes[] = [
                'type'    => 'notice',
                'msg'     => 'mautic.core.notice.deleted',
                'msgVars' => [
                    '%name%' => $name,
                    '%id%'   => $objectId,
                ],
            ];
        }

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                [
                    'flashes' => $flashes,
                ]
            )
        );
    }

    /**
     * Exports the widgets of current user into a json file.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function exportAction()
    {
        /** @var \Mautic\DashboardBundle\Model\DashboardModel $model */
        $model            = $this->getModel('dashboard');
        $widgetsPaginator = $model->getWidgets();
        $usersName        = $this->user->getName();
        $dateTime         = new \DateTime();
        $dateStamp        = $dateTime->format('Y-m-d H:i:s');
        $name             = $this->request->get(
            'name',
            'dashboard-of-'.str_replace(' ', '-', $usersName).'-'.$dateStamp
        );

        $description = $this->get('translator')->trans(
            'mautic.dashboard.generated_by',
            [
                '%name%' => $usersName,
                '%date%' => $dateStamp,
            ]
        );

        $dashboard = [
            'name'        => $name,
            'description' => $description,
            'widgets'     => [],
        ];

        foreach ($widgetsPaginator as $widget) {
            $dashboard['widgets'][] = [
                'name'     => $widget->getName(),
                'width'    => $widget->getWidth(),
                'height'   => $widget->getHeight(),
                'ordering' => $widget->getOrdering(),
                'type'     => $widget->getType(),
                'params'   => $widget->getParams(),
                'template' => $widget->getTemplate(),
            ];
        }

        // Make the filename safe
        $filename = InputHelper::alphanum($name, false, '_').'.json';

        if ($this->request->get('save', false)) {
            // Save to the user's folder
            $dir = $this->factory->getSystemPath('dashboard.user');
            file_put_contents($dir.'/'.$filename, json_encode($dashboard));

            return $this->redirect($this->get('router')->generate('mautic_dashboard_action', ['objectAction' => 'import']));
        }

        $response = new JsonResponse($dashboard);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $response->headers->set('Expires', 0);
        $response->headers->set('Cache-Control', 'must-revalidate');
        $response->headers->set('Pragma', 'public');

        return $response;
    }

    /**
     * Exports the widgets of current user into a json file.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteDashboardFileAction()
    {
        $file = $this->request->get('file');

        $parts = explode('.', $file);
        $type  = array_shift($parts);
        $name  = implode('.', $parts);

        $dir  = $this->factory->getSystemPath("dashboard.$type");
        $path = $dir.'/'.$name.'.json';

        if (file_exists($path) && is_writable($path)) {
            unlink($path);
        }

        return $this->redirect($this->generateUrl('mautic_dashboard_action', ['objectAction' => 'import']));
    }

    /**
     * Applies dashboard layout.
     *
     * @param null $file
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function applyDashboardFileAction($file = null)
    {
        if (!$file) {
            $file = $this->request->get('file');
        }

        $parts = explode('.', $file);
        $type  = array_shift($parts);
        $name  = implode('.', $parts);

        $dir  = $this->factory->getSystemPath("dashboard.$type");
        $path = $dir.'/'.$name.'.json';

        if (file_exists($path) && is_writable($path)) {
            $widgets = json_decode(file_get_contents($path), true);
            if (isset($widgets['widgets'])) {
                $widgets = $widgets['widgets'];
            }

            if ($widgets) {
                /** @var \Mautic\DashboardBundle\Model\DashboardModel $model */
                $model = $this->getModel('dashboard');

                $model->clearDashboardCache();

                $currentWidgets = $model->getWidgets();

                if (count($currentWidgets)) {
                    foreach ($currentWidgets as $widget) {
                        $model->deleteEntity($widget);
                    }
                }

                $filter = $model->getDefaultFilter();
                foreach ($widgets as $widget) {
                    $widget = $model->populateWidgetEntity($widget, $filter);
                    $model->saveEntity($widget);
                }

                return $this->redirect($this->get('router')->generate('mautic_dashboard_index'));
            }
        }

        return $this->redirect($this->generateUrl('mautic_dashboard_action', ['objectAction' => 'import']));
    }

    /**
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function importAction()
    {
        $preview = $this->request->get('preview');

        /** @var \Mautic\DashboardBundle\Model\DashboardModel $model */
        $model = $this->getModel('dashboard');

        $directories = [
            'user'   => $this->factory->getSystemPath('dashboard.user'),
            'global' => $this->factory->getSystemPath('dashboard.global'),
        ];

        $action = $this->generateUrl('mautic_dashboard_action', ['objectAction' => 'import']);
        $form   = $this->get('form.factory')->create('dashboard_upload', [], ['action' => $action]);

        if ($this->request->getMethod() == 'POST') {
            if (isset($form) && !$cancelled = $this->isFormCancelled($form)) {
                if ($this->isFormValid($form)) {
                    $fileData = $form['file']->getData();
                    if (!empty($fileData)) {
                        $extension = pathinfo($fileData->getClientOriginalName(), PATHINFO_EXTENSION);
                        if ($extension === 'json') {
                            $fileData->move($directories['user'], $fileData->getClientOriginalName());
                        } else {
                            $form->addError(
                                new FormError(
                                    $this->translator->trans('mautic.core.not.allowed.file.extension', ['%extension%' => $extension], 'validators')
                                )
                            );
                        }
                    } else {
                        $form->addError(
                            new FormError(
                                $this->translator->trans('mautic.dashboard.upload.filenotfound', [], 'validators')
                            )
                        );
                    }
                }
            }
        }

        $dashboardFiles = [];
        $dashboards     = [];

        // User specific layouts
        chdir($directories['user']);
        $dashboardFiles['user'] = glob('*.json');

        // Global dashboards
        chdir($directories['global']);
        $dashboardFiles['global'] = glob('*.json');

        foreach ($dashboardFiles as $type => $dirDashboardFiles) {
            $tempDashboard = [];
            foreach ($dirDashboardFiles as $dashId => $dashboard) {
                $dashboard = str_replace('.json', '', $dashboard);
                $config    = json_decode(
                    file_get_contents($directories[$type].'/'.$dirDashboardFiles[$dashId]),
                    true
                );

                // Check for name, description, etc
                $tempDashboard[$dashboard] = [
                    'type'        => $type,
                    'name'        => (isset($config['name'])) ? $config['name'] : $dashboard,
                    'description' => (isset($config['description'])) ? $config['description'] : '',
                    'widgets'     => (isset($config['widgets'])) ? $config['widgets'] : $config,
                ];
            }

            // Sort by name
            uasort($tempDashboard,
                function ($a, $b) {
                    return strnatcasecmp($a['name'], $b['name']);
                }
            );

            $dashboards = array_merge(
                $dashboards,
                $tempDashboard
            );
        }

        if ($preview && isset($dashboards[$preview])) {
            // @todo check is_writable
            $widgets = $dashboards[$preview]['widgets'];
            $filter  = $model->getDefaultFilter();
            $model->populateWidgetsContent($widgets, $filter);
        } else {
            $widgets = [];
        }

        return $this->delegateView(
            [
                'viewParameters' => [
                    'form'       => $form->createView(),
                    'dashboards' => $dashboards,
                    'widgets'    => $widgets,
                    'preview'    => $preview,
                ],
                'contentTemplate' => 'MauticDashboardBundle:Dashboard:import.html.php',
                'passthroughVars' => [
                    'activeLink'    => '#mautic_dashboard_index',
                    'mauticContent' => 'dashboardImport',
                    'route'         => $this->generateUrl(
                        'mautic_dashboard_action',
                        [
                            'objectAction' => 'import',
                        ]
                    ),
                ],
            ]
        );
    }

    public function getCountryName()
    {
        $clientip        = $this->request->getClientIp();
        $dataArray       = json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip='.$clientip));
        $countrycode     =$dataArray->{'geoplugin_countryName'};

        return $countrycode;
    }

    public function isMobile()
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER['HTTP_USER_AGENT']);
    }
}
