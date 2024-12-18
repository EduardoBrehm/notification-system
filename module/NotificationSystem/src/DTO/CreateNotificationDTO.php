<?php

declare(strict_types=1);

namespace NotificationSystem\DTO;

use DateTime;

final class CreateNotificationDTO
{
    public function __construct(
        private readonly string $type,
        private readonly string $message,
        private readonly string $typeMessage,
        private readonly ?int $relationId = null,
        private readonly ?string $userId = null,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTypeMessage(): string
    {
        return $this->typeMessage;
    }

    public function getRelationId(): ?int
    {
        return $this->relationId;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }
}
