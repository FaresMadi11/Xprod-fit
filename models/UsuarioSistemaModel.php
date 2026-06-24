<?php
class UsuarioSistemaModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }
}
