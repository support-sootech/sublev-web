<?php
$app->get('/modo-conservacao', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/modo-conservacao-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/modo-conservacao-edit/:id', function($id='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class = new ModoConservacaoModel();

        if (!empty($id)) {
            $arr = $class->loadId($id);
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

$app->get('/modo-conservacao-del/:id', function($id='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class = new ModoConservacaoModel();

        if (!empty($id)) {
            $del = $class->del($id);
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

$app->post('/modo-conservacao-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class = new ModoConservacaoModel();

        $status = (isset($_POST['status']) && !empty($_POST['status']) ? $_POST['status'] : '');
        $id_empresas = getIdEmpresasLogado();

        $arr = $class->loadAll($status, $id_empresas);
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

$app->post('/modo-conservacao-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('modo_conservacao_', '', $key))] = $value;
            }
    
            if (isset($post['id'])) {
                $id = $post['id'];
                unset($post['id']);
            }

            $post['id_empresas'] = getIdEmpresasLogado();

            try {
                $class = new ModoConservacaoModel();
    
                if (!empty($id)) {
                    $data = $class->edit($post, array('id'=>$id));
                } else {
                    $data = $class->add($post);
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