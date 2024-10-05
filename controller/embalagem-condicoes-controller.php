<?php
$app->post('/embalagem-condicoes-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_embalagem_condicoes = new EmbalagemCondicoesModel();

        $arr = $class_embalagem_condicoes->loadAll();
        if ($arr) {
            foreach ($arr as $key => $value) {
                $data['data'][] = $value;
            }
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