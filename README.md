# Laminas Notification System

A sophisticated notification and redirection system built as a reusable Laminas module. This system provides a flexible and extensible way to handle notifications and intelligent redirections in your Laminas applications.

## Features

- Flexible notification system with support for different types (info, success, warning, error)
- Intelligent redirection system with configurable redirect maps
- Event-driven architecture for extensibility
- RESTful API endpoints for notification management
- Support for user-specific notifications
- Built-in support for read/unread status tracking
- Automatic cleanup of old notifications
- AJAX and regular HTTP support
- Fully configurable through module configuration
- Robust validation with DTO pattern
- Built-in caching support for improved performance
- Comprehensive error handling and logging
- 100% unit test coverage

## Requirements

- PHP 8.3 or later
- Laminas MVC Framework 3.6 or later
- PSR-3 compatible logger
- Composer for dependency management

## Installation

1. Install via composer:
```bash
composer require eduardokum/notification-system
```

2. Enable the module in your `config/modules.config.php`:
```php
return [
    // ... other modules
    'NotificationSystem',
];
```

3. Copy the provided configuration to your `config/autoload/` directory and modify as needed.

## Usage

### Creating Notifications

```php
// In your controller or service
$notificationService = $container->get(NotificationService::class);

// Create a DTO for the notification
$notificationDto = new CreateNotificationDTO(
    'success',                    // type
    'Your action was successful', // message
    'user_profile_update',       // type_message
    123,                         // relation_id (optional)
    'user123'                    // user_id (optional)
);

try {
    $notification = $notificationService->createNotification($notificationDto);
} catch (NotificationValidationException $e) {
    // Handle validation errors
    $errors = $e->getErrors();
} catch (\Exception $e) {
    // Handle other errors
}
```

### Configuring Redirects

In your module config or local config:

```php
return [
    'notification_system' => [
        'notification_types' => [
            'info' => [
                'icon' => 'fas fa-info-circle',
                'class' => 'info',
            ],
            'success' => [
                'icon' => 'fas fa-check-circle',
                'class' => 'success',
            ],
            'warning' => [
                'icon' => 'fas fa-exclamation-triangle',
                'class' => 'warning',
            ],
            'error' => [
                'icon' => 'fas fa-times-circle',
                'class' => 'error',
            ],
        ],
        'redirect_map' => [
            'user_profile_update' => [
                'route' => 'user/profile',
                'params' => [
                    'id' => 'relation_id',
                ],
            ],
        ],
    ],
];
```

### Handling Notifications in Frontend

```javascript
// Example using jQuery
$.get('/notification', { user_id: 'user123', unread: true }, function(response) {
    if (response.success) {
        response.data.notifications.forEach(function(notification) {
            // Handle each notification
        });
    }
}).fail(function(error) {
    // Handle errors
    console.error('Failed to fetch notifications:', error);
});
```

## Advanced Features

### Caching

The system includes built-in caching support for improved performance:
- Caches unread notification counts
- Caches frequently accessed notifications
- Automatic cache invalidation on updates

```php
// Cached unread count
$unreadCount = $notificationService->getUnreadCount('user123');

// Mark as read (automatically invalidates relevant caches)
$notificationService->markAsRead($notificationId, 'user123');
```

### Validation

The system includes comprehensive validation:
- Type validation against configured notification types
- Message length and content validation
- Required field validation
- Custom validation rules can be added

### Error Handling

Robust error handling with specific exceptions:
- `NotificationValidationException`: For validation errors
- `NotificationNotFoundException`: When a notification cannot be found
- Proper error logging with PSR-3 logger

## Documentation

Complete technical documentation is available in both English and Portuguese:

### English Documentation
- [English Documentation (Markdown)](docs/documentation_en.md)
- [English Documentation (PDF)](docs/Laminas_Notification_System.pdf)

### Portuguese Documentation
- [Documentação em Português (Markdown)](docs/documentacao_pt_BR.md)
- [Documentação em Português (PDF)](docs/Sistema_de_Notificacoes_Laminas.pdf)

## Testing

The module comes with comprehensive unit tests. To run the tests:

```bash
composer test
```

For code coverage report:

```bash
composer test-coverage
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Write your changes with tests
4. Run `composer check` to ensure all tests pass
5. Submit a Pull Request

## License

MIT License. See [LICENSE.md](LICENSE.md) for details.
