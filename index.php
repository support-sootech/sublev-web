<?php
session_start();
ini_set('display_errors', 'On');
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

$app->get('/dashboard', function() use ($app){
	$app->render('/dashboard.php', array());
});

$app->get('/forgot-password', function() use ($app){	
	$app->render('/forgot-password-page.php', array());
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

$app->run();

?>
