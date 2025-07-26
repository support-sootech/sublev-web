<?php
$app->get('/controle-materiais-categorias', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/materiais-categorias-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/materiais-categorias-edit/:id_materiais_categorias', function($id_materiais_categorias='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_materiais_categorias = new MateriaisCategoriasModel();

        if (!empty($id_materiais_categorias)) {
            $arr = $class_materiais_categorias->loadId($id_materiais_categorias);
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

$app->get('/materiais-categorias-del/:id_materiais_categorias', function($id_materiais_categorias='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_materiais_categorias = new MateriaisCategoriasModel();

        if (!empty($id_materiais_categorias)) {
            $del = $class_materiais_categorias->del($id_materiais_categorias);
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

$app->post('/materiais-categorias-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {

        try {
            $id_empresas = getIdEmpresasLogado();

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
    
            $class_materiais_categorias = new MateriaisCategoriasModel();
            $arr = $class_materiais_categorias->loadAll($id_empresas, $status);
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

$app->post('/materiais-categorias-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_materiais_categorias = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('materiais_categorias_', '', $key))] = $value;
            }
    
            if (isset($post['id_materiais_categorias'])) {
                $id_materiais_categorias = $post['id_materiais_categorias'];
                unset($post['id_materiais_categorias']);
            }

            $post['id_empresas'] = getIdEmpresasLogado();
            
            try {
                $class_embalagens_tipos = new MateriaisCategoriasModel();
    
                if (!empty($id_materiais_categorias)) {
                    $data = $class_embalagens_tipos->edit($post, array('id_materiais_categorias'=>$id_materiais_categorias));
                } else {
                    $data = $class_embalagens_tipos->add($post);
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id_materiais_categorias) ? 'update' : 'register'),
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