<?php
$app->map('/app-materiais-info(/:cod_barras)', function($cod_barras='') use ($app){
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

            if (empty($cod_barras)) {
                throw new Exception("É necessário informar o código de barras!");
            }

            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadCodBarrasMaterialDetalhes('', $cod_barras);
            if ($arr==false) {
                throw new Exception("Nenhum material localizado!");                
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


?>