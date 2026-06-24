<?php
class ClienteModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function getByEntrenador(int $entrenadorId): array {
        $s = $this->db->prepare(
            "SELECT u.id, u.nombre, u.email, u.activo,
                    c.id AS cliente_id, c.peso_kg, c.altura_cm, c.objetivo
             FROM clientes c JOIN usuarios u ON c.usuario_id = u.id
             WHERE c.entrenador_id = ? ORDER BY u.nombre"
        );
        $s->execute([$entrenadorId]);
        return $s->fetchAll();
    }

    public function getById(int $clienteId): ?array {
        $s = $this->db->prepare(
            "SELECT u.nombre, u.email, c.id, c.usuario_id, c.entrenador_id,
                    c.peso_kg, c.altura_cm, c.objetivo, c.fecha_nac
             FROM clientes c JOIN usuarios u ON c.usuario_id = u.id WHERE c.id = ?"
        );
        $s->execute([$clienteId]);
        return $s->fetch() ?: null;
    }

    public function create(int $usuarioId, int $entrenadorId, ?float $peso, ?float $altura, ?string $objetivo): int {
        $s = $this->db->prepare(
            "INSERT INTO clientes (usuario_id, entrenador_id, peso_kg, altura_cm, objetivo)
             VALUES (?,?,?,?,?)"
        );
        $s->execute([$usuarioId, $entrenadorId, $peso, $altura, $objetivo]);
        return (int)$this->db->lastInsertId();
    }

    public function updateMedidas(int $clienteId, float $peso, float $altura): void {
        $this->db->prepare(
            "UPDATE clientes SET peso_kg=?, altura_cm=? WHERE id=?"
        )->execute([$peso, $altura, $clienteId]);
    }
}
