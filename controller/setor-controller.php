<?php
$app->get('/controle-setor', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/setor-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/setor-edit/:id_setor', function($id_setor='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_setor = new SetorModel();

        if (!empty($id_setor)) {
            $arr = $class_setor->loadId($id_setor);
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

$app->get('/setor-del/:id_setor', function($id_setor='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_setor = new SetorModel();

        if (!empty($id_setor)) {
            $del = $class_setor->del($id_setor);
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

$app->post('/setor-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_setor = new SetorModel();

        if (array_search('ROOT', array_column($_SESSION['usuario']['perfil'], 'ds_perfil')) !== false) {
            $id_empresas = '';
        } else {
            $id_empresas = getIdEmpresasLogado();
        }
        $status = (isset($_POST['status']) && !empty($_POST['status']) ? $_POST['status'] : '');

        $arr = $class_setor->loadAll($id_empresas, $status);
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

$app->post('/setor-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_setor = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('setor_', '', $key))] = $value;
            }
    
            if (isset($post['id_setor'])) {
                $id_setor = $post['id_setor'];
                unset($post['id_setor']);
            }

            $post['id_empresas'] = getIdEmpresasLogado();
            
            try {
                $class_setor = new SetorModel();
    
                if (!empty($id_setor)) {
                    $data = $class_setor->edit($post, array('id_setor'=>$id_setor));
                } else {
                    $data = $class_setor->add($post);
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id_setor) ? 'update' : 'register'),
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