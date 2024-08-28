<?php
$app->get('/auto-fracionar-material', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/auto-fracionar-material-page.php');
    } else {
        $app->notFound();
    }
});

?>