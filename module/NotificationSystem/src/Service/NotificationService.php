<?php

namespace NotificationSystem\Service;

use DateTime;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerAwareInterface;
use NotificationSystem\DTO\CreateNotificationDTO;
use NotificationSystem\Entity\Notification;
use NotificationSystem\Exception\NotificationNotFoundException;
use NotificationSystem\Repository\NotificationRepository;
use NotificationSystem\Service\NotificationCacheService;
use NotificationSystem\Validator\NotificationValidator;
use Psr\Log\LoggerInterface;

class NotificationService implements EventManagerAwareInterface
{
    private EventManagerInterface $eventManager;
    private array $config;

    public function __construct(
        private NotificationRepository $notificationRepository,
        private NotificationValidator $validator,
        private NotificationCacheService $cache,
        private LoggerInterface $logger,
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

    public function createNotification(CreateNotificationDTO $dto): Notification
    {
        try {
            $this->validator->validateCreateDTO($dto);

            $notification = new Notification();
            $notification
                ->setType($dto->getType())
                ->setMessage($dto->getMessage())
                ->setTypeMessage($dto->getTypeMessage())
                ->setRelationId($dto->getRelationId())
                ->setUserId($dto->getUserId());

            $this->notificationRepository->save($notification);
            $this->cache->setCachedNotification($notification);

            if ($dto->getUserId()) {
                $this->cache->invalidateUnreadCount($dto->getUserId());
            }

            $this->getEventManager()->trigger('notification.created', $this, [
                'notification' => $notification
            ]);

            return $notification;
        } catch (\Exception $e) {
            $this->logger->error('Failed to create notification', [
                'error' => $e->getMessage(),
                'dto' => $dto,
            ]);
            throw $e;
        }
    }

    public function markAsRead(int $id, ?string $userId = null): bool
    {
        try {
            $notification = $this->cache->getCachedNotification($id) 
                ?? $this->notificationRepository->find($id);
            
            if (!$notification) {
                throw new NotificationNotFoundException($id);
            }

            if ($userId && $notification->getUserId() !== $userId) {
                return false;
            }

            $notification->setIsRead(true);
            $this->notificationRepository->save($notification);
            $this->cache->setCachedNotification($notification);

            if ($notification->getUserId()) {
                $this->cache->invalidateUnreadCount($notification->getUserId());
            }

            $this->getEventManager()->trigger('notification.read', $this, [
                'notification' => $notification
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to mark notification as read', [
                'error' => $e->getMessage(),
                'id' => $id,
                'userId' => $userId,
            ]);
            throw $e;
        }
    }

    public function getUnreadCount(?string $userId = null): int
    {
        if (!$userId) {
            return $this->notificationRepository->countUnread(null);
        }

        try {
            $cachedCount = $this->cache->getCachedUnreadCount($userId);
            if ($cachedCount !== null) {
                return $cachedCount;
            }

            $count = $this->notificationRepository->countUnread($userId);
            $this->cache->setCachedUnreadCount($userId, $count);

            return $count;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get unread count', [
                'error' => $e->getMessage(),
                'userId' => $userId,
            ]);
            throw $e;
        }
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
