<?php
class UsuarioModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function findByEmail(string $email): ?array {
        $s = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $s->execute([$email]);
        return $s->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $s = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public function create(string $nombre, string $email, string $hash, string $rol = 'cliente'): int {
        $s = $this->db->prepare(
            "INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?,?,?,?)"
        );
        $s->execute([$nombre, $email, $hash, $rol]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $sets   = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;
        $this->db->prepare("UPDATE usuarios SET $sets WHERE id = ?")->execute($values);
    }
}
