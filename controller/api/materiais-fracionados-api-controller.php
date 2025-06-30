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

$app->map('/app-materiais-fracionados-vencimento', function() use ($app){
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

            $class_materiais = new MateriaisModel();
            $arr['vencem_hoje'] = $class_materiais->loadQuantMateriaisVencimento('','texto_vencem_hoje', $usuario['id_empresas']);
            $arr['vencem_hoje'] = isset($arr['vencem_hoje'][0]['quantidade']) ? $arr['vencem_hoje'][0]['quantidade'] : 0;

            $arr['vencem_amanha'] = $class_materiais->loadQuantMateriaisVencimento('','texto_vencem_amanha', $usuario['id_empresas']);
            $arr['vencem_amanha'] = isset($arr['vencem_amanha'][0]['quantidade']) ? $arr['vencem_amanha'][0]['quantidade'] : 0;

            $arr['vencem_semana'] = $class_materiais->loadQuantMateriaisVencimento('','texto_vencem_semana', $usuario['id_empresas']);
            $arr['vencem_semana'] = isset($arr['vencem_semana'][0]['quantidade']) ? $arr['vencem_semana'][0]['quantidade'] : 0;

            $arr['vencem_mais_1_semana'] = $class_materiais->loadQuantMateriaisVencimento('','texto_vencem_mais_1_semana', $usuario['id_empresas']);
            $arr['vencem_mais_1_semana'] = isset($arr['vencem_mais_1_semana'][0]['quantidade']) ? $arr['vencem_mais_1_semana'][0]['quantidade'] : 0;
            
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

$app->map('/app-materiais-fracionados-baixa', function() use ($app){
	$response_status = 400;
    $response_metodo = 'PUT';
    $data = array();

    if ($app->request->isOptions()) {
        $response_status = 200;
        $response_metodo = 'PUT, OPTIONS';
        $data = array('OK');
    } else if ($app->request->isPut()) {

        try {
            $usuario = getUsuario($app);
            $params = retornaParametros($app);

            if ($usuario==false) {
                throw new Exception("Usuário não localizado!");
            }

            if (empty($params['id_materiais_fracionados'])) {
                throw new Exception('É necessário informar o material!');
            }

            if (empty($params['status'])) {
                throw new Exception('É necessário informar o status!');
            }

            if ($params['status']=='D' && empty($params['motivo_descarte'])) {
                throw new Exception('Para descarte do material é necessário informar o motivo!');
            }

            $class_materiais = new MateriaisFracionadosModel();

            $material_fracionado = $class_materiais->loadId($params['id_materiais_fracionados']);
            if (!$material_fracionado || $material_fracionado['status']=='D') {
                throw new Exception('Material fracionado não localizado!');
            }

            $material_fracionado['status'] = $params['status'];
            $material_fracionado['id_usuarios'] = $usuario['id_usuarios'];
            $material_fracionado['motivo_descarte'] = $params['motivo_descarte'];
    
            $salvar = $class_materiais->edit($material_fracionado, array('id_materiais_fracionados'=>$params['id_materiais_fracionados']));
            $response_status = 200;
            $data = array('success'=>true, 'type'=>'success', 'msg'=>'Registro alterado com sucesso!');

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

})->via('PUT','OPTIONS');

?>