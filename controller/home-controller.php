<?php

$app->get('/home', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/home-page.php');
    } else {
        $app->notFound();
    }
});

$app->post('/materiais-vencimento-json', function() use ($app){
    $status = 200;
	$data = array();
   
    if (valida_logado()) {
        
        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
            
            $id_acao = '';
            if ($app->request->post('id_acao')) {
                $id_acao = $app->request->post('id_acao');
            }

            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadMateriaisVencimento($status,$id_acao);
            if ($arr) {
                foreach ($arr as $key => $value) {
                    $data['data'][] = $value;
                }
            }
        } catch (Exception $e) {
            die('ERROR: '.$e->getMessage().'');
        }
        

    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->post('/quant-materiais-vencimento-json', function() use ($app){
    $status = 200;
	$data = array();
   
    if (valida_logado()) {
        
        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
            
            $id_acao = '';
            if ($app->request->post('id_acao')) {
                $id_acao = $app->request->post('id_acao');
            }

            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadQuantMateriaisVencimento($status,$id_acao);
            if ($arr) {
                foreach ($arr as $key => $value) {
                    $data['data'][] = $value;
                }
            }
        } catch (Exception $e) {
            die('ERROR: '.$e->getMessage().'');
        }
        

    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});



?>