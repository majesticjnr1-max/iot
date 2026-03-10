<?php
class Testimony {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM testimonies ORDER BY id DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM testimonies WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
    public function create($picture, $message, $name, $position, $avatar) {
        $stmt = $this->pdo->prepare("INSERT INTO testimonies (picture, message, name, position, avatar) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$picture, $message, $name, $position, $avatar]);
    }
    public function update($id, $picture, $message, $name, $position, $avatar) {
        $stmt = $this->pdo->prepare("UPDATE testimonies SET picture = ?, message = ?, name = ?, position = ?, avatar = ? WHERE id = ?");
        return $stmt->execute([$picture, $message, $name, $position, $avatar, $id]);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM testimonies WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
