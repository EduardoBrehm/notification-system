<?php

declare(strict_types=1);

namespace NotificationSystem\Validator;

use NotificationSystem\DTO\CreateNotificationDTO;
use NotificationSystem\Exception\NotificationValidationException;

class NotificationValidator
{
    private const MAX_MESSAGE_LENGTH = 1000;
    private array $validTypes;

    public function __construct(array $config)
    {
        $this->validTypes = array_keys($config['notification_system']['notification_types'] ?? []);
    }

    public function validateCreateDTO(CreateNotificationDTO $dto): void
    {
        $errors = [];

        if (!in_array($dto->getType(), $this->validTypes)) {
            $errors[] = sprintf(
                'Invalid notification type. Valid types are: %s',
                implode(', ', $this->validTypes)
            );
        }

        if (empty($dto->getMessage())) {
            $errors[] = 'Message cannot be empty';
        }

        if (mb_strlen($dto->getMessage()) > self::MAX_MESSAGE_LENGTH) {
            $errors[] = sprintf(
                'Message length cannot exceed %d characters',
                self::MAX_MESSAGE_LENGTH
            );
        }

        if (empty($dto->getTypeMessage())) {
            $errors[] = 'Type message cannot be empty';
        }

        if (!empty($errors)) {
            throw new NotificationValidationException($errors);
        }
    }
}
