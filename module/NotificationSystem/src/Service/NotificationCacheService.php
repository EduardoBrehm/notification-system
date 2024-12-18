<?php

declare(strict_types=1);

namespace NotificationSystem\Service;

use Laminas\Cache\Storage\StorageInterface;
use NotificationSystem\Entity\Notification;

class NotificationCacheService
{
    private const TTL = 3600; // 1 hour
    private const UNREAD_COUNT_PREFIX = 'unread_count_';
    private const NOTIFICATION_PREFIX = 'notification_';

    public function __construct(
        private StorageInterface $cache
    ) {}

    public function getCachedUnreadCount(string $userId): ?int
    {
        $key = $this->getUnreadCountKey($userId);
        $result = $this->cache->getItem($key);
        
        return $result !== null ? (int) $result : null;
    }

    public function setCachedUnreadCount(string $userId, int $count): void
    {
        $key = $this->getUnreadCountKey($userId);
        $this->cache->setItem($key, $count);
    }

    public function getCachedNotification(int $id): ?Notification
    {
        $key = $this->getNotificationKey($id);
        return $this->cache->getItem($key);
    }

    public function setCachedNotification(Notification $notification): void
    {
        if ($notification->getId() === null) {
            return;
        }

        $key = $this->getNotificationKey($notification->getId());
        $this->cache->setItem($key, $notification);
    }

    public function invalidateUnreadCount(string $userId): void
    {
        $key = $this->getUnreadCountKey($userId);
        $this->cache->removeItem($key);
    }

    public function invalidateNotification(int $id): void
    {
        $key = $this->getNotificationKey($id);
        $this->cache->removeItem($key);
    }

    private function getUnreadCountKey(string $userId): string
    {
        return self::UNREAD_COUNT_PREFIX . $userId;
    }

    private function getNotificationKey(int $id): string
    {
        return self::NOTIFICATION_PREFIX . $id;
    }
}
