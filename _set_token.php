<?php
require __DIR__.'/_pdo_boot.php';

$token = '03249a816baf4f613f62095a13698467'; // o mesmo que o app envia no header
$id    = 2;                                  // <-- TROQUE para o id_usuarios certo

$st = $GLOBALS['pdo']->prepare("UPDATE tb_usuarios SET hash=?, status='A' WHERE id_usuarios=?");
$st->execute([$token, $id]);
echo "linhas_afetadas=".$st->rowCount()."\n";
