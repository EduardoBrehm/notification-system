<?php

namespace NotificationSystem\Service;

use Laminas\Router\RouteStackInterface;

class RedirectService
{
    private array $redirectMap;

    public function __construct(
        private RouteStackInterface $router,
        array $config
    ) {
        $this->redirectMap = $config['notification_system']['redirect_map'] ?? [];
    }

    public function getRedirectUrl(string $typeMessage, ?int $relationId = null): array
    {
        $result = [
            'success' => false,
            'url' => '',
            'params' => []
        ];

        if (!isset($this->redirectMap[$typeMessage])) {
            // Fallback to default if exists
            if (!isset($this->redirectMap['default'])) {
                return $result;
            }
            $typeMessage = 'default';
        }

        $redirectConfig = $this->redirectMap[$typeMessage];
        $params = [];

        foreach ($redirectConfig['params'] as $param => $value) {
            if ($value === 'relation_id') {
                $params[$param] = $relationId;
            } else {
                $params[$param] = $value;
            }
        }

        try {
            $url = $this->router->assemble($params, [
                'name' => $redirectConfig['route']
            ]);

            $result['success'] = true;
            $result['url'] = $url;
            $result['params'] = $params;
        } catch (\Exception $e) {
            // Log error if needed
            return $result;
        }

        return $result;
    }

    public function addRedirectMapping(string $type, string $route, array $params = []): void
    {
        $this->redirectMap[$type] = [
            'route' => $route,
            'params' => $params
        ];
    }

    public function removeRedirectMapping(string $type): bool
    {
        if (isset($this->redirectMap[$type])) {
            unset($this->redirectMap[$type]);
            return true;
        }
        return false;
    }

    public function getRedirectMap(): array
    {
        return $this->redirectMap;
    }
}
