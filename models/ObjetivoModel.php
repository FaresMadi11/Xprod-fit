<?php
class ObjetivoModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function getCatalogo(): array {
        return $this->db->query("SELECT * FROM objetivos_catalogo ORDER BY id")->fetchAll();
    }

    public function getByCliente(int $clienteId): array {
        $s = $this->db->prepare(
            "SELECT oc.* FROM cliente_objetivos co
             JOIN objetivos_catalogo oc ON co.objetivo_id = oc.id
             WHERE co.cliente_id = ?"
        );
        $s->execute([$clienteId]);
        return $s->fetchAll();
    }
}
