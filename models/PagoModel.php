<?php
class PagoModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function getByCliente(int $clienteId): array {
        $s = $this->db->prepare(
            "SELECT id, concepto, monto, fecha_emision, fecha_vence, estado, comprobante_img, referencia, notas_cliente
             FROM pagos WHERE cliente_id = ? ORDER BY fecha_vence DESC"
        );
        $s->execute([$clienteId]);
        return $s->fetchAll();
    }
}
