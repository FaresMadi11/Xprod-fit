<?php
class PesoHistorialModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function registrar(int $clienteId, float $peso, string $tipo = 'manual'): void {
        $this->db->prepare(
            "INSERT INTO peso_historial (cliente_id, peso_kg, fecha, tipo) VALUES (?,?,CURDATE(),?)
             ON DUPLICATE KEY UPDATE peso_kg=?"
        )->execute([$clienteId, $peso, $tipo, $peso]);
    }

    public function getHistorial(int $clienteId): array {
        $s = $this->db->prepare(
            "SELECT fecha, peso_kg FROM peso_historial WHERE cliente_id=? ORDER BY fecha ASC"
        );
        $s->execute([$clienteId]);
        return $s->fetchAll();
    }
}
