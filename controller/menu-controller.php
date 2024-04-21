<?php
$app->get('/controle-menu', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/menu-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/menu-edit/:id', function($id = '') use ($app){
    if (valida_logado()) {
        $data = array();
        try {
            $class_menu = new MenuModel();
            $arr = $class_menu->loadId($id);
            if ($arr) {
                $data = $arr;                
            }
        } catch (Exception $e) {
            $data = $e->getMessage();
        }

        $response = $app->response();
        $response['Access-Control-Allow-Origin'] = '*';
        $response['Access-Control-Allow-Methods'] = 'GET';
        $response['Content-Type'] = 'application/json';

        $response->status(200);
        $response->body(json_encode($data));

    } else {
        $app->notFound();
    }
});

$app->get('/menu-del/:id', function($id='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_menu = new MenuModel();

        if (!empty($id)) {
            $del = $class_menu->del($id);
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

$app->get('/menu-json', function() use ($app){
    if (valida_logado()) {
        $data['data'] = array();
        try {
            $class_menu = new MenuModel();
            $arr = $class_menu->loadAll();
            if ($arr) {
                foreach ($arr as $key => $value) {
                    $data['data'][] = $value;
                }
            }
        } catch (Exception $e) {
            $data = $e->getMessage();
        }

        $response = $app->response();
        $response['Access-Control-Allow-Origin'] = '*';
        $response['Access-Control-Allow-Methods'] = 'GET';
        $response['Content-Type'] = 'application/json';

        $response->status(200);
        $response->body(json_encode($data));

    } else {
        $app->notFound();
    }
});

$app->get('/menu-principal-json', function() use ($app){
    if (valida_logado()) {
        $data['data'] = array();
        try {
            $class_menu = new MenuModel();
            $arr = $class_menu->loadAllMenuPrincipal();
            if ($arr) {
                foreach ($arr as $key => $value) {
                    $data['data'][] = $value;
                }
            }
        } catch (Exception $e) {
            $data = $e->getMessage();
        }

        $response = $app->response();
        $response['Access-Control-Allow-Origin'] = '*';
        $response['Access-Control-Allow-Methods'] = 'GET';
        $response['Content-Type'] = 'application/json';

        $response->status(200);
        $response->body(json_encode($data));

    } else {
        $app->notFound();
    }
});

$app->post('/menu-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        try {
            $class_menu = new MenuModel();
            $post = $app->request->post();
            $arr = array();
            $id = '';
    
            foreach ($post as $key => $value) {
                $arr[substr($key, 5)] = $value;
            }
    
            $id = $arr['id_menu'];
            unset($arr['id_menu']);
            
            if (!empty($id)) {
                $data = $class_menu->edit($arr, array('id_menu'=>$id));
            } else {
                $data = $class_menu->add($arr);
            }
            
            $status = 200;
            $retorno = array(
                'success'=>true, 
                'type'=>'success', 
                'msg'=>messagesDefault(empty($id) ? 'register' : 'update'),
                'id'=>$id,
                'data'=>$data
            );

        } catch (Exception $e) {
            $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
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