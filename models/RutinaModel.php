<?php
class RutinaModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function getByCliente(int $clienteId): array {
        $s = $this->db->prepare(
            "SELECT r.id, r.dias_semana, r.estado, r.descripcion, r.creado_en
             FROM rutinas r WHERE r.cliente_id = ? ORDER BY r.creado_en DESC"
        );
        $s->execute([$clienteId]);
        return $s->fetchAll();
    }

    public function getWithEjercicios(int $rutinaId, int $clienteId): ?array {
        $s = $this->db->prepare(
            "SELECT r.id, r.dias_semana, r.descripcion, r.estado, u.nombre AS entrenador
             FROM rutinas r JOIN usuarios u ON r.entrenador_id = u.id
             WHERE r.id = ? AND r.cliente_id = ?"
        );
        $s->execute([$rutinaId, $clienteId]);
        $rutina = $s->fetch();
        if (!$rutina) return null;

        $sEj = $this->db->prepare(
            "SELECT e.id, e.nombre, e.series, e.repeticiones, e.rir, e.nota_biomecanica, e.orden,
                    COALESCE(s.completado,0) AS completado, COALESCE(s.reps_realizadas,0) AS reps_realizadas
             FROM ejercicios e
             LEFT JOIN sesiones s ON s.ejercicio_id=e.id AND s.cliente_id=? AND s.fecha=CURDATE()
             WHERE e.rutina_id=? ORDER BY e.orden"
        );
        $sEj->execute([$clienteId, $rutinaId]);
        $rutina['ejercicios'] = $sEj->fetchAll();
        return $rutina;
    }
}
