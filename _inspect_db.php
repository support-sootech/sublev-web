<?php
require __DIR__.'/_pdo_boot.php';
$q = $GLOBALS['pdo']->query("SELECT id_usuarios, hash, status FROM tb_usuarios ORDER BY id_usuarios DESC LIMIT 50");
foreach ($q as $r) {
  echo $r['id_usuarios']."\t".$r['hash']."\t".$r['status']."\n";
}
