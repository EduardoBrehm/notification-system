<?php

declare(strict_types=1);

namespace NotificationSystem\Exception;

use RuntimeException;

class NotificationNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Notification with ID %d not found', $id));
    }
}
