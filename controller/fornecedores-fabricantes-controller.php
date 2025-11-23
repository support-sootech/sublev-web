<?php
$app->get('/controle-fornecedores-fabricantes', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/fornecedores-fabricantes-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/controle-fornecedores-fabricantes-teste', function() use ($app){
    if (valida_logado(false)) {
        
        try {
            $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();
            $fornecedor = $class_fornecedores_fabricantes->loadId(10);
            //verMatriz($class_fornecedores);
            unset($fornecedor['nome']);
            unset($fornecedor['tp_juridico']);
            $add = $class_fornecedores_fabricantes->add($fornecedor);
            verMatriz($add);
        } catch (Exception $e) {            
            verMatriz(array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage()));
        }

    } else {
        $app->notFound();
    }
});

// Compatível com app: aceita sessão logada OU header Token-User
$app->map('/fornecedores-fabricantes-edit/:id_pessoas', function($id_pessoas='') use ($app){
    $status = 200;
    $ret = ['success'=>false, 'data'=>[]];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        $logado = function_exists('valida_logado') ? valida_logado() : false;
        $id_usuario = null;
        if ($logado) {
            $id_usuario = $_SESSION['usuario']['id_usuarios'] ?? null;
        }
        if (!$id_usuario && function_exists('_getHeaderValue')) {
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

        if (!$id_usuario) {
            $status = 401;
            $ret = ['success'=>false, 'msg'=>'Não autorizado'];
        } else {
            try {
                $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();
                if (!empty($id_pessoas)) {
                    $arr = $class_fornecedores_fabricantes->loadId($id_pessoas);
                    if ($arr) {
                        $ret = ['success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$arr];
                    } else {
                        $status = 404;
                        $ret = ['success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found')];
                    }
                } else {
                    $status = 400;
                    $ret = ['success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found')];
                }
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao buscar pessoa', 'detail'=>$e->getMessage()];
            }
        }
    }

    while (ob_get_level()) { ob_end_clean(); }
    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Methods'] = 'GET, OPTIONS';
    $response['Content-Type'] = 'application/json';

    $response->status($status);
    $response->body(json_encode($ret));
})->via('GET','OPTIONS');

