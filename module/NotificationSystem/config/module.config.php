<?php

namespace NotificationSystem;

use Laminas\Router\Http\Segment;
use NotificationSystem\Controller\NotificationController;
use NotificationSystem\Controller\RedirectController;
use NotificationSystem\Factory\Controller\NotificationControllerFactory;
use NotificationSystem\Factory\Controller\RedirectControllerFactory;
use NotificationSystem\Factory\Repository\NotificationRepositoryFactory;
use NotificationSystem\Factory\Service\NotificationServiceFactory;
use NotificationSystem\Factory\Service\RedirectServiceFactory;
use NotificationSystem\Repository\NotificationRepository;
use NotificationSystem\Service\NotificationService;
use NotificationSystem\Service\RedirectService;
use NotificationSystem\Validator\NotificationValidator;
use NotificationSystem\Service\NotificationCacheService;
use Laminas\Cache\Storage\Adapter\Memory;

return [
    'router' => [
        'routes' => [
            'notification' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/notification[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => NotificationController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'notification-redirect' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/notification/redirect[/:id]',
                    'defaults' => [
                        'controller' => RedirectController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            NotificationController::class => NotificationControllerFactory::class,
            RedirectController::class => RedirectControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            NotificationService::class => NotificationServiceFactory::class,
            RedirectService::class => RedirectServiceFactory::class,
            NotificationRepository::class => NotificationRepositoryFactory::class,
            NotificationValidator::class => function($container) {
                return new NotificationValidator($container->get('config'));
            },
            NotificationCacheService::class => function($container) {
                $cache = new Memory();
                return new NotificationCacheService($cache);
            },
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'notification_system' => [
        'redirect_map' => [
            // Default redirect map configuration
            'default' => [
                'route' => 'home',
                'params' => [],
            ],
            // Example configuration for contract termination
            'contract_termination' => [
                'route' => 'contract/termination',
                'params' => [
                    'termination_id' => 'relation_id',
                ],
            ],
        ],
        'notification_types' => [
            'info' => [
                'icon' => 'fas fa-info-circle',
                'class' => 'info',
            ],
            'success' => [
                'icon' => 'fas fa-check-circle',
                'class' => 'success',
            ],
            'warning' => [
                'icon' => 'fas fa-exclamation-triangle',
                'class' => 'warning',
            ],
            'error' => [
                'icon' => 'fas fa-times-circle',
                'class' => 'error',
            ],
        ],
    ],
];
