<?php
$app->get('/controle-computadores', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/computadores-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/computadores-edit/:id', function($id='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_computadores = new ComputadoresModel();

        if (!empty($id)) {
            $arr = $class_computadores->loadId($id);
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

$app->get('/computadores-del/:id', function($id='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_computadores = new ComputadoresModel();

        if (!empty($id)) {
            $del = $class_computadores->del($id);
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

$app->post('/computadores-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    $arr = array();

    if (valida_logado()) {

        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
    
            $class_computadores = new ComputadoresModel();
            $arr = $class_computadores->loadAll($id_empresas, $status);
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

$app->post('/computadores-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('computadores_', '', $key))] = $value;
            }
    
            if (isset($post['id'])) {
                $id = $post['id'];
                unset($post['id']);
            }

            $post['id_empresas'] = $_SESSION['usuario']['id_empresas'];
            
            try {
                $class_embalagens_tipos = new ComputadoresModel();
    
                if (!empty($id)) {
                    $data = $class_embalagens_tipos->edit($post, array('id'=>$id));
                } else {
                    $data = $class_embalagens_tipos->add($post);
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id) ? 'update' : 'register'),
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