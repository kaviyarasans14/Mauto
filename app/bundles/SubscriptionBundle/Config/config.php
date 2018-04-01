<?php

return [
    'routes' => [
        'main' => [
            'le_subscription_index' => [
                'path'       => '/subscription',
                'controller' => 'MauticSubscriptionBundle:Subscription:index',
            ],
            'le_plan_index' => [
                'path'       => '/plans',
                'controller' => 'MauticSubscriptionBundle:Subscription:indexplan',
            ],
            'le_subscription_status' => [
                'path'       => '/subscription-status',
                'controller' => 'MauticSubscriptionBundle:Subscription:subscriptionstatus',
            ],
            'le_payment_status' => [
                'path'       => '/payment-status',
                'controller' => 'MauticSubscriptionBundle:Subscription:paymentstatus',
            ],
            'mautic_kyc_action' => [
                'path'       => '/kyc/{objectAction}/{objectId}',
                'controller' => 'MauticSubscriptionBundle:KYC:execute',
            ],
        ],
        'public' => [],
        'api'    => [],
    ],
    'menu' => [
        'main'    => [],
        'admin'   => [],
        'extra'   => [],
        'profile' => [],
    ],
    'services' => [
        'events'  => [],
        'forms'   => [
            'mautic.form.type.kycinfo' => [
                'class'     => 'Mautic\SubscriptionBundle\Form\Type\KYCType',
                'alias'     => 'kycinfo',
                'arguments' => [
                    'mautic.report.model.report',
                    'translator',
                    'mautic.helper.language',
                    'mautic.helper.core_parameters',
                ],
            ],
        ],
        'helpers' => [],
        'menus'   => [],
        'other'   => [
            'le.core.repository.subscription' => [
                'class'     => 'Mautic\SubscriptionBundle\Entity\SubscriptionRepository',
                'arguments' => [
                    'doctrine.orm.commondb_entity_manager', 'mautic.email.repository.licenseinfo',
                ],
            ],
            'le.core.repository.signup' => [
                'class'     => 'Mautic\SubscriptionBundle\Entity\SignupRepository',
                'arguments' => [
                    'doctrine.orm.signupdb_entity_manager',
                ],
            ],
            'le.helper.payment' => [
                'class'     => \Mautic\SubscriptionBundle\Helper\PaymentHelper::class,
                'arguments' => [
                    'mautic.factory',
                ],
            ],
            'le.subscription.repository.payment' => [
                'class'     => \Doctrine\ORM\EntityRepository::class,
                'factory'   => ['@doctrine.orm.entity_manager', 'getRepository'],
                'arguments' => [
                    \Mautic\SubscriptionBundle\Entity\PaymentHistory::class,
                ],
            ],
        ],
        'models'    => [
            'mautic.subscription.model.kycinfo' => [
                'class' => 'Mautic\SubscriptionBundle\Model\KYCModel',
            ],
            'mautic.subscription.model.userpreference' => [
                'class' => 'Mautic\SubscriptionBundle\Model\UserPreferenceModel',
            ],
        ],
        'validator' => [],
    ],
    'parameters' => [
        'razoparpay_apikey'                => '',
        'razoparpay_apisecret'             => '',
        'paypal_clientid'                  => '',
        'paypal_clientsecret'              => '',
        'paypal_mode'                      => 'sandbox', //live
        'paypal_rootpath'                  => '%kernel.root_dir%/paypal',
        'paypal_logpath'                   => '%kernel.root_dir%/paypal/log',
        'paypal_cachepath'                 => '%kernel.root_dir%/paypal/cache', // for determining paypal cache directory
        'paypal_loglevel'                  => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
        'paypal_log_enabled'               => true,
    ],
];
