<?php

namespace NotificationSystem\Service;

use DateTime;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerAwareInterface;
use NotificationSystem\Entity\Notification;
use NotificationSystem\Repository\NotificationRepository;

class NotificationService implements EventManagerAwareInterface
{
    private EventManagerInterface $eventManager;
    private array $config;

    public function __construct(
        private NotificationRepository $notificationRepository,
        array $config
    ) {
        $this->config = $config['notification_system'] ?? [];
        $this->eventManager = new EventManager();
    }

    public function setEventManager(EventManagerInterface $eventManager): void
    {
        $this->eventManager = $eventManager;
    }

    public function getEventManager(): EventManagerInterface
    {
        return $this->eventManager;
    }

    public function createNotification(
        string $type,
        string $message,
        string $typeMessage,
        ?int $relationId = null,
        ?string $userId = null
    ): Notification {
        $notification = new Notification();
        $notification
            ->setType($type)
            ->setMessage($message)
            ->setTypeMessage($typeMessage)
            ->setRelationId($relationId)
            ->setUserId($userId);

        $this->notificationRepository->save($notification);

        $this->getEventManager()->trigger('notification.created', $this, [
            'notification' => $notification
        ]);

        return $notification;
    }

    public function markAsRead(int $id, ?string $userId = null): bool
    {
        $notification = $this->notificationRepository->find($id);
        
        if (!$notification) {
            return false;
        }

        if ($userId && $notification->getUserId() !== $userId) {
            return false;
        }

        $notification->setIsRead(true);
        $this->notificationRepository->save($notification);

        $this->getEventManager()->trigger('notification.read', $this, [
            'notification' => $notification
        ]);

        return true;
    }

    public function getUnreadCount(?string $userId = null): int
    {
        return $this->notificationRepository->countUnread($userId);
    }

    public function getNotifications(
        ?string $userId = null,
        bool $onlyUnread = false,
        int $limit = 10,
        int $offset = 0
    ): array {
        return $this->notificationRepository->findBy([
            'user_id' => $userId,
            'is_read' => $onlyUnread ? false : null,
        ], ['created_at' => 'DESC'], $limit, $offset);
    }

    public function getNotificationTypes(): array
    {
        return $this->config['notification_types'] ?? [];
    }

    public function cleanOldNotifications(int $daysOld = 30): int
    {
        $date = new DateTime();
        $date->modify("-{$daysOld} days");
        return $this->notificationRepository->deleteOlderThan($date);
    }
}
