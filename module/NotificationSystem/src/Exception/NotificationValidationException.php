<?php

declare(strict_types=1);

namespace NotificationSystem\Exception;

use RuntimeException;

class NotificationValidationException extends RuntimeException
{
    private array $errors;

    public function __construct(array $errors)
    {
        parent::__construct(implode(', ', $errors));
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
