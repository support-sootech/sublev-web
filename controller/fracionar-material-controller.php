<?php
$app->get('/fracionar-material', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/fracionar-material-page.php');
    } else {
        $app->notFound();
    }
});
$app->get('/fracionar-material1', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/fracionar-material-page1.php');
    } else {
        $app->notFound();
    }
});
?>