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

// API compat (mobile app) - /app-marcas (GET|POST)
$app->map('/app-marcas', function() use ($app){
    $status = 200;
    $ret = ['success'=>false, 'data'=>[]];

    $logado = function_exists('valida_logado') ? valida_logado() : false;
    $id_usuario = null;
    if ($logado) {
        $id_usuario = $_SESSION['usuario']['id_usuarios'] ?? null;
    }
    if (!$id_usuario) {
        if (function_exists('_getHeaderValue')) {
            $token = _getHeaderValue('Token-User');
            if ($token) {
                try {
                    $pdo = $GLOBALS['pdo'];
                    $st = $pdo->prepare("SELECT id_usuarios FROM tb_usuarios WHERE hash = :h AND status = 'A' LIMIT 1");
                    $st->execute([':h' => $token]);
                    $row = $st->fetch(PDO::FETCH_ASSOC);
                    if ($row) $id_usuario = (int)$row['id_usuarios'];
                } catch (Exception $e) {}
            }
        }
    }

    if (!$id_usuario) {
        $status = 401;
        $ret = ['success'=>false, 'msg'=>'Não autorizado'];
    } else {
        try {
            $class_materiais_marcas = new MateriaisMarcasModel();

            $id_empresas = 0;
            if (function_exists('getIdEmpresasLogado')) $id_empresas = getIdEmpresasLogado();
            if (empty($id_empresas) && function_exists('_getHeaderValue')) $id_empresas = (int)_getHeaderValue('X-Company-Id');
            if (empty($id_empresas)) $id_empresas = (int)$app->request->params('id_empresas');

            $statusParam = $app->request->params('status') ?: '';

            $arr = $class_materiais_marcas->loadAll($id_empresas, $statusParam);
            $ret = ['success'=>true, 'data'=>($arr?:[])];
        } catch (Exception $e) {
            $status = 500;
            $ret = ['success'=>false, 'msg'=>'Erro ao listar marcas', 'detail'=>$e->getMessage()];
        }
    }

    while (ob_get_level()) { ob_end_clean(); }
    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Methods'] = 'GET, POST';
    $response['Content-Type'] = 'application/json';

    $response->status($status);
    $response->body(json_encode($ret));
})->via('GET','POST');

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