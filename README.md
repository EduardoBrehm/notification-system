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

## Installation

1. Install via composer:
```bash
composer require laminas/notification-system
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

$notification = $notificationService->createNotification(
    'success',                    // type
    'Your action was successful', // message
    'user_profile_update',       // type_message (for redirect mapping)
    123,                         // relation_id (optional)
    'user123'                    // user_id (optional)
);
```

### Configuring Redirects

In your module config or local config:

```php
return [
    'notification_system' => [
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
});
```

## Configuration Options

### Notification Types

You can configure notification types with their associated icons and CSS classes:

```php
'notification_system' => [
    'notification_types' => [
        'info' => [
            'icon' => 'fas fa-info-circle',
            'class' => 'info',
        ],
        // ... other types
    ],
],
```

### Redirect Mapping

Configure how different notification types should redirect:

```php
'notification_system' => [
    'redirect_map' => [
        'contract_termination' => [
            'route' => 'contract/termination',
            'params' => [
                'termination_id' => 'relation_id',
            ],
        ],
    ],
],
```

## API Endpoints

- `GET /notification` - List notifications
- `POST /notification` - Create new notification
- `POST /notification/{id}/read` - Mark notification as read
- `GET /notification/types` - Get available notification types
- `GET /notification/redirect/{id}` - Handle notification redirect

## Events

The module triggers the following events:

- `notification.created` - When a new notification is created
- `notification.read` - When a notification is marked as read

## License

MIT License - see the LICENSE file for details.
