<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Controller;

use Mautic\CoreBundle\Entity\Account;
use Mautic\CoreBundle\Entity\Billing;

/**
 * Class AccountController.
 */
class AccountController extends FormController
{
    /**
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction()
    {
        if (!$this->user->isAdmin() && !$this->user->isCustomAdmin() && $this->coreParametersHelper->getParameter('accountinfo_disabled')) {
            return $this->accessDenied();
        }

        /** @var \Mautic\CoreBundle\Model\AccountInfoModel $model */
        $model         = $this->getModel('core.accountinfo');
        $action        = $this->generateUrl('mautic_accountinfo_action', ['objectAction' => 'edit']);
        $accrepo       = $model->getRepository();
        $accountentity = $accrepo->findAll();
        if (sizeof($accountentity) > 0) {
            $account = $accountentity[0]; //$model->getEntity(1);
        } else {
            $account = new Account();
        }
        $form          = $model->createForm($account, $this->get('form.factory'), $action);
        if ($this->request->getMethod() == 'POST') {
            $isValid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($isValid = $this->isFormValid($form)) {
                    $data           = $this->request->request->get('accountinfo');
                    $accountname    = $data['accountname'];
                    $domainname     = $data['domainname'];
                    $email          = $data['email'];
                    $phonenumber    = $data['phonenumber'];
                    $currencysymbol = $data['currencysymbol'];
                    $timezone       = $data['timezone'];
                    $accountid      = $data['accountid'];
                    if (isset($data['needpoweredby'])) {
                        $needpoweredby = $data['needpoweredby'];
                    } else {
                        $needpoweredby = 1;
                    }
                    $account->setAccountname($accountname);
                    $account->setDomainname($domainname);
                    $account->setEmail($email);
                    $account->setPhonenumber($phonenumber);
                    $account->setCurrencysymbol($currencysymbol);
                    $account->setTimezone($timezone);
                    $account->setAccountid($accountid);
                    $account->getNeedpoweredby($needpoweredby);
                    /** @var \Mautic\CoreBundle\Configurator\Configurator $configurator */
                    $configurator = $this->get('mautic.configurator');
                    $isWritabale  = $configurator->isFileWritable();
                    if ($isWritabale && $timezone != '') {
                        $configurator->mergeParameters(['default_timezone' => $timezone]);
                        $configurator->write();
                    }
                    $model->saveEntity($account);
                }
            }
            if ($cancelled || $isValid) {
                if (!$cancelled && $this->isFormApplied($form)) {
                    return $this->delegateRedirect($this->generateUrl('mautic_accountinfo_action', ['objectAction' => 'edit']));
                } else {
                    return $this->delegateRedirect($this->generateUrl('mautic_dashboard_index'));
                }
            }
        }
        $tmpl = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';

        return $this->delegateView([
            'viewParameters' => [
                'tmpl'               => $tmpl,
                'form'               => $form->createView(),
                'security'           => $this->get('mautic.security'),
                'actionRoute'        => 'mautic_accountinfo_action',
                'typePrefix'         => 'form',
            ],
            'contentTemplate' => 'MauticCoreBundle:AccountInfo:form.html.php',
            'passthroughVars' => [
                'activeLink'    => '#mautic_accountinfo_index',
                'mauticContent' => 'accountinfo',
                'route'         => $this->generateUrl('mautic_accountinfo_action', ['objectAction' => 'edit']),
            ],
        ]);
    }

    /**
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function billingAction()
    {
        if (!$this->user->isAdmin() && !$this->user->isCustomAdmin() && $this->coreParametersHelper->getParameter('accountinfo_disabled')) {
            return $this->accessDenied();
        }

        /** @var \Mautic\CoreBundle\Model\BillingModel $model */
        $model         = $this->getModel('core.billinginfo');
        $action        = $this->generateUrl('mautic_accountinfo_action', ['objectAction' => 'billing']);
        $billingrepo   = $model->getRepository();
        $billingentity = $billingrepo->findAll();
        if (sizeof($billingentity) > 0) {
            $billing = $billingentity[0]; //$model->getEntity(1);
        } else {
            $billing = new Billing();
        }
        $form          = $model->createForm($billing, $this->get('form.factory'), $action, ['isBilling' => true]);
        if ($this->request->getMethod() == 'POST') {
            $isValid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($isValid = $this->isFormValid($form)) {
                    $data               = $this->request->request->get('billinginfo');
                    $companyname        = $data['companyname'];
                    $companyaddressname = $data['companyaddress'];
                    $accountingemail    = $data['accountingemail'];
                    $billing->setCompanyname($companyname);
                    $billing->setCompanyaddress($companyaddressname);
                    $billing->setAccountingemail($accountingemail);
                    $model->saveEntity($billing);
                }
            }
            if ($cancelled || $isValid) {
                if (!$cancelled && $this->isFormApplied($form)) {
                    return $this->delegateRedirect($this->generateUrl('mautic_accountinfo_action', ['objectAction' => 'billing']));
                } else {
                    return $this->delegateRedirect($this->generateUrl('mautic_dashboard_index'));
                }
            }
        }
        $tmpl = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';

        return $this->delegateView([
            'viewParameters' => [
                'tmpl'               => $tmpl,
                'form'               => $form->createView(),
                'security'           => $this->get('mautic.security'),
                'actionRoute'        => 'mautic_accountinfo_action',
                'typePrefix'         => 'form',
            ],
            'contentTemplate' => 'MauticCoreBundle:AccountInfo:billing.html.php',
            'passthroughVars' => [
                'activeLink'    => '#mautic_accountinfo_index',
                'mauticContent' => 'accountinfo',
                'route'         => $this->generateUrl('mautic_accountinfo_action', ['objectAction' => 'billing']),
            ],
        ]);
    }

    /**
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function paymentAction()
    {
        if (!$this->user->isAdmin() && !$this->user->isCustomAdmin() && $this->coreParametersHelper->getParameter('accountinfo_disabled')) {
            return $this->accessDenied();
        }

        $tmpl               = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';
        $paymentrepository  =$this->get('le.subscription.repository.payment');
        $paymentalias       =$paymentrepository->getTableAlias();
//        $filter = [
//            'force'  => [
//                ['column' => $paymentalias.'.orderid', 'expr' => 'eq', 'value' => $orderid],
//            ],
//        ];
        $args= [
         //   'filter'         => $filter,
            'orderBy'        => $paymentalias.'.id',
            'orderByDir'     => 'ASC',
         //   'ignore_paginator' => true,
        ];
        $payments=$paymentrepository->getEntities($args);

        return $this->delegateView([
            'viewParameters' => [
                'tmpl'               => $tmpl,
                'security'           => $this->get('mautic.security'),
                'actionRoute'        => 'mautic_accountinfo_action',
                'typePrefix'         => 'form',
                'payments'           => $payments,
            ],
            'contentTemplate' => 'MauticCoreBundle:AccountInfo:payment.html.php',
            'passthroughVars' => [
                'activeLink'    => '#mautic_accountinfo_index',
                'mauticContent' => 'accountinfo',
                'route'         => $this->generateUrl('mautic_accountinfo_action', ['objectAction' => 'payment']),
            ],
        ]);
    }

    /**
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function cancelAction()
    {
        if (!$this->user->isAdmin() && !$this->user->isCustomAdmin() && $this->coreParametersHelper->getParameter('accountinfo_disabled')) {
            return $this->accessDenied();
        }

        /** @var \Mautic\CoreBundle\Model\BillingModel $model */
        $model   = $this->getModel('core.billinginfo');
        $action  = $this->generateUrl('mautic_accountinfo_action', ['objectAction' => 'cancel']);
        $billing = $model->getEntity(1);
        //$form = $model->createForm($billing, $this->get('form.factory'), $action);
        $form = '';
        if ($this->request->getMethod() == 'POST') {
            $isValid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($isValid = $this->isFormValid($form)) {
                    $data               = $this->request->request->get('billinginfo');
                    $companyname        = $data['companyname'];
                    $companyaddressname = $data['companyaddress'];
                    $accountingemail    = $data['accountingemail'];
                    $billing->setCompanyname($companyname);
                    $billing->setCompanyaddress($companyaddressname);
                    $billing->setAccountingemail($accountingemail);
                    $model->saveEntity($billing);
                }
            }
            if ($cancelled || $isValid) {
                if (!$cancelled && $this->isFormApplied($form)) {
                    return $this->delegateRedirect($this->generateUrl('mautic_accountinfo_action', ['objectAction' => 'cancel']));
                } else {
                    return $this->delegateRedirect($this->generateUrl('mautic_dashboard_index'));
                }
            }
        }
        $tmpl = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';

        return $this->delegateView([
            'viewParameters' => [
                'tmpl'               => $tmpl,
                'security'           => $this->get('mautic.security'),
                'actionRoute'        => 'mautic_accountinfo_action',
                'typePrefix'         => 'form',
            ],
            'contentTemplate' => 'MauticCoreBundle:AccountInfo:cancel.html.php',
            'passthroughVars' => [
                'activeLink'    => '#mautic_accountinfo_index',
                'mauticContent' => 'accountinfo',
                'route'         => $this->generateUrl('mautic_accountinfo_action', ['objectAction' => 'cancel']),
            ],
        ]);
    }
}
