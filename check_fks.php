<?php
require_once 'config.php';
require_once 'model/Connection.php';

$conn = new Connection();
$sql = "SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'tb_catalogo_avulso' 
        AND REFERENCED_TABLE_NAME IS NOT NULL";

$res = $conn->select($sql);
print_r($res);
