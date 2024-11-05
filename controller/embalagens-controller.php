<?php
$app->get('/controle-embalagens', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/embalagens-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/embalagens-edit/:id_embalagens', function($id_embalagens='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_embalagens = new EmbalagensModel();

        if (!empty($id_embalagens)) {
            $arr = $class_embalagens->loadId($id_embalagens);
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

$app->get('/embalagens-del/:id_embalagens', function($id_embalagens='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_embalagens = new EmbalagensModel();

        if (!empty($id_embalagens)) {
            $del = $class_embalagens->del($id_embalagens);
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

$app->post('/embalagens-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {

        $id_empresas = $_SESSION['usuario']['id_empresas'];

        $class_embalagens = new EmbalagensModel();
        $arr = $class_embalagens->loadAll($id_empresas);
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

$app->post('/embalagens-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_embalagens = '';
            $post = array();
            $post['id_embalagens_tipos'] = $app->request->post('embalagens_id_embalagens_tipos');
            $post['id_empresas'] = getIdEmpresasLogado();
            
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('embalagens_', '', $key))] = $value;
            }
    
            if (isset($post['id_embalagens'])) {
                $id_embalagens = $post['id_embalagens'];
                unset($post['id_embalagens']);
            }

            try {
                $class_embalagens = new EmbalagensModel();
    
                if (!empty($id_embalagens)) {
                    $data = $class_embalagens->edit($post, array('id_embalagens'=>$id_embalagens));
                } else {
                    $data = $class_embalagens->add($post);
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id_embalagens) ? 'update' : 'register'),
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