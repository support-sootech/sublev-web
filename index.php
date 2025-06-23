<?php
session_start();
//ini_set('display_errors', 'Off');
//ini_set('memory_limit', '512M');
require_once("vendor/autoload.php");
require_once("config.php");
require_once("funcoes.php");


\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim(array(
	'debug'=>TRUE,
	'templates.path' => './views'
));

//ROTAS
$app->get('/', function() use ($app){
	$app->render('/login-page.php', array());
});

$app->get('/home', function() use ($app){
	if (valida_logado()) {
		$app->render('/home-page.php', array());
	} else {
		$app->notFound();
	}
});

$app->get('/dashboard', function() use ($app){
	if (valida_logado()) {
		$app->render('/dashboard.php', array());
	} else {
		$app->notFound();
	}
});

$app->get('/forgot-password', function() use ($app){	
	//$app->render('/forgot-password-page.php', array());
	$app->render('/reset-password-page.php', array());
});

$app->get('/logout', function() use ($app){
	$app->redirect('/');
});

$app->get('/materiais-marcas', function() use ($app){	
	$app->render('/materiais-marcas-page.php', array());
});

$app->get('/info', function() use ($app){
	echo phpinfo();
});

$app->get('/teste', function() use ($app){
	$arr = array('1','1','1');
	
	$data = array();
	try {
		$data = fracionarMateriais(27,'12/06/2025',$arr,'UNIDADE',true,2);
	} catch (Exception $e) {
		$data = $e->getMessage();
	}

	verMatriz($data);
});

$app->notFound(function () use ($app) {
    $app->render('/404.php');
});

include_once('controller/login-controller.php');
include_once('controller/menu-controller.php');
include_once('controller/perfil-controller.php');
include_once('controller/permissoes-controller.php');
include_once('controller/menu-permissoes-perfil-controller.php');
include_once('controller/usuarios-controller.php');
include_once('controller/tipos-pessoas-controller.php');
include_once('controller/empresas-controller.php');
include_once('controller/fornecedores-fabricantes-controller.php');
include_once('controller/embalagens-controller.php');
include_once('controller/embalagens-tipos-controller.php');
include_once('controller/unidades-medidas-controller.php');
include_once('controller/materiais-tipos-controller.php');
include_once('controller/materiais-marcas-controller.php');
include_once('controller/materiais-categorias-controller.php');
include_once('controller/produtos-controller.php');
include_once('controller/materiais-controller.php');
include_once('controller/fracionar-material-controller.php');
include_once('controller/setor-controller.php');
include_once('controller/auto-fracionar-material-controller.php');
include_once('controller/teste-controller.php');
include_once('controller/materiais-fracionados-log-controller.php');
include_once('controller/embalagem-condicoes-controller.php');
include_once('controller/modo-conservacao-controller.php');
include_once('controller/relatorios-controller.php');
include_once('controller/etiqueta-detalhes-controller.php');
include_once('controller/home-controller.php');

//APP
include_once('controller/api/login-api-controller.php');
include_once('controller/api/etiqueta-api-controller.php');
include_once('controller/api/materiais-api-controller.php');
include_once('controller/api/materiais-fracionados-api-controller.php');

$app->run();

?>
