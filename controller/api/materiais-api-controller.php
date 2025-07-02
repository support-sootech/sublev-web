<?php
$app->map('/app-materiais-info(/:filtro)', function($filtro='') use ($app){
	$response_status = 400;
    $response_metodo = 'GET';
    $data = array();
    $arr = array();

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

            if (empty($filtro)) {
                throw new Exception("É necessário informar o código de barras!");
            }

            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadMaterialCodBarrasNomeDetalhes('', $filtro);
            if ($arr==false) {
                throw new Exception("Nenhum material localizado!");                
            }
            $arr_materiais = array();
            if (is_array($arr)) {
                foreach ($arr as $key => $value) {
                    $arr_materiais['id_materiais'] = $value['id_materiais'];
                    $arr_materiais['descricao'] = $value['descricao'];
                    $arr_materiais['peso'] = $value['peso'];
                    $arr_materiais['lote'] = $value['lote'];
                    $arr_materiais['cod_barras'] = $value['cod_barras'];
                    $arr_materiais['marca'] = $value['marca'];
                    $arr_materiais['ds_unidade_medida'] = $value['ds_unidade_medida'];
                    $arr_materiais['color_dt_vencimento'] = $value['color_dt_vencimento'];
                }
            }
            
            $response_status = 200;
            $data = array('success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$arr_materiais);

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