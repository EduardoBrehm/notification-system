<?php

namespace NotificationSystem\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use NotificationSystem\Service\NotificationService;
use NotificationSystem\Service\RedirectService;

class RedirectController extends AbstractActionController
{
    public function __construct(
        private NotificationService $notificationService,
        private RedirectService $redirectService
    ) {}

    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id');
        $userId = $this->params()->fromQuery('user_id');

        if (!$id) {
            return $this->redirect()->toRoute('home');
        }

        $notification = $this->notificationService->getNotificationById($id);

        if (!$notification || ($userId && $notification->getUserId() !== $userId)) {
            return $this->redirect()->toRoute('home');
        }

        // Mark as read
        $this->notificationService->markAsRead($id, $userId);

        // Get redirect URL
        $redirect = $this->redirectService->getRedirectUrl(
            $notification->getTypeMessage(),
            $notification->getRelationId()
        );

        if (!$redirect['success']) {
            return $this->redirect()->toRoute('home');
        }

        // If it's an AJAX request, return JSON
        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonModel([
                'success' => true,
                'redirect_url' => $redirect['url']
            ]);
        }

        // Otherwise redirect
        return $this->redirect()->toUrl($redirect['url']);
    }
}
