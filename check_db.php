<?php
require_once 'config.php';
require_once 'model/Connection.php';

$conn = new Connection();
$res = $conn->select("SHOW TABLES LIKE 'tb_catalogo_avulso'");
if ($res && count($res) > 0) {
    echo "Tabela EXISTE!\n";
    print_r($res);
} else {
    echo "Tabela NAO existe.\n";
}
