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

// API compat (mobile app) - /app-materiais-vencimento (GET|POST)
$app->map('/app-materiais-vencimento', function() use ($app){
    $status = 200;
    $ret = ['success'=>false, 'data'=>[]];
    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $statusParam = $app->request->params('status') ?: $app->request->post('status');
                $id_acao = $app->request->params('id_acao') ?: $app->request->post('id_acao');
                $class_materiais = new MateriaisModel();
                $arr = $class_materiais->loadMateriaisVencimento($statusParam, $id_acao);
                $ret = ['success'=>true, 'data'=>($arr?:[])];
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao listar materiais com vencimento', 'detail'=>$e->getMessage()];
            }
        } else {
            $status = 401;
            $ret = ['success'=>false, 'msg'=>'Não autorizado'];
        }
    }
    while (ob_get_level()) { ob_end_clean(); }
    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS';
    $response['Content-Type'] = 'application/json';
    $response->status($status);
    $response->body(json_encode($ret));
})->via('GET','POST','OPTIONS');

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
            $arr = $class_materiais->loadQuantMateriaisVencimento($status,$id_acao, $id_empresas);
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

// API compat (mobile app) - /app-quant-materiais-vencimento (GET|POST)
$app->map('/app-quant-materiais-vencimento', function() use ($app){
    $status = 200;
    $ret = ['success'=>false, 'data'=>[]];
    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $statusParam = $app->request->params('status') ?: $app->request->post('status');
                $id_acao = $app->request->params('id_acao') ?: $app->request->post('id_acao');
                $id_empresas = function_exists('getIdEmpresasLogado') ? getIdEmpresasLogado() : 0;
                if (empty($id_empresas) && function_exists('_getHeaderValue')) {
                    $hdr = _getHeaderValue('X-Company-Id'); if (!empty($hdr)) $id_empresas = (int)$hdr;
                }
                $class_materiais = new MateriaisModel();
                $arr = $class_materiais->loadQuantMateriaisVencimento($statusParam, $id_acao, $id_empresas);
                $ret = ['success'=>true, 'data'=>($arr?:[])];
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao contar materiais com vencimento', 'detail'=>$e->getMessage()];
            }
        } else {
            $status = 401;
            $ret = ['success'=>false, 'msg'=>'Não autorizado'];
        }
    }
    while (ob_get_level()) { ob_end_clean(); }
    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS';
    $response['Content-Type'] = 'application/json';
    $response->status($status);
    $response->body(json_encode($ret));
})->via('GET','POST','OPTIONS');



?>