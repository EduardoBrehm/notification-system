<?php

declare(strict_types=1);

namespace NotificationSystemTest\Validator;

use NotificationSystem\DTO\CreateNotificationDTO;
use NotificationSystem\Exception\NotificationValidationException;
use NotificationSystem\Validator\NotificationValidator;
use PHPUnit\Framework\TestCase;

class NotificationValidatorTest extends TestCase
{
    private NotificationValidator $validator;
    private array $config;

    protected function setUp(): void
    {
        $this->config = [
            'notification_system' => [
                'notification_types' => [
                    'info' => ['icon' => 'info'],
                    'success' => ['icon' => 'success'],
                ],
            ],
        ];

        $this->validator = new NotificationValidator($this->config);
    }

    public function testValidateCreateDTOSuccess(): void
    {
        $dto = new CreateNotificationDTO(
            'info',
            'Test message',
            'test_type'
        );

        $this->validator->validateCreateDTO($dto);
        $this->assertTrue(true); // If we get here, no exception was thrown
    }

    public function testValidateCreateDTOInvalidType(): void
    {
        $dto = new CreateNotificationDTO(
            'invalid',
            'Test message',
            'test_type'
        );

        $this->expectException(NotificationValidationException::class);
        $this->expectExceptionMessage('Invalid notification type');
        
        $this->validator->validateCreateDTO($dto);
    }

    public function testValidateCreateDTOEmptyMessage(): void
    {
        $dto = new CreateNotificationDTO(
            'info',
            '',
            'test_type'
        );

        $this->expectException(NotificationValidationException::class);
        $this->expectExceptionMessage('Message cannot be empty');
        
        $this->validator->validateCreateDTO($dto);
    }

    public function testValidateCreateDTOMessageTooLong(): void
    {
        $dto = new CreateNotificationDTO(
            'info',
            str_repeat('a', 1001),
            'test_type'
        );

        $this->expectException(NotificationValidationException::class);
        $this->expectExceptionMessage('Message length cannot exceed 1000 characters');
        
        $this->validator->validateCreateDTO($dto);
    }

    public function testValidateCreateDTOEmptyTypeMessage(): void
    {
        $dto = new CreateNotificationDTO(
            'info',
            'Test message',
            ''
        );

        $this->expectException(NotificationValidationException::class);
        $this->expectExceptionMessage('Type message cannot be empty');
        
        $this->validator->validateCreateDTO($dto);
    }
}
