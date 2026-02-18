<?php

class Partner
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $name, ?string $logo = null, ?string $website = null): bool
    {
        $sql = 'INSERT INTO PARTNERS (partner_name, partner_logo, partner_website) VALUES (?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $logo, $website]);
    }

    public function getAll(): array
    {
        return $this->pdo->query('SELECT * FROM PARTNERS')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM PARTNERS WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function update(int $id, string $name, ?string $logo = null, ?string $website = null): bool
    {
        $stmt = $this->pdo->prepare('UPDATE PARTNERS SET partner_name = ?, partner_logo = ?, partner_website = ? WHERE id = ?');
        return $stmt->execute([$name, $logo, $website, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM PARTNERS WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
