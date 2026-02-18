<?php

class User
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $username, string $password, ?int $roleId = null): bool
    {
        $sql = 'INSERT INTO USERS (user_name, password, role_id) VALUES (?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$username, $password, $roleId]);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM USERS WHERE user_id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM USERS WHERE user_name = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function getAll(): array
    {
        return $this->pdo->query('SELECT * FROM USERS')->fetchAll();
    }

    public function updateRole(int $id, ?int $roleId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE USERS SET role_id = ? WHERE user_id = ?');
        return $stmt->execute([$roleId, $id]);
    }

    public function updatePassword(int $id, string $password): bool
    {
        $stmt = $this->pdo->prepare('UPDATE USERS SET password = ? WHERE user_id = ?');
        return $stmt->execute([$password, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM USERS WHERE user_id = ?');
        return $stmt->execute([$id]);
    }
}
