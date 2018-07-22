<?php

namespace App\Repository;

/**
 * Class TaskRepository
 * @package App\Repository
 */
class TaskRepository
{
    /**
     * @var Database
     */
    private $db;

    /**
     * @var
     */
    private $tableName;

    /**
     * TaskRepository constructor.
     * @param $db
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->tableName = 'Task';
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        $stmt = $this->db->getConnection()->prepare('SELECT * FROM ' . $this->tableName . ' ORDER BY id DESC');
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $id
     * @return null
     */
    public function findOneById($id)
    {
        $stmt = $this->db->getConnection()->prepare('SELECT * FROM ' . $this->tableName . ' WHERE id = :id');
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $task = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$task) {
            return null;
        } else {
            return $task;
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        $stmt = $this->db->getConnection()->prepare('INSERT INTO ' . $this->tableName . ' (user_id, title, description, creation_date, status) VALUES(:user_id, :title, :description, NOW(), :status)');
        $stmt->bindParam(':user_id', $data['user_id'], \PDO::PARAM_INT);
        $stmt->bindParam(':title', $data['title'], \PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], \PDO::PARAM_STR);
        $stmt->bindParam(':status', $data['status'], \PDO::PARAM_INT);
        $stmt->execute();

        return $data;
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateById($id, $data)
    {
        $task = $this->findOneById($id);

        $stmt = $this->db->getConnection()->prepare('UPDATE ' . $this->tableName . ' SET user_id = :user_id, title = :title, description = :description, creation_date = :creation_date, status = :status');
        $stmt->bindParam(':user_id', $data['user_id'] ?? $task['user_id'], \PDO::PARAM_INT);
        $stmt->bindParam(':title', $data['title'] ?? $task['title'], \PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'] ?? $task['description'], \PDO::PARAM_STR);
        $stmt->bindParam(':creation_date', $data['creation_date'] ?? $task['creation_date']);
        $stmt->bindParam(':status', $data['status'] ?? $task['status'], \PDO::PARAM_INT);
        $stmt->execute();

        return $data;
    }

    /**
     * @param $id
     */
    public function deleteById($id)
    {
        $stmt = $this->db->getConnection()->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :id');
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }
}