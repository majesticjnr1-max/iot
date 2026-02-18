<?php

class Privilege
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $name, ?string $description = null, ?string $module = null): bool
    {
        $sql = 'INSERT INTO PRIVILEGES (privilege_name, privilege_description, module) VALUES (?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $description, $module]);
    }

    public function getAll(): array
    {
        return $this->pdo->query('SELECT * FROM PRIVILEGES')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM PRIVILEGES WHERE privilege_id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function update(int $id, string $name, ?string $description = null, ?string $module = null): bool
    {
        $stmt = $this->pdo->prepare('UPDATE PRIVILEGES SET privilege_name = ?, privilege_description = ?, module = ? WHERE privilege_id = ?');
        return $stmt->execute([$name, $description, $module, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM PRIVILEGES WHERE privilege_id = ?');
        return $stmt->execute([$id]);
    }
}
