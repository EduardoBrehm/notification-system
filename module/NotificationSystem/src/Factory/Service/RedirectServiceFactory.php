<?php

namespace NotificationSystem\Factory\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use NotificationSystem\Service\RedirectService;

class RedirectServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RedirectService
    {
        $router = $container->get('Router');
        $config = $container->get('config');

        return new RedirectService($router, $config);
    }
}
