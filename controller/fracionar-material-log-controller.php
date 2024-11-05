<?php
$app->post('/fracionar-material-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $id_materiais_fracionados = $app->request->post('id_materiais_fracionados');
        $class_materiais_fracionados_log = new MateriaisFracionadosLogModel();
        $arr = $class_materiais_fracionados_log->loadIdMateriaisFrancionados($id_materiais_fracionados);
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