<?php
$app->get('/controle-unidades-medidas', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/unidades-medidas-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/unidades-medidas-edit/:id_unidades_medidas', function($id_unidades_medidas='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_unidades_medidas = new UnidadesMedidasModel();

        if (!empty($id_unidades_medidas)) {
            $arr = $class_unidades_medidas->loadId($id_unidades_medidas);
            if ($arr) {
                $status = 200;
                $data = array('success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$arr);
            } else {
                $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
            }
        } else {
            $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
        }
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'GET';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->get('/unidades-medidas-del/:id_unidades_medidas', function($id_unidades_medidas='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_unidades_medidas = new UnidadesMedidasModel();

        if (!empty($id_unidades_medidas)) {
            $del = $class_unidades_medidas->del($id_unidades_medidas);
            if ($del) {
                $status = 200;
                $data = array('success'=>true, 'type'=>'success', 'msg'=>messagesDefault('delete'));
            } else {
                $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
            }
        } else {
            $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
        }
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'GET';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->post('/unidades-medidas-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {

        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
    
            $class_unidades_medidas = new UnidadesMedidasModel();
            $arr = $class_unidades_medidas->loadAll($id_empresas, $status);
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

$app->post('/unidades-medidas-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_unidades_medidas = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('unidades_medidas_', '', $key))] = $value;
            }
    
            if (isset($post['id_unidades_medidas'])) {
                $id_unidades_medidas = $post['id_unidades_medidas'];
                unset($post['id_unidades_medidas']);
            }

            $post['id_empresas'] = $_SESSION['usuario']['id_empresas'];
            
            try {
                $class_embalagens_tipos = new UnidadesMedidasModel();
    
                if (!empty($id_unidades_medidas)) {
                    $data = $class_embalagens_tipos->edit($post, array('id_unidades_medidas'=>$id_unidades_medidas));
                } else {
                    $data = $class_embalagens_tipos->add($post);
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id_unidades_medidas) ? 'update' : 'register'),
                        'data'=>$data
                    );
                } else {
                    $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$data);    
                }   
            } catch (Exception $e) {
                $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
            }
        }
        
        
    } else {
        $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>'Método incorreto!');
    }

	$response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($retorno));
});


?>