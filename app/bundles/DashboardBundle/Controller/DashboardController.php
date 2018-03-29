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
use Mautic\CoreBundle\Entity\Account;
use Mautic\CoreBundle\Entity\Billing;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\DashboardBundle\Entity\Widget;
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

            /** @var \Mautic\CoreBundle\Model\BillingModel $billingmodel */
            $billingmodel  = $this->getModel('core.billinginfo');
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
            }
            $billing->setCountry($countryname);
            $repository  =$this->get('le.core.repository.subscription');
            $signupinfo  =$repository->getSignupInfo($userentity->getEmail());
            if (!empty($signupinfo)) {
                $billing->setCompanyname($signupinfo[0]['f2']);
                $billing->setAccountingemail($userentity->getEmail());
            }

            $billform = $billingmodel->createForm($billing, $this->get('form.factory'), [], ['isBilling' => false]);

            /** @var \Mautic\CoreBundle\Model\AccountInfoModel $model */
            $model         = $this->getModel('core.accountinfo');
            $accrepo       = $model->getRepository();
            $accountentity = $accrepo->findAll();
            if (sizeof($accountentity) > 0) {
                $account = $accountentity[0]; //$model->getEntity(1);
            } else {
                $account = new Account();
            }
            if (!empty($signupinfo)) {
                $account->setPhonenumber($signupinfo[0]['f11']);
            }
            $account->setTimezone($timezone);
            $accform = $model->createForm($account, $this->get('form.factory'));
        }
        /** @var \Mautic\DashboardBundle\Model\DashboardModel $model */
        $model   = $this->getModel('dashboard');
        $widgets = $model->getWidgets();
        $loginsession->set('isLogin', false);

        $licenseRemDays       = $this->get('mautic.helper.licenseinfo')->getLicenseRemainingDays();
        $licenseRemDate       = $this->get('mautic.helper.licenseinfo')->getLicenseEndDate();
        $emailUsageCount      = $this->get('mautic.helper.licenseinfo')->getTotalEmailUsage();
        $bounceUsageCount     = $this->get('mautic.helper.licenseinfo')->getEmailBounceUsageCount();
        $totalRecordUsage     = $this->get('mautic.helper.licenseinfo')->getTotalRecordUsage();
        $emailValidityEndDate = $this->get('mautic.helper.licenseinfo')->getEmailValidityEndDate();
        $emailCountExpired    = $this->get('mautic.helper.licenseinfo')->emailCountExpired();
        $emailValidity        = $this->get('mautic.helper.licenseinfo')->getEmailValidityDays();

        $emailUssage= false;
        $bouceUsage = false;
        $emailsValidity= false;
        $recordUsage= false;
        $buyCreditRoute =$this->generateUrl('le_plan_index');
        $message="";
        $usageMsg="";
        if(!empty($licenseRemDays)) {
            if($licenseRemDays < 7) {
                $message = $this->translator->trans('leadsengage.msg.license.expired', ['%licenseRemDate%' => $licenseRemDate]);
            }
        }
         if (isset($emailUsageCount) && $emailUsageCount > 85) {
             $emailUssage=true;
         }
        if (isset($bounceUsageCount) && $bounceUsageCount >3) {
            $bouceUsage=true;
        }
        if (isset($emailValidity) && $emailValidity < 7) {
            $emailsValidity=true;
        }
        if (isset($totalRecordUsage) && $totalRecordUsage > 85) {
            $recordUsage=true;
        }

          if ($emailUssage && $bouceUsage && $emailsValidity && $recordUsage){
            if($emailCountExpired == 0 && $emailValidity == 0){
                $usageMsg = $this->translator->trans('leadsengage.email.bounce.validity.record.expired.iszero', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%' =>$buyCreditRoute]);
            } else if($emailCountExpired == 0){
                $usageMsg = $this->translator->trans('leadsengage.email.bounce.record.email.iszero', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%' =>$buyCreditRoute]);
            } else if($emailValidity == 0){
                $usageMsg = $this->translator->trans('leadsengage.email.bounce.validity.iszero', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%' =>$buyCreditRoute]);
            } else {
                $usageMsg = $this->translator->trans('leadsengage.email.bounce.validity.record.expired', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%' =>$buyCreditRoute]);
            }
         } elseif ($emailUssage && $bouceUsage && $emailsValidity){
              if($emailCountExpired == 0 && $emailValidity == 0){
                  $usageMsg = $this->translator->trans('le.email.bounce.validity.iszero.expired', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
              } else if($emailCountExpired == 0){
                  $usageMsg = $this->translator->trans('le.email.bounce.validity.email.iszero.expired', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
              } else if($emailValidity == 0){
                  $usageMsg = $this->translator->trans('le.email.bounce.validity.validity.iszero', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
              } else {
                  $usageMsg = $this->translator->trans('leadsengage.email.bounce.validity.expired', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
              }
          } elseif ($bouceUsage && $emailsValidity && $recordUsage){
             if($emailValidity == 0){
                 $usageMsg = $this->translator->trans('le.bounce.validity.record.iszero', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
             } else {
                 $usageMsg = $this->translator->trans('leadsengage.bounce.validity.record.expired', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
             }
          } elseif ($recordUsage && $emailUssage && $bouceUsage){
              if($emailCountExpired == 0){
                  $usageMsg = $this->translator->trans('le.record.email.bounce.expired', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
              } else {
                  $usageMsg = $this->translator->trans('leadsengage.record.email.bounce.expired', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
              }
          } elseif ($emailsValidity && $recordUsage && $emailUssage){
              if($emailCountExpired == 0 && $emailValidity == 0){
                  $usageMsg = $this->translator->trans('le.record.validity.email.iszero', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate]);
              } else if($emailCountExpired == 0){
                  $usageMsg = $this->translator->trans('le.record.validity.email.expired.iszero', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate]);
              } else if($emailValidity == 0){
                  $usageMsg = $this->translator->trans('le.record.validity.email.val.zero', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate]);
              } else {
                  $usageMsg = $this->translator->trans('leadsengage.record.validity.email.expired', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate]);
              }
          } elseif ($emailUssage && $bouceUsage) {
              if($emailCountExpired == 0){
                  $usageMsg = $this->translator->trans('le.bounce.email.usage.exceeds.zero', ['%bounceUsageCount%' => $bounceUsageCount, '%url%'=> $buyCreditRoute]);
              } else {
                  $usageMsg = $this->translator->trans('leadsengage.bounce.email.usage.exceeds', ['%bounceUsageCount%' => $bounceUsageCount, '%url%'=> $buyCreditRoute]);
              }
          } elseif ($bouceUsage && $emailsValidity){
              if($emailValidity == 0){
                  $usageMsg = $this->translator->trans('le.bounce.validity.expired.zero', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%' =>$buyCreditRoute]);
              } else{
                  $usageMsg = $this->translator->trans('leadsengage.bounce.validity.expired', ['%bounceUsageCount%' => $bounceUsageCount, '%emailValidityEndDate%' => $emailValidityEndDate, '%url%' =>$buyCreditRoute]);
              }
          } elseif ($emailsValidity && $recordUsage) {
              if($emailValidity == 0){
                  $usageMsg = $this->translator->trans('le.record.validity.expired.iszero', ['%emailValidityEndDate%' => $emailValidityEndDate, '%url%' =>$buyCreditRoute]);
              } else{
                  $usageMsg = $this->translator->trans('leadsengage.record.validity.expired', ['%emailValidityEndDate%' => $emailValidityEndDate, '%url%' =>$buyCreditRoute]);
              }
          } elseif ($emailUssage && $recordUsage) {
              if($emailCountExpired == 0){
                  $usageMsg = $this->translator->trans('le.email.record.expired', ['%url%' =>$buyCreditRoute]);
              } else {
                  $usageMsg = $this->translator->trans('leadsengage.email.record.expired', ['%url%' =>$buyCreditRoute]);
              }
          } elseif ($bouceUsage && $recordUsage) {
              $usageMsg = $this->translator->trans('leadsengage.bounce.record.expired', ['%bounceUsageCount%' => $bounceUsageCount]);
          } elseif ($emailUssage && $emailsValidity) {
              if($emailCountExpired == 0 && $emailValidity == 0){
                  $usageMsg = $this->translator->trans('le.email.validity.exceeds.iszero', ['%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
              } else if($emailCountExpired == 0){
                  $usageMsg = $this->translator->trans('le.email.validity.email.zero', ['%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
              } else if($emailValidity == 0){
                  $usageMsg = $this->translator->trans('le.email.validity.validity.zero', ['%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
              } else {
                  $usageMsg = $this->translator->trans('leadsengage.email.validity.exceeds', ['%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=>$buyCreditRoute]);
              }
          } elseif ($emailUssage) {
              if($emailCountExpired == 0 ){
                  $usageMsg = $this->translator->trans('le.email.usage.iszero', ['%url%'=> $buyCreditRoute]);
              } else {
                  $usageMsg = $this->translator->trans('leadsengage.email.usage.exceeds', ['%url%'=> $buyCreditRoute]);
              }
          } elseif ($bouceUsage) {
              $usageMsg =$this->translator->trans('leadsengage.bounce.usage.exceeds', ['%bounceUsageCount%' => $bounceUsageCount]);
          } elseif ($emailsValidity) {
              if($emailValidity == 0){
                  $usageMsg =$this->translator->trans('le.email.validity.expired.iszero', ['%url%'=> $buyCreditRoute]);
              }else {
                  $usageMsg =$this->translator->trans('leadsengage.email.validity.expired', ['%emailValidityEndDate%' => $emailValidityEndDate, '%url%'=> $buyCreditRoute]);
              }
          } elseif ($recordUsage) {
              $usageMsg =$this->translator->trans('leadsengage.record.usage.exceeds', ['%licenseRemDate%' => $licenseRemDate, '%url%'=> $buyCreditRoute]);
          }


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

        /** @var \Mautic\SubscriptionBundle\Model\UserPreferenceModel $userprefmodel */
        $userprefmodel  = $this->getModel('subscription.userpreference');
        $userprefrepo   = $userprefmodel->getRepository();
        $userprefentity = $userprefrepo->findOneBy(['userid' => $currentuser->getId()]);
        $videoURL       = ''; //$this->coreParametersHelper->getParameter('video_url');
        $repository     = $this->get('le.core.repository.subscription');
        $videoconfig    = $repository->getVideoURL();
        if (!empty($videoconfig)) {
            $videoURL = $videoconfig[0]['video_url'];
        }
        $showvideo      = 0;
        //if ($userprefentity == null && $loginarg) {
        //    $showvideo = 1;
        //}
        if ($videoarg == 'dont_show_again') {
            $userprefentity = new UserPreference();
            $userprefentity->setProperty('Dont Show Video again');
            $userprefentity->setUserid($currentuser->getId());
            $userprefmodel->saveEntity($userprefentity);
        }
        if ($showsetup) {
            $billformview = $billform->createView();
            $accformview  = $accform->createView();
            $userformview = $userform->createView();
        }

        return $this->delegateView([
            'viewParameters' => [
                'security'            => $this->get('mautic.security'),
                'widgets'             => $widgets,
                'dateRangeForm'       => $dateRangeForm->createView(),
                'message'             => $message,
                'usageMsg'            => $usageMsg,
                'showvideo'           => $showvideo,
                'videoURL'            => $videoURL,
<<<<<<< Updated upstream
                'emailValidityEndDate'=> $emailValidityEndDate,
                'route'               => $this->generateUrl('le_plan_index'),
                'showsetup'           => $showsetup,
                'billingform'         => $billformview,
                'accountform'         => $accformview,
                'userform'            => $userformview,
=======
>>>>>>> Stashed changes
            ],
            'contentTemplate' => 'MauticDashboardBundle:Dashboard:index.html.php',
            'passthroughVars' => [
                'activeLink'    => '#mautic_dashboard_index',
                'mauticContent' => 'dashboard',
                'route'         => $this->generateUrl('mautic_dashboard_index'),
            ],
        ]);
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
}
