<?php

class OurWork
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $title, ?string $photo = null): bool
    {
        $sql = 'INSERT INTO OUR_WORK (work_title, photo) VALUES (?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $photo]);
    }

    public function getAll(): array
    {
        return $this->pdo->query('SELECT * FROM OUR_WORK')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM OUR_WORK WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function update(int $id, string $title, ?string $photo = null): bool
    {
        $stmt = $this->pdo->prepare('UPDATE OUR_WORK SET work_title = ?, photo = ? WHERE id = ?');
        return $stmt->execute([$title, $photo, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM OUR_WORK WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
