<?php

declare(strict_types=1);

namespace NotificationSystemTest\Service;

use Laminas\Cache\Storage\StorageInterface;
use NotificationSystem\Entity\Notification;
use NotificationSystem\Service\NotificationCacheService;
use PHPUnit\Framework\TestCase;

class NotificationCacheServiceTest extends TestCase
{
    private NotificationCacheService $service;
    private StorageInterface $cache;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(StorageInterface::class);
        $this->service = new NotificationCacheService($this->cache);
    }

    public function testGetCachedUnreadCount(): void
    {
        $userId = 'user1';
        $expectedCount = 5;

        $this->cache->expects($this->once())
            ->method('getItem')
            ->with('unread_count_user1')
            ->willReturn($expectedCount);

        $result = $this->service->getCachedUnreadCount($userId);
        $this->assertEquals($expectedCount, $result);
    }

    public function testSetCachedUnreadCount(): void
    {
        $userId = 'user1';
        $count = 5;

        $this->cache->expects($this->once())
            ->method('setItem')
            ->with('unread_count_user1', $count);

        $this->service->setCachedUnreadCount($userId, $count);
    }

    public function testGetCachedNotification(): void
    {
        $id = 1;
        $notification = new Notification();

        $this->cache->expects($this->once())
            ->method('getItem')
            ->with('notification_1')
            ->willReturn($notification);

        $result = $this->service->getCachedNotification($id);
        $this->assertSame($notification, $result);
    }

    public function testSetCachedNotification(): void
    {
        $notification = new Notification();
        $notification->setId(1);

        $this->cache->expects($this->once())
            ->method('setItem')
            ->with('notification_1', $notification);

        $this->service->setCachedNotification($notification);
    }

    public function testSetCachedNotificationWithoutId(): void
    {
        $notification = new Notification();

        $this->cache->expects($this->never())
            ->method('setItem');

        $this->service->setCachedNotification($notification);
    }

    public function testInvalidateUnreadCount(): void
    {
        $userId = 'user1';

        $this->cache->expects($this->once())
            ->method('removeItem')
            ->with('unread_count_user1');

        $this->service->invalidateUnreadCount($userId);
    }

    public function testInvalidateNotification(): void
    {
        $id = 1;

        $this->cache->expects($this->once())
            ->method('removeItem')
            ->with('notification_1');

        $this->service->invalidateNotification($id);
    }
}
