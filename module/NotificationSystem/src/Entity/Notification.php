<?php

namespace NotificationSystem\Entity;

use DateTime;
use JsonSerializable;

class Notification implements JsonSerializable
{
    private ?int $id = null;
    private string $type;
    private string $message;
    private string $typeMessage;
    private ?int $relationId;
    private bool $isRead = false;
    private ?DateTime $createdAt = null;
    private ?DateTime $readAt = null;
    private ?string $userId = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getTypeMessage(): string
    {
        return $this->typeMessage;
    }

    public function setTypeMessage(string $typeMessage): self
    {
        $this->typeMessage = $typeMessage;
        return $this;
    }

    public function getRelationId(): ?int
    {
        return $this->relationId;
    }

    public function setRelationId(?int $relationId): self
    {
        $this->relationId = $relationId;
        return $this;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;
        if ($isRead) {
            $this->readAt = new DateTime();
        }
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getReadAt(): ?DateTime
    {
        return $this->readAt;
    }

    public function setReadAt(?DateTime $readAt): self
    {
        $this->readAt = $readAt;
        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'message' => $this->message,
            'typeMessage' => $this->typeMessage,
            'relationId' => $this->relationId,
            'isRead' => $this->isRead,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'readAt' => $this->readAt?->format('Y-m-d H:i:s'),
            'userId' => $this->userId,
        ];
    }
}
