<?php
$app->get('/fracionar-material', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/fracionar-material-page.php');
    } else {
        $app->notFound();
    }
});

$app->post('/fracionar-material-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $id_empresas = $_SESSION['usuario']['id_empresas'];
        $class_materiais_categorias = new MateriaisCategoriasModel();
        $arr_materiais_categorias = $class_materiais_categorias->loadAll($id_empresas, 'A');
        if ($arr_materiais_categorias) {
            foreach ($arr_materiais_categorias as $key => $value) {
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

$app->post('/fracionar-materiais', function() use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        
        $id_materiais = $app->request->post('id_materiais');
        $arr_quantidades = $app->request->post('arr_quantidades');

        if (!empty($id_materiais)) {
            
            $data = fracionarMateriais($id_materiais, $arr_quantidades);
            $status = $data['success'] ? 200 : 400;
            
        } else {
            $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
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