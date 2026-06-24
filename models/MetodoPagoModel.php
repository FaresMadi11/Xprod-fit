<?php
class MetodoPagoModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function getByEntrenador(int $entrenadorId): array {
        $s = $this->db->prepare("SELECT * FROM metodos_pago WHERE entrenador_id = ? AND activo = 1 ORDER BY id");
        $s->execute([$entrenadorId]);
        return $s->fetchAll();
    }

    public function save(array $data): void {
        $s = $this->db->prepare(
            "INSERT INTO metodos_pago (entrenador_id, tipo, monto_dolares, banco, titular, cedula, telefono, numero_cuenta, instrucciones)
             VALUES (:entrenador_id,:tipo,:monto_dolares,:banco,:titular,:cedula,:telefono,:numero_cuenta,:instrucciones)"
        );
        $s->execute($data);
    }

    public function delete(int $id, int $entrenadorId): void {
        $this->db->prepare("UPDATE metodos_pago SET activo=0 WHERE id=? AND entrenador_id=?")
                 ->execute([$id, $entrenadorId]);
    }
}
