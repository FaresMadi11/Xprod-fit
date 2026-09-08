<?php
class UsuarioModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function findByEmail(string $identifier): ?array {
        $clean = trim($identifier);
        if ($clean === '') return null;
        
        $s = $this->db->prepare(
            "SELECT * FROM usuarios 
             WHERE LOWER(TRIM(email)) = LOWER(?) 
                OR LOWER(TRIM(nombre)) = LOWER(?) 
                OR REPLACE(LOWER(email), '@', '') = LOWER(?)
                OR SUBSTRING_INDEX(LOWER(email), '@', 1) = LOWER(?)
                OR LOWER(nombre) LIKE CONCAT(LOWER(?), '%')
             ORDER BY (LOWER(TRIM(email)) = LOWER(?)) DESC,
                      (LOWER(TRIM(nombre)) = LOWER(?)) DESC
             LIMIT 1"
        );
        $s->execute([$clean, $clean, $clean, $clean, $clean, $clean, $clean]);
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
