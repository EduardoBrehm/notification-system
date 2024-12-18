<?php

namespace NotificationSystem\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use NotificationSystem\Service\NotificationService;

class NotificationController extends AbstractActionController
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function indexAction()
    {
        $userId = $this->params()->fromQuery('user_id');
        $onlyUnread = (bool) $this->params()->fromQuery('unread', false);
        $limit = (int) $this->params()->fromQuery('limit', 10);
        $offset = (int) $this->params()->fromQuery('offset', 0);

        $notifications = $this->notificationService->getNotifications(
            $userId,
            $onlyUnread,
            $limit,
            $offset
        );

        return new JsonModel([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'unread_count' => $this->notificationService->getUnreadCount($userId)
            ]
        ]);
    }

    public function createAction()
    {
        $data = $this->getRequest()->getPost()->toArray();

        if (!isset($data['type'], $data['message'], $data['type_message'])) {
            return new JsonModel([
                'success' => false,
                'error' => 'Missing required fields'
            ]);
        }

        try {
            $notification = $this->notificationService->createNotification(
                $data['type'],
                $data['message'],
                $data['type_message'],
                $data['relation_id'] ?? null,
                $data['user_id'] ?? null
            );

            return new JsonModel([
                'success' => true,
                'data' => $notification
            ]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function markAsReadAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        $userId = $this->params()->fromPost('user_id');

        if (!$id) {
            return new JsonModel([
                'success' => false,
                'error' => 'Invalid notification ID'
            ]);
        }

        $success = $this->notificationService->markAsRead($id, $userId);

        return new JsonModel([
            'success' => $success,
            'unread_count' => $success ? $this->notificationService->getUnreadCount($userId) : null
        ]);
    }

    public function typesAction()
    {
        return new JsonModel([
            'success' => true,
            'data' => $this->notificationService->getNotificationTypes()
        ]);
    }
}
