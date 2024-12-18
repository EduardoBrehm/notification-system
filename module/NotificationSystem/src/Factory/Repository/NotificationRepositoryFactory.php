<?php

namespace NotificationSystem\Factory\Repository;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use NotificationSystem\Repository\NotificationRepository;

class NotificationRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): NotificationRepository
    {
        $adapter = $container->get('Laminas\Db\Adapter\Adapter');
        return new NotificationRepository($adapter);
    }
}
