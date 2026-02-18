<?php

class OurProject
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $name, ?string $description = null, ?string $photo = null): bool
    {
        $sql = 'INSERT INTO OUR_PROJECT (project_name, project_description, photo) VALUES (?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $description, $photo]);
    }

    public function getAll(): array
    {
        return $this->pdo->query('SELECT * FROM OUR_PROJECT')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM OUR_PROJECT WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function update(int $id, string $name, ?string $description = null, ?string $photo = null): bool
    {
        $stmt = $this->pdo->prepare('UPDATE OUR_PROJECT SET project_name = ?, project_description = ?, photo = ? WHERE id = ?');
        return $stmt->execute([$name, $description, $photo, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM OUR_PROJECT WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
