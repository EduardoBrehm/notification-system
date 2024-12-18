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

return [
    'router' => [
        'routes' => [
            'notification' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/notification[/:action[/:id]]',
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
                'class' => 'danger',
            ],
        ],
    ],
];
