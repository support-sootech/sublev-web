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

$app->map('/app-modo-conservacao', function() use ($app) {
  $status = 200; $ret = ['success'=>false, 'data'=>[]];

  $logado = function_exists('valida_logado') ? valida_logado() : false;
  $id_usuario = $logado ? ($_SESSION['usuario']['id_usuarios'] ?? null) : null;

  if (!$id_usuario) {
    // tenta por Token-User
    try {
      $token = $app->request->headers->get('Token-User');
      if ($token) {
        $pdo = $GLOBALS['pdo'];
        $st = $pdo->prepare("SELECT id_usuarios FROM tb_usuarios WHERE hash = :h AND status = 'A' LIMIT 1");
        $st->execute([':h' => $token]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row) $id_usuario = (int)$row['id_usuarios'];
      }
    } catch (Exception $e) {}
  }

  if (!$id_usuario) {
    $status = 401; $ret = ['success'=>false, 'msg'=>'Não autorizado'];
  } else {
    $id_empresas = function_exists('getIdEmpresasLogado') ? (int)(getIdEmpresasLogado() ?: 0) : 0;
    if ($id_empresas <= 0) {
      $hdr = $app->request->headers->get('X-Company-Id');
      if (!empty($hdr)) $id_empresas = (int)$hdr;
    }
    if ($id_empresas <= 0) {
      $param = $app->request->params('id_empresas');
      if (!empty($param)) $id_empresas = (int)$param;
    }

    if ($id_empresas <= 0) {
      $status = 400; $ret = ['success'=>false, 'msg'=>'Empresa não informada'];
    } else {
      try {
        $statusParam = $app->request->params('status') ?: 'A';
        $class = new ModoConservacaoModel();
        $arr   = $class->loadAll($statusParam, $id_empresas);
        $ret   = ['success'=>true, 'data'=>($arr ?: [])];
      } catch (Exception $e) {
        $status = 500; $ret = ['success'=>false, 'msg'=>'Erro ao listar modos', 'detail'=>$e->getMessage()];
      }
    }
  }

  $response = $app->response();
  $response['Access-Control-Allow-Origin']  = '*';
  $response['Access-Control-Allow-Methods'] = 'GET, POST';
  $response['Content-Type'] = 'application/json';
  $response->status($status);
  $response->body(json_encode($ret));
})->via('GET','POST');


?>