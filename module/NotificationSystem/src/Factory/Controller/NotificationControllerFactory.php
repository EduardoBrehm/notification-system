<?php

namespace NotificationSystem\Factory\Controller;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use NotificationSystem\Controller\NotificationController;
use NotificationSystem\Service\NotificationService;

class NotificationControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): NotificationController
    {
        $notificationService = $container->get(NotificationService::class);
        return new NotificationController($notificationService);
    }
}
