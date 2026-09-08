<?php
class SesionAsistenciaModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function iniciar(int $rutinaId, int $clienteId, int $entrenadorId): int {
        $s = $this->db->prepare(
            "INSERT INTO sesiones_asistencia (rutina_id, cliente_id, entrenador_id) VALUES (?,?,?)"
        );
        $s->execute([$rutinaId, $clienteId, $entrenadorId]);
        return (int)$this->db->lastInsertId();
    }

    public function completar(int $id): void {
        $this->db->prepare(
            "UPDATE sesiones_asistencia SET estado='completada', fin_en=NOW() WHERE id=?"
        )->execute([$id]);
    }

    public function getActiva(int $clienteId): ?array {
        $s = $this->db->prepare(
            "SELECT sa.id, sa.inicio_en, r.descripcion AS rutina_nombre
             FROM sesiones_asistencia sa JOIN rutinas r ON sa.rutina_id=r.id
             WHERE sa.cliente_id=? AND sa.estado='iniciada' ORDER BY sa.inicio_en DESC LIMIT 1"
        );
        $s->execute([$clienteId]);
        return $s->fetch() ?: null;
    }
}
