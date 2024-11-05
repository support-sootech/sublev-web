<?php
$app->get('/controle-permissoes', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/permissoes-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/permissoes-edit/:id', function($id='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_permissoes = new PermissoesModel();

        if (!empty($id)) {
            $arr = $class_permissoes->loadId($id);
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

$app->get('/permissoes-del/:id', function($id='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_permissoes = new PermissoesModel();

        if (!empty($id)) {
            $del = $class_permissoes->del($id);
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

$app->post('/permissoes-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_permissoes = new PermissoesModel();
        $arr = $class_permissoes->loadAll();
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

$app->post('/permissoes-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {
        
        $id = '';
        $post = array();

        foreach ($app->request->post() as $key => $value) {
            $post[(str_replace('permissoes_', '', $key))] = $value;
        }

        if (isset($post['id_permissoes'])) {
            $id = $post['id_permissoes'];
            unset($post['id_permissoes']);
        }        
        
        try {
            $class_permissoes = new PermissoesModel();

            if (!empty($id)) {
                $data = $class_permissoes->edit($post, array('id_permissoes'=>$id));
            } else {
                $data = $class_permissoes->add($post);
            }
            
            if ($data) {
                $status = 200;
                $retorno = array(
                    'success'=>true, 
                    'type'=>'success', 
                    'msg'=>messagesDefault(!empty($id_perfil) ? 'update' : 'register'),
                    'data'=>$data
                );
            } else {
                $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$data);    
            }   
        } catch (Exception $e) {
            $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
        }
        
    } else {
        $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('incorrect_method'));
    }

	$response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($retorno));
});
?>