<?php

return [
    'routes' => [
        'main' => [
            'le_subscription_index' => [
                'path'       => '/subscription',
                'controller' => 'MauticSubscriptionBundle:Subscription:index',
            ],
            'le_subscription_status' => [
                'path'       => '/subscription-status',
                'controller' => 'MauticSubscriptionBundle:Subscription:status',
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
        'forms'   => [],
        'helpers' => [],
        'menus'   => [],
        'other'   => [
            'le.core.repository.subscription' => [
                'class'     => 'Mautic\SubscriptionBundle\Entity\SubscriptionRepository',
                'arguments' => [
                    'doctrine.orm.commondb_entity_manager',
                ],
            ],
        ],
        'models'    => [],
        'validator' => [],
    ],
    'parameters' => [
        'razoparpay_apikey'                => '',
        'razoparpay_apisecret'             => '',
        'paypal_clientid'                  => '',
        'paypal_clientsecret'              => '',
        'paypal_mode'                      => 'sandbox',
        'paypal_rootpath'                  => '%kernel.root_dir%/paypal',
        'paypal_logpath'                   => '%kernel.root_dir%/paypal/log',
        'paypal_cachepath'                 => '%kernel.root_dir%/paypal/cache', // for determining paypal cache directory
        'paypal_loglevel'                  => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
        'paypal_log_enabled'               => true,
    ],
];
