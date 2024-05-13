<?php
session_start();
ini_set('display_errors', 'On');
ini_set('memory_limit', '256M');
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
include_once('controller/fornecedores-controller.php');

$app->run();

?>
