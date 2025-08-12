<?php
$app->get('/controle-materiais-marcas', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/materiais-marcas-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/materiais-marcas-edit/:id_materiais_marcas', function($id_materiais_marcas='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_materiais_marcas = new MateriaisMarcasModel();

        if (!empty($id_materiais_marcas)) {
            $arr = $class_materiais_marcas->loadId($id_materiais_marcas);
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

$app->get('/materiais-marcas-del/:id_materiais_marcas', function($id_materiais_marcas='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_materiais_marcas = new MateriaisMarcasModel();

        if (!empty($id_materiais_marcas)) {
            $del = $class_materiais_marcas->del($id_materiais_marcas);
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

$app->post('/materiais-marcas-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {

        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
    
            $class_materiais_marcas = new MateriaisMarcasModel();
            $arr = $class_materiais_marcas->loadAll($id_empresas, $status);
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

$app->post('/materiais-marcas-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_materiais_marcas = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('materiais_marcas_', '', $key))] = $value;
            }
    
            if (isset($post['id_materiais_marcas'])) {
                $id_materiais_marcas = $post['id_materiais_marcas'];
                unset($post['id_materiais_marcas']);
            }

            $post['id_empresas'] = $_SESSION['usuario']['id_empresas'];
            
            try {
                $class_embalagens_tipos = new MateriaisMarcasModel();
    
                if (!empty($id_materiais_marcas)) {
                    $data = $class_embalagens_tipos->edit($post, array('id_materiais_marcas'=>$id_materiais_marcas));
                } else {
                    $data = $class_embalagens_tipos->add($post);
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id_materiais_marcas) ? 'update' : 'register'),
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