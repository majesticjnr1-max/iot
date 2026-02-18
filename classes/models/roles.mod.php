<?php

class Role
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $name, ?string $description = null): bool
    {
        $sql = 'INSERT INTO ROLES (role_name, role_description) VALUES (?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $description]);
    }

    public function getAll(): array
    {
        return $this->pdo->query('SELECT * FROM ROLES')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ROLES WHERE role_id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function update(int $id, string $name, ?string $description = null): bool
    {
        $stmt = $this->pdo->prepare('UPDATE ROLES SET role_name = ?, role_description = ? WHERE role_id = ?');
        return $stmt->execute([$name, $description, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM ROLES WHERE role_id = ?');
        return $stmt->execute([$id]);
    }
}
