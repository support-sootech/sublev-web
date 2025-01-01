<?php
$app->get('/controle-empresas', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/empresas-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/empresas-edit/:id_empresas', function($id_empresas='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_empresas = new EmpresasModel();

        if (!empty($id_empresas)) {
            $arr = $class_empresas->loadId($id_empresas);
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

$app->get('/empresas-del/:id_empresas', function($id_empresas='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_empresas = new EmpresasModel();

        if (!empty($id_empresas)) {
            $del = $class_empresas->del($id_empresas);
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

$app->post('/empresas-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_empresas = new EmpresasModel();
        $arr_tipos_pessoas = $class_empresas->loadAll();
        if ($arr_tipos_pessoas) {
            foreach ($arr_tipos_pessoas as $key => $value) {
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

$app->post('/empresas-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_empresas = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('empresas_', '', $key))] = $value;
            }
    
            if (isset($post['id_empresas'])) {
                $id_empresas = $post['id_empresas'];
                unset($post['id_empresas']);
            }        
            
            try {
                $class_empresas = new EmpresasModel();
    
                if (!empty($id_empresas)) {
                    $data = $class_empresas->edit($post, array('id_empresas'=>$id_empresas));
                } else {
                    $data = $class_empresas->add($post);
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id_empresas) ? 'update' : 'register'),
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