<?php

namespace NotificationSystem\Repository;

use DateTime;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\TableGateway;
use NotificationSystem\Entity\Notification;

class NotificationRepository
{
    private TableGateway $tableGateway;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway('notifications', $adapter);
    }

    public function save(Notification $notification): void
    {
        $data = [
            'type' => $notification->getType(),
            'message' => $notification->getMessage(),
            'type_message' => $notification->getTypeMessage(),
            'relation_id' => $notification->getRelationId(),
            'is_read' => $notification->isRead(),
            'created_at' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
            'read_at' => $notification->getReadAt()?->format('Y-m-d H:i:s'),
            'user_id' => $notification->getUserId(),
        ];

        if ($notification->getId()) {
            $this->tableGateway->update($data, ['id' => $notification->getId()]);
        } else {
            $this->tableGateway->insert($data);
            $notification->setId($this->tableGateway->getLastInsertValue());
        }
    }

    public function find(int $id): ?Notification
    {
        $result = $this->tableGateway->select(['id' => $id])->current();
        
        if (!$result) {
            return null;
        }

        return $this->hydrate($result);
    }

    public function findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $select = $this->tableGateway->getSql()->select();
        
        foreach ($criteria as $field => $value) {
            if ($value !== null) {
                $select->where([$field => $value]);
            }
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $direction) {
                $select->order("$field $direction");
            }
        }

        if ($limit) {
            $select->limit($limit);
        }

        if ($offset) {
            $select->offset($offset);
        }

        $resultSet = $this->tableGateway->selectWith($select);
        $notifications = [];

        foreach ($resultSet as $row) {
            $notifications[] = $this->hydrate($row);
        }

        return $notifications;
    }

    public function countUnread(?string $userId = null): int
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['count' => new \Laminas\Db\Sql\Expression('COUNT(*)')]);
        $select->where(['is_read' => false]);

        if ($userId) {
            $select->where(['user_id' => $userId]);
        }

        $row = $this->tableGateway->selectWith($select)->current();
        return (int) $row['count'];
    }

    public function deleteOlderThan(DateTime $date): int
    {
        return $this->tableGateway->delete([
            'created_at < ?' => $date->format('Y-m-d H:i:s'),
            'is_read' => true
        ]);
    }

    private function hydrate($data): Notification
    {
        $data = $data instanceof \ArrayObject ? $data->getArrayCopy() : $data;
        
        $notification = new Notification();
        $notification
            ->setId((int) $data['id'])
            ->setType($data['type'])
            ->setMessage($data['message'])
            ->setTypeMessage($data['type_message'])
            ->setRelationId($data['relation_id'] ? (int) $data['relation_id'] : null)
            ->setIsRead((bool) $data['is_read'])
            ->setUserId($data['user_id']);

        if (isset($data['created_at'])) {
            $notification->setCreatedAt(new DateTime($data['created_at']));
        }
        
        if (isset($data['read_at']) && $data['read_at'] !== null) {
            $notification->setReadAt(new DateTime($data['read_at']));
        }

        return $notification;
    }
}
