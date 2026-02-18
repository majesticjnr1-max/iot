<?php

class Count
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addCount(int $impact = 0, int $project = 0, int $member = 0, int $trainees = 0): bool
    {
        $sql = 'INSERT INTO `COUNT` (count_impact, count_project, count_member, count_trainees) VALUES (?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$impact, $project, $member, $trainees]);
    }

    public function getAll(): array
    {
        return $this->pdo->query('SELECT * FROM `COUNT`')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `COUNT` WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function update(int $id, int $impact, int $project, int $member, int $trainees): bool
    {
        $stmt = $this->pdo->prepare('UPDATE `COUNT` SET count_impact = ?, count_project = ?, count_member = ?, count_trainees = ? WHERE id = ?');
        return $stmt->execute([$impact, $project, $member, $trainees, $id]);
    }
}