<?php
class MetaModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }
}
