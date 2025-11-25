<?php
require __DIR__.'/config.php';
require __DIR__.'/model/Connection.php';

$c = new Connection();

// pega a propriedade privada "conn" (PDO) via Reflection
$ref  = new ReflectionClass($c);
$prop = $ref->getProperty('conn');
$prop->setAccessible(true);
$pdo = $prop->getValue($c);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$GLOBALS['pdo'] = $pdo;
