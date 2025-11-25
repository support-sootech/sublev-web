<?php
$app->get('/controle-tipos-pessoas', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/tipos-pessoas-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/tipos-pessoas-edit/:id_tipos_pessoas', function($id_tipos_pessoas='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_tipos_pessoas = new TiposPessoasModel();

        if (!empty($id_tipos_pessoas)) {
            $arr = $class_tipos_pessoas->loadId($id_tipos_pessoas);
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

$app->get('/tipos-pessoas-del/:id_tipos_pessoas', function($id_tipos_pessoas='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_tipos_pessoas = new TiposPessoasModel();

        if (!empty($id_tipos_pessoas)) {
            $del = $class_tipos_pessoas->del($id_tipos_pessoas);
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

$app->post('/tipos-pessoas-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_tipos_pessoas = new TiposPessoasModel();
        $arr_tipos_pessoas = $class_tipos_pessoas->loadAll();
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

// API compat (mobile app) - /app-tipos-pessoas (GET|POST)
$app->map('/app-tipos-pessoas', function() use ($app){
    $status = 200;
    $ret = ['success'=>false, 'data'=>[]];
    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $class_tipos_pessoas = new TiposPessoasModel();
                $arr = $class_tipos_pessoas->loadAll();
                $ret = ['success'=>true, 'data'=>($arr?:[])];
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao listar tipos de pessoas', 'detail'=>$e->getMessage()];
            }
        } else {
            $status = 401;
            $ret = ['success'=>false, 'msg'=>'Não autorizado'];
        }
    }
    while (ob_get_level()) { ob_end_clean(); }
    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS';
    $response['Content-Type'] = 'application/json';
    $response->status($status);
    $response->body(json_encode($ret));
})->via('GET','POST','OPTIONS');

$app->post('/tipos-pessoas-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_tipos_pessoas = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('tipos-pessoas_', '', $key))] = $value;
            }
    
            if (isset($post['id_tipos_pessoas'])) {
                $id_tipos_pessoas = $post['id_tipos_pessoas'];
                unset($post['id_tipos_pessoas']);
            }        
            
            try {
                $class_tipos_pessoas = new TiposPessoasModel();
    
                if (!empty($id_tipos_pessoas)) {
                    $data = $class_tipos_pessoas->edit($post, array('id_tipos_pessoas'=>$id_tipos_pessoas));
                } else {
                    $data = $class_tipos_pessoas->add($post);
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id_tipos_pessoas) ? 'update' : 'register'),
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