$app->get('/fornecedores-fabricantes-del/:id', function($id='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();

        if (!empty($id)) {
            $del = $class_fornecedores_fabricantes->del($id);
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

$app->post('/fornecedores-fabricantes-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();

        if (array_search('ROOT', array_column($_SESSION['usuario']['perfil'], 'ds_perfil')) !== false) {
            $id_empresas = '';
        } else {
            $id_empresas = getIdEmpresasLogado();
        }

        $id_tipos_pessoas = $app->request->post('id_tipos_pessoas');

        $data['data'] = $class_fornecedores_fabricantes->loadAll($id_empresas, '', $id_tipos_pessoas);
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

// API compat (mobile app) - /app-fornecedores (GET|POST)
$app->map('/app-fornecedores', function() use ($app){
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
            $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();

            // empresa: sessão -> header -> param (padrão). Se param all=1, ignora filtro empresa.
            $id_empresas = 0;
            if (function_exists('getIdEmpresasLogado')) $id_empresas = getIdEmpresasLogado();
            if (empty($id_empresas) && function_exists('_getHeaderValue')) $id_empresas = (int)_getHeaderValue('X-Company-Id');
            if (empty($id_empresas)) $id_empresas = (int)$app->request->params('id_empresas');
            $fg_all = $app->request->params('all') ?: $app->request->params('fg_all');
            if (!empty($fg_all) && $fg_all == '1') { $id_empresas = ''; }

            // status e tipo de pessoa (3=Fornecedor, 2=Fabricante).
            // Se não vier, não forçar (loadAll usa IN(2,3)), porém por padrão retornamos fornecedores (3)
            $status = $app->request->params('status') ?: '';
            $id_tipos_pessoas = $app->request->params('id_tipos_pessoas') ?: '';
            // Se não vier tipo, por padrão retornar fornecedores (id=3) para evitar lista vazia
            if (empty($id_tipos_pessoas)) { $id_tipos_pessoas = '3'; }

                // Lista fornecedores. Se all=1, lista sem filtro por empresa (como tela web root)
                $arr = $class_fornecedores_fabricantes->loadAll($id_empresas, $status, $id_tipos_pessoas);
                if ($arr === false) { $arr = []; }
            $ret = ['success'=>true, 'data'=>($arr?:[])];
        } catch (Exception $e) {
            $status = 500;
            $ret = ['success'=>false, 'msg'=>'Erro ao listar fornecedores', 'detail'=>$e->getMessage()];
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

// API compat (mobile app) - /app-fabricantes (GET|POST)
$app->map('/app-fabricantes', function() use ($app){
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
            $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();

            $id_empresas = 0;
            if (function_exists('getIdEmpresasLogado')) $id_empresas = getIdEmpresasLogado();
            if (empty($id_empresas) && function_exists('_getHeaderValue')) $id_empresas = (int)_getHeaderValue('X-Company-Id');
            if (empty($id_empresas)) $id_empresas = (int)$app->request->params('id_empresas');
            $fg_all = $app->request->params('all') ?: $app->request->params('fg_all');
            if (!empty($fg_all) && $fg_all == '1') { $id_empresas = ''; }

            // status e tipo de pessoa (3=Fornecedor, 2=Fabricante).
            // Requisito: este endpoint deve listar SOMENTE fabricantes (id_tipos_pessoas=2) por padrão,
            // reproduzindo a tela web (entrada de materiais) que apresenta lista filtrada.
            $status = $app->request->params('status') ?: ($app->request->post('status') ?: '');
            $id_tipos_pessoas_param = $app->request->params('id_tipos_pessoas') ?: ($app->request->post('id_tipos_pessoas') ?: '');

            // Se cliente não informar tipo, assumir '2'. Se informar explicitamente outro valor, usar.
            $id_tipos_pessoas = empty($id_tipos_pessoas_param) ? '2' : $id_tipos_pessoas_param;

            // Consulta fabricantes. Se all=1, ignora filtro por empresa.
            $arr = $class_fornecedores_fabricantes->loadAll($id_empresas, $status, $id_tipos_pessoas);
            // Caso vazio, manter lista vazia para refletir ausência de cadastro válido
            if ($arr === false) { $arr = []; }
            $ret = ['success'=>true, 'data'=>($arr?:[])];
        } catch (Exception $e) {
            $status = 500;
            $ret = ['success'=>false, 'msg'=>'Erro ao listar fabricantes', 'detail'=>$e->getMessage()];
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

$app->post('/fornecedores-fabricantes-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            
            $obj_pessoas = array();
            
            try {
                $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();

                $obj_pessoas['id_pessoas'] = $app->request->post('fornecedores_fabricantes_id_pessoas');
                $obj_pessoas['nome'] = $app->request->post('fornecedores_fabricantes_nome');
                $obj_pessoas['tp_juridico'] = $app->request->post('fornecedores_fabricantes_tp_juridico');
                $obj_pessoas['cpf_cnpj'] = $app->request->post('fornecedores_fabricantes_cpf_cnpj');
                $obj_pessoas['status'] = $app->request->post('fornecedores_fabricantes_status');
                $obj_pessoas['email'] = $app->request->post('fornecedores_fabricantes_email');
                $obj_pessoas['dt_nascimento'] = $app->request->post('fornecedores_fabricantes_dt_nascimento');
                $obj_pessoas['id_tipos_pessoas'] = $app->request->post('fornecedores_fabricantes_id_tipos_pessoas');

                $obj_pessoas['id_empresas'] = getIdEmpresasLogado();
                
                $obj_pessoas['telefone'] = $app->request->post('fornecedores_fabricantes_telefone');
                $obj_pessoas['cep'] = $app->request->post('fornecedores_fabricantes_cep');
                $obj_pessoas['logradouro'] = $app->request->post('fornecedores_fabricantes_logradouro');
                $obj_pessoas['numero'] = $app->request->post('fornecedores_fabricantes_numero');
                $obj_pessoas['complemento'] = $app->request->post('fornecedores_fabricantes_complemento');
                $obj_pessoas['bairro'] = $app->request->post('fornecedores_fabricantes_bairro');
                $obj_pessoas['cidade'] = $app->request->post('fornecedores_fabricantes_cidade');
                $obj_pessoas['estado'] = $app->request->post('fornecedores_fabricantes_estado');
                $obj_pessoas['cod_ibge'] = $app->request->post('fornecedores_fabricantes_cod_ibge');
                
                $id_pessoas = $obj_pessoas['id_pessoas'];
                //unset($obj_pessoas['id_pessoas']);
                if (empty($id_pessoas)) {
                    $data = $class_fornecedores_fabricantes->addFornecedoresFabricantes($obj_pessoas);
                    $id_pessoas = $data;
                } else {
                    $data = $class_fornecedores_fabricantes->edit($obj_pessoas, array('id_pessoas'=>$id_pessoas));
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(isset($obj_pessoas['id_pessoas']) && !empty($obj_pessoas['id_pessoas']) ? 'update' : 'register'),
                        'data'=>$data,
                        'obj_pessoas'=>$obj_pessoas,
                    );
                } else {
                    $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$data);    
                }   
            } catch (Exception $e) {
                $msg = messagesDefault($e->getCode());
                if (empty($msg)) {
                    $msg = $e->getMessage();
                }
                $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$msg);
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