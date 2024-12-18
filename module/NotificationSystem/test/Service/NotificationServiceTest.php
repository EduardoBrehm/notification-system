<?php

declare(strict_types=1);

namespace NotificationSystemTest\Service;

use DateTime;
use Laminas\EventManager\EventManager;
use NotificationSystem\DTO\CreateNotificationDTO;
use NotificationSystem\Entity\Notification;
use NotificationSystem\Exception\NotificationNotFoundException;
use NotificationSystem\Exception\NotificationValidationException;
use NotificationSystem\Repository\NotificationRepository;
use NotificationSystem\Service\NotificationCacheService;
use NotificationSystem\Service\NotificationService;
use NotificationSystem\Validator\NotificationValidator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class NotificationServiceTest extends TestCase
{
    private NotificationService $service;
    private NotificationRepository $repository;
    private NotificationValidator $validator;
    private NotificationCacheService $cache;
    private LoggerInterface $logger;
    private array $config;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(NotificationRepository::class);
        $this->validator = $this->createMock(NotificationValidator::class);
        $this->cache = $this->createMock(NotificationCacheService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->config = [
            'notification_system' => [
                'notification_types' => [
                    'info' => ['icon' => 'info'],
                    'success' => ['icon' => 'success'],
                ],
            ],
        ];

        $this->service = new NotificationService(
            $this->repository,
            $this->validator,
            $this->cache,
            $this->logger,
            $this->config
        );
    }

    public function testCreateNotificationSuccess(): void
    {
        $dto = new CreateNotificationDTO(
            'success',
            'Test message',
            'test_type',
            1,
            'user1'
        );

        $this->validator->expects($this->once())
            ->method('validateCreateDTO')
            ->with($dto);

        $expectedNotification = new Notification();
        $expectedNotification->setType('success')
            ->setMessage('Test message')
            ->setTypeMessage('test_type')
            ->setRelationId(1)
            ->setUserId('user1');

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($notification) {
                return $notification instanceof Notification
                    && $notification->getType() === 'success'
                    && $notification->getMessage() === 'Test message';
            }));

        $this->cache->expects($this->once())
            ->method('setCachedNotification');

        $this->cache->expects($this->once())
            ->method('invalidateUnreadCount')
            ->with('user1');

        $notification = $this->service->createNotification($dto);

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals('success', $notification->getType());
        $this->assertEquals('Test message', $notification->getMessage());
    }

    public function testCreateNotificationValidationError(): void
    {
        $dto = new CreateNotificationDTO(
            'invalid',
            '',
            'test_type'
        );

        $this->validator->expects($this->once())
            ->method('validateCreateDTO')
            ->with($dto)
            ->willThrowException(new NotificationValidationException(['Invalid type']));

        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                'Failed to create notification',
                $this->arrayHasKey('error')
            );

        $this->expectException(NotificationValidationException::class);
        $this->service->createNotification($dto);
    }

    public function testMarkAsReadSuccess(): void
    {
        $notification = new Notification();
        $notification->setId(1)
            ->setUserId('user1')
            ->setIsRead(false);

        $this->cache->expects($this->once())
            ->method('getCachedNotification')
            ->with(1)
            ->willReturn(null);

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($notification);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($notification) {
                return $notification->isRead() === true;
            }));

        $result = $this->service->markAsRead(1, 'user1');
        $this->assertTrue($result);
    }

    public function testMarkAsReadNotificationNotFound(): void
    {
        $this->cache->expects($this->once())
            ->method('getCachedNotification')
            ->with(1)
            ->willReturn(null);

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->expectException(NotificationNotFoundException::class);
        $this->service->markAsRead(1);
    }

    public function testGetUnreadCountWithCache(): void
    {
        $userId = 'user1';
        $expectedCount = 5;

        $this->cache->expects($this->once())
            ->method('getCachedUnreadCount')
            ->with($userId)
            ->willReturn($expectedCount);

        $this->repository->expects($this->never())
            ->method('countUnread');

        $count = $this->service->getUnreadCount($userId);
        $this->assertEquals($expectedCount, $count);
    }

    public function testGetUnreadCountWithoutCache(): void
    {
        $userId = 'user1';
        $expectedCount = 5;

        $this->cache->expects($this->once())
            ->method('getCachedUnreadCount')
            ->with($userId)
            ->willReturn(null);

        $this->repository->expects($this->once())
            ->method('countUnread')
            ->with($userId)
            ->willReturn($expectedCount);

        $this->cache->expects($this->once())
            ->method('setCachedUnreadCount')
            ->with($userId, $expectedCount);

        $count = $this->service->getUnreadCount($userId);
        $this->assertEquals($expectedCount, $count);
    }
}
