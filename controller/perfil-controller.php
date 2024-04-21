<?php
$app->get('/controle-perfil', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/perfil-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/perfil-edit/:id_perfil', function($id_perfil='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_perfil = new PerfilModel();

        if (!empty($id_perfil)) {
            $arr = $class_perfil->loadId($id_perfil);
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

$app->get('/perfil-del/:id_perfil', function($id_perfil='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_perfil = new PerfilModel();

        if (!empty($id_perfil)) {
            $del = $class_perfil->del($id_perfil);
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

$app->post('/perfil-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_perfil = new PerfilModel();
        $arr_perfil = $class_perfil->loadAll();
        if ($arr_perfil) {
            foreach ($arr_perfil as $key => $value) {
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

$app->post('/perfil-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_perfil = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('perfil_', '', $key))] = $value;
            }
    
            if (isset($post['id_perfil'])) {
                $id_perfil = $post['id_perfil'];
                unset($post['id_perfil']);
            }        
            
            try {
                $class_perfil = new PerfilModel();
    
                if (!empty($id_perfil)) {
                    $data = $class_perfil->edit($post, array('id_perfil'=>$id_perfil));
                } else {
                    $data = $class_perfil->add($post);
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

$app->get('/perfil-menu-permissao', function() use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_perfil = new PerfilModel();
        $class_menu = new MenuModel();
        $class_permissoes = new PermissoesModel();
        $arr_permissoes = $class_permissoes->loadAll(false);

        try {
            $data = array(
                'success'=>true, 
                'type'=>'success', 
                'msg'=>messagesDefault('OK'), 
                'data'=>$class_menu->menuPerfilSistema(),
                'permissoes'=>$arr_permissoes,
            );
        } catch (Exception $e) {
            $data = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
        }
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'GET';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

?>