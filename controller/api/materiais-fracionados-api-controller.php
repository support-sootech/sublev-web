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

            if (!isset($params['dt_vencimento']) || empty($params['dt_vencimento'])) {
                throw new Exception("É necessário informar a data de vencimento!");
            }            
           
            $class_materiais = new MateriaisModel();
            $material = $class_materiais->loadIdMaterialDetalhes('A',$params['id_materiais']);

            if ($material) {
                
                $fg_fracionado = fracionarMateriais($params['id_materiais'], $params['dt_vencimento'], array(), $usuario['id_usuarios']);
                
                if ($fg_fracionado['success']) {
                    $response_status = 200;
                    
                    $class_etiquetas = new EtiquetasModel();
                    $arr_etiqueta = array();
                    $id_etiquetas = '';
                    $arr_etiqueta['id_etiquetas'] = '';
                    $arr_etiqueta['descricao'] = 'Etiqueta '.$material['descricao'].' - '.dt_br(date("Ymd"));
                    $arr_etiqueta['codigo'] = $material['cod_barras'];
                    $arr_etiqueta['id_materiais_fracionados'] = $material['id_materiais_fracionados'];
                    $arr_etiqueta['id_materiais'] = $material['id_materiais'];
                    $arr_etiqueta['status'] = 'A';
                    $arr_etiqueta['id_usuarios'] = $usuario['id_usuarios'];
                    $data_etiqueta = $class_etiquetas->add($arr_etiqueta);
                    
                    if ($data_etiqueta) {
                        $etiqueta = $class_etiquetas->loadId($data_etiqueta);
                        $material = $class_materiais->loadIdMaterialDetalhes('A',$etiqueta['id_materiais']);
                        $res = array('etiqueta'=>$etiqueta, 'material'=>$material, 'fracionamento'=>$fg_fracionado);
                    }

                } else {
                    throw new Exception($fg_fracionado['msg']);
                }
            } else {
                throw new Exception("Material nãolocalizado!", 1);
            }
            
            $response_status = 200;
            $data = array('success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$res);

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