<?php

namespace NotificationSystem\Factory\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use NotificationSystem\Service\NotificationService;
use NotificationSystem\Repository\NotificationRepository;

class NotificationServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): NotificationService
    {
        $config = $container->get('config');
        $repository = $container->get(NotificationRepository::class);

        return new NotificationService($repository, $config);
    }
}
