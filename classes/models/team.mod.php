<?php

class Team
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $name, string $position, ?string $photo = null, ?string $facebook = null, ?string $instagram = null, ?string $twitter = null, ?string $linkedin = null): bool
    {
        $sql = 'INSERT INTO TEAM (name, position, photo, facebook, instagram, twitter, linkedin) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $position, $photo, $facebook, $instagram, $twitter, $linkedin]);
    }

    public function getAll(): array
    {
        return $this->pdo->query('SELECT * FROM TEAM')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM TEAM WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function update(int $id, string $name, string $position, ?string $photo = null, ?string $facebook = null, ?string $instagram = null, ?string $twitter = null, ?string $linkedin = null): bool
    {
        $stmt = $this->pdo->prepare('UPDATE TEAM SET name = ?, position = ?, photo = ?, facebook = ?, instagram = ?, twitter = ?, linkedin = ? WHERE id = ?');
        return $stmt->execute([$name, $position, $photo, $facebook, $instagram, $twitter, $linkedin, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM TEAM WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
