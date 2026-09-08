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
            "INSERT INTO metodos_pago (entrenador_id, tipo, monto_dolares, banco, titular, cedula, telefono, numero_cuenta, instrucciones, monto_bs, correo_binance)
             VALUES (:entrenador_id,:tipo,:monto_dolares,:banco,:titular,:cedula,:telefono,:numero_cuenta,:instrucciones,:monto_bs,:correo_binance)"
        );
        $s->execute($data);
    }

    public function update(int $id, int $entrenadorId, array $data): void {
        $data['id'] = $id;
        $data['entrenador_id'] = $entrenadorId;
        $s = $this->db->prepare(
            "UPDATE metodos_pago SET 
                tipo = :tipo, monto_dolares = :monto_dolares, banco = :banco, titular = :titular, 
                cedula = :cedula, telefono = :telefono, numero_cuenta = :numero_cuenta, 
                instrucciones = :instrucciones, monto_bs = :monto_bs, correo_binance = :correo_binance
             WHERE id = :id AND entrenador_id = :entrenador_id"
        );
        $s->execute($data);
    }

    public function delete(int $id, int $entrenadorId): void {
        $this->db->prepare("DELETE FROM metodos_pago WHERE id=? AND entrenador_id=?")
                 ->execute([$id, $entrenadorId]);
    }
}
