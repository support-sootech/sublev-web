<?php
$app->map('/app-materiais-fracionados-info', function($codigo='') use ($app){
	$response_status = 400;
    $response_metodo = 'GET';
    $data = array();

    if ($app->request->isOptions()) {
        $response_status = 200;
        $response_metodo = 'GET, OPTIONS';
        $data = array('OK');
    } else if ($app->request->isGet()) {

        try {
            $usuario = getUsuario($app);
            if ($usuario==false) {
                throw new Exception("Usuário não localizado!");
            }            
            
            $class_materiais_fracionados = new MateriaisFracionadosModel();

            $arr = $class_materiais_fracionados->load($usuario['id_empresas'],'','',$usuario['id_usuarios']);
            if ($arr==false) {
                throw new Exception("Nenhum material fracionado localizado!");
            }
            
            $response_status = 200;
            $data = array('success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$arr);

        } catch (Exception $e) {
            $data = array('error'=>true, 'type'=>'danger', 'msg'=>$e->getMessage());
        }        
        
    } else {
        $data = array('success'=>false, 'type'=>'danger', 'msg'=>'Método incorreto!');
    }

	$response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Headers'] = '*';
	$response['Access-Control-Allow-Methods'] = $response_metodo;
	$response['Content-Type'] = 'application/json';

	$response->status($response_status);
	$response->body(json_encode($data));

})->via('GET','OPTIONS');

$app->map('/app-materiais-fracionar', function() use ($app){
	$response_status = 400;
    $response_metodo = 'POST';
    $data = array();
    $res = false;

    if ($app->request->isOptions()) {
        $response_status = 200;
        $response_metodo = 'POST, OPTIONS';
        $data = array('OK');
    } else if ($app->request->isPost()) {

        try {
            $usuario = getUsuario($app);
            if ($usuario==false) {
                throw new Exception("Usuário não localizado!");
            }

            $params = retornaParametros($app);

            if (!isset($params['id_materiais']) || empty($params['id_materiais'])) {
                throw new Exception("É necessário informar o código do material!");
            }

            if (!isset($params['quantidade']) || empty($params['quantidade'])) {
                throw new Exception("É necessário informar a quantidade!");
            }

            if (!isset($params['tipo']) || empty($params['tipo'])) {
                throw new Exception("É necessário informar a tipo do fracionamento (UNIDADE OU FRACAO)!");
            }
           
            $class_materiais = new MateriaisModel();
            $material = $class_materiais->loadIdMaterialDetalhes('A',$params['id_materiais']);

            if ($material) {
                $quantidade = array();
                for ($i=1; $i <= $params['quantidade']; $i++) { 
                    $quantidade[] = 1;
                }

                $tipo_fracionamento = mb_strtoupper($params['tipo']) == 'FRACAO' ? 'FRACAO' : 'UNIDADE';
                
                $fracionamento = fracionarMateriais(
                    $params['id_materiais'], 
                    $quantidade, 
                    $tipo_fracionamento,
                    true,
                    $usuario['id_usuarios']
                );                
                $response_status = 200;
                $data = array('success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$fracionamento);
            } else {
                throw new Exception("Material nãolocalizado!", 1);
            }
        } catch (Exception $e) {
            $data = array('error'=>true, 'type'=>'danger', 'msg'=>$e->getMessage());
        }        
        
    } else {
        $data = array('success'=>false, 'type'=>'danger', 'msg'=>'Método incorreto!');
    }

	$response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Headers'] = '*';
	$response['Access-Control-Allow-Methods'] = $response_metodo;
	$response['Content-Type'] = 'application/json';

	$response->status($response_status);
	$response->body(json_encode($data));

})->via('POST','OPTIONS');

?>