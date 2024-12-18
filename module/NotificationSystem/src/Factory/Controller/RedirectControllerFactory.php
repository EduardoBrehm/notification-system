<?php

namespace NotificationSystem\Factory\Controller;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use NotificationSystem\Controller\RedirectController;
use NotificationSystem\Service\NotificationService;
use NotificationSystem\Service\RedirectService;

class RedirectControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RedirectController
    {
        $notificationService = $container->get(NotificationService::class);
        $redirectService = $container->get(RedirectService::class);
        
        return new RedirectController($notificationService, $redirectService);
    }
}
