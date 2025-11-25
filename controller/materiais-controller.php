<?php
$app->get('/controle-materiais', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/materiais-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/materiais-edit/:id_materiais', function($id_materiais='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_materiais = new MateriaisModel();

        if (!empty($id_materiais)) {
            $arr = $class_materiais->loadId($id_materiais);
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

$app->get('/materiais-del/:id_materiais', function($id_materiais='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_materiais = new MateriaisModel();

        if (!empty($id_materiais)) {
            $del = $class_materiais->del($id_materiais);
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

$app->post('/materiais-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        
        try {
            $id_empresas = getIdEmpresasLogado();
            if (empty($id_empresas) && function_exists('_getHeaderValue')) {
                $hdr = _getHeaderValue('X-Company-Id');
                if (!empty($hdr)) $id_empresas = (int)$hdr;
            }

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
    
            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadAll($status, $id_empresas);
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

// API compat (mobile app) - /app-materiais (GET|POST)
$app->map('/app-materiais', function() use ($app){
    $status = 200;
    $ret = ['success'=>false, 'data'=>[]];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                // aceita tanto GET params quanto POST
                $statusParam = $app->request->params('status') ?: ($app->request->post('status') ?: '');
                $id_empresas = function_exists('getIdEmpresasLogado') ? getIdEmpresasLogado() : 0;
                if (empty($id_empresas) && function_exists('_getHeaderValue')) {
                    $hdr = _getHeaderValue('X-Company-Id');
                    if (!empty($hdr)) $id_empresas = (int)$hdr;
                }

                $class_materiais = new MateriaisModel();
                $arr = $class_materiais->loadAll($statusParam, $id_empresas);
                $ret = ['success'=>true, 'data'=>($arr?:[])];
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao listar materiais', 'detail'=>$e->getMessage()];
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

// API compat (mobile app) - /app-materiais-etiquetas-e (GET|POST)
$app->map('/app-materiais-etiquetas-e', function() use ($app){
    $status = 200;
    $ret = ['success'=>false, 'data'=>[]];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $statusParam = $app->request->params('status') ?: ($app->request->post('status') ?: '');
                $id_empresas = function_exists('getIdEmpresasLogado') ? getIdEmpresasLogado() : 0;
                if (empty($id_empresas) && function_exists('_getHeaderValue')) {
                    $hdr = _getHeaderValue('X-Company-Id');
                    if (!empty($hdr)) $id_empresas = (int)$hdr;
                }

                $class_materiais = new MateriaisModel();
                $arr = $class_materiais->loadAllComEtiquetaTipoE($statusParam, $id_empresas);
                $ret = ['success'=>true, 'data'=>($arr?:[])];
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao listar materiais com etiquetas tipo E', 'detail'=>$e->getMessage()];
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

// API compat (mobile app) - /app-materiais-edit (GET)
$app->map('/app-materiais-edit/:id_materiais', function($id_materiais='') use ($app){
    $status = 200;
    $ret = ['success'=>false, 'data'=>[]];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $class_materiais = new MateriaisModel();
                if (!empty($id_materiais)) {
                    $arr = $class_materiais->loadId($id_materiais);
                    if ($arr) {
                        $ret = ['success'=>true, 'data'=>$arr];
                    } else {
                        $status = 404;
                        $ret = ['success'=>false, 'msg'=>'Registro não encontrado'];
                    }
                } else {
                    $status = 400;
                    $ret = ['success'=>false, 'msg'=>'ID inválido'];
                }
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao buscar material', 'detail'=>$e->getMessage()];
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

// API compat (mobile app) - /app-materiais-del/:id (GET|POST)
$app->map('/app-materiais-del/:id_materiais', function($id_materiais='') use ($app){
    $status = 400;
    $ret = ['success'=>false];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                if (!empty($id_materiais)) {
                    $class_materiais = new MateriaisModel();
                    $del = $class_materiais->del($id_materiais);
                    if ($del) {
                        $status = 200;
                        $ret = ['success'=>true, 'msg'=>messagesDefault('delete')];
                    } else {
                        $status = 404;
                        $ret = ['success'=>false, 'msg'=>'Registro não encontrado'];
                    }
                } else {
                    $status = 400;
                    $ret = ['success'=>false, 'msg'=>'ID inválido'];
                }
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao deletar material', 'detail'=>$e->getMessage()];
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

// API compat (mobile app) - /app-materiais-save (POST)
$app->map('/app-materiais-save', function() use ($app){
    $status = 400;
    $retorno = ['success'=>false];

    if ($app->request->isOptions()) {
        $status = 200;
        $retorno = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            // Support both form-encoded and raw JSON payloads
            $raw = $app->request->getBody();
            $post = array();
            $decoded = @json_decode($raw, true);
            if (is_array($decoded)) {
                // accept both keys with or without 'material_' prefix
                foreach ($decoded as $k => $v) {
                    if (strpos($k, 'material_') === 0) $post[str_replace('material_', '', $k)] = $v;
                    else $post[$k] = $v;
                }
            } else {
                foreach ($app->request->post() as $key => $value) {
                    $post[(str_replace('material_', '', $key))] = $value;
                }
            }

            if (!isset($post['status'])) $post['status'] = 'A';

            // detect company id from session or header
            $post['id_empresas'] = function_exists('getIdEmpresasLogado') ? getIdEmpresasLogado() : 0;
            if (empty($post['id_empresas']) && function_exists('_getHeaderValue')) {
                $hdr = _getHeaderValue('X-Company-Id');
                if (!empty($hdr)) $post['id_empresas'] = (int)$hdr;
            }

            // Validações por campo (checar existência de FK antes de tentar salvar)
            $fieldErrors = array();

            try {
                // Unidades de medida
                if (isset($post['id_unidades_medidas']) && !empty($post['id_unidades_medidas'])) {
                    try {
                        $class_um = new UnidadesMedidasModel();
                        $fg = $class_um->loadId($post['id_unidades_medidas']);
                        if (!$fg) $fieldErrors['id_unidades_medidas'] = 'Unidade de medida não encontrada';
                    } catch (Exception $e) {
                        $fieldErrors['id_unidades_medidas'] = 'Erro ao validar unidade de medida';
                    }
                }

                // Marca
                if (isset($post['id_materiais_marcas']) && !empty($post['id_materiais_marcas'])) {
                    try {
                        $class_mm = new MateriaisMarcasModel();
                        $fg = $class_mm->loadId($post['id_materiais_marcas']);
                        if (!$fg) $fieldErrors['id_materiais_marcas'] = 'Marca não encontrada';
                    } catch (Exception $e) {
                        $fieldErrors['id_materiais_marcas'] = 'Erro ao validar marca';
                    }
                }

                // Categoria
                if (isset($post['id_materiais_categorias']) && !empty($post['id_materiais_categorias'])) {
                    try {
                        $class_mc = new MateriaisCategoriasModel();
                        $fg = $class_mc->loadId($post['id_materiais_categorias']);
                        if (!$fg) $fieldErrors['id_materiais_categorias'] = 'Categoria não encontrada';
                    } catch (Exception $e) {
                        $fieldErrors['id_materiais_categorias'] = 'Erro ao validar categoria';
                    }
                }

                // Fornecedor
                if (isset($post['id_pessoas_fornecedor']) && !empty($post['id_pessoas_fornecedor'])) {
                    try {
                        $class_p = new PessoasModel();
                        $fg = $class_p->loadId($post['id_pessoas_fornecedor']);
                        // loadId may return false (not found) or a string (error message). Treat both as validation failures.
                        if ($fg === false) {
                            $fieldErrors['id_pessoas_fornecedor'] = 'Fornecedor não encontrado';
                        } elseif (is_string($fg)) {
                            $fieldErrors['id_pessoas_fornecedor'] = 'Erro ao validar fornecedor';
                        }
                    } catch (Exception $e) {
                        $fieldErrors['id_pessoas_fornecedor'] = 'Erro ao validar fornecedor';
                    }
                }

                // Fabricante (validação usando FornecedoresFabricantesModel para
                // permitir pessoas sem empresa vinculada e garantir que o registro
                // é do tipo fabricante/fornecedor)
                if (isset($post['id_pessoas_fabricante']) && !empty($post['id_pessoas_fabricante'])) {
                    try {
                        $class_ff = new FornecedoresFabricantesModel();
                        $fg = $class_ff->loadId($post['id_pessoas_fabricante']);
                        if ($fg === false) {
                            $fieldErrors['id_pessoas_fabricante'] = 'Fabricante não encontrado';
                        } elseif (is_string($fg)) {
                            $fieldErrors['id_pessoas_fabricante'] = 'Erro ao validar fabricante';
                        }
                    } catch (Exception $e) {
                        $fieldErrors['id_pessoas_fabricante'] = 'Erro ao validar fabricante';
                    }
                }

                // Modo de conservação
                if (isset($post['id_modo_conservacao']) && !empty($post['id_modo_conservacao'])) {
                    try {
                        $class_mc2 = new ModoConservacaoModel();
                        $fg = $class_mc2->loadId($post['id_modo_conservacao']);
                        if (!$fg) $fieldErrors['id_modo_conservacao'] = 'Modo de conservação não encontrado';
                    } catch (Exception $e) {
                        $fieldErrors['id_modo_conservacao'] = 'Erro ao validar modo de conservação';
                    }
                }

                // Condição de embalagem
                if (isset($post['id_embalagem_condicoes']) && !empty($post['id_embalagem_condicoes'])) {
                    try {
                        $class_ec = new EmbalagemCondicoesModel();
                        $fg = $class_ec->loadId($post['id_embalagem_condicoes']);
                        if (!$fg) $fieldErrors['id_embalagem_condicoes'] = 'Condição de embalagem não encontrada';
                    } catch (Exception $e) {
                        $fieldErrors['id_embalagem_condicoes'] = 'Erro ao validar condição de embalagem';
                    }
                }

                // Se houver erros de validação, retornar imediatamente com HTTP 422
                if (!empty($fieldErrors)) {
                    $status = 422;
                    $retorno = ['success'=>false, 'msg'=>'Validação de campos', 'data'=>$fieldErrors];
                } else {
                    // prosseguir com o salvamento
                    $class_materiais = new MateriaisModel();
                    $id_materiais = '';
                    if (isset($post['id_materiais'])) {
                        $id_materiais = $post['id_materiais'];
                        unset($post['id_materiais']);
                    }

                    // Responsável: sempre tentar identificar o usuário logado (sessão ou token) para registrar no material
                    $usuarioLogado = getUsuario($app);
                    $idUsuarioResponsavel = 0;
                    if (is_array($usuarioLogado) && isset($usuarioLogado['id_usuarios'])) {
                        $idUsuarioResponsavel = (int)$usuarioLogado['id_usuarios'];
                    } elseif (isset($_SESSION['usuario']['id_usuarios'])) {
                        $idUsuarioResponsavel = (int)$_SESSION['usuario']['id_usuarios'];
                    }

                    if (!empty($id_materiais)) {
                        if ($idUsuarioResponsavel > 0) {
                            $post['id_usuarios'] = $idUsuarioResponsavel;
                        }
                        $data = $class_materiais->edit($post, array('id_materiais'=>$id_materiais));
                    } else {
                        $post['id_usuarios'] = $idUsuarioResponsavel;
                        $data = $class_materiais->add($post);
                        if ($idUsuarioResponsavel === 0) {
                            error_log('[MATERIAIS_SAVE][DEBUG] id_usuarios não resolvido para inserção (app). Payload descricao='.(isset($post['descricao'])?$post['descricao']:'').', empresa='.(isset($post['id_empresas'])?$post['id_empresas']:'').');');
                        }
                    }

                    if ($data) {
                        $status = 200;
                        $retorno = ['success'=>true, 'msg'=>messagesDefault(!empty($id_materiais) ? 'update' : 'register'), 'data'=>$data];
                    } else {
                        $retorno = ['success'=>false, 'msg'=>'Falha ao salvar', 'data'=>$data];
                    }
                }
            } catch (Exception $e) {
                $status = 500;
                $retorno = ['success'=>false, 'msg'=>$e->getMessage()];
            }
        } else {
            $status = 401;
            $retorno = ['success'=>false, 'msg'=>'Não autorizado'];
        }
    }

            // Se o retorno contém uma mensagem de SQL (erro de integridade), padronizar resposta
            if (isset($retorno['data']) && is_string($retorno['data']) && stripos($retorno['data'], 'SQLSTATE') !== false) {
                $status = 500;
                $retDetail = $retorno['data'];
                $retorno = ['success'=>false, 'msg'=>'Erro de banco de dados durante o salvamento', 'detail'=>$retDetail];
            }

    while (ob_get_level()) { ob_end_clean(); }
    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Methods'] = 'POST, OPTIONS';
    $response['Content-Type'] = 'application/json';
    $response->status($status);
    $response->body(json_encode($retorno));
})->via('POST','OPTIONS');

$app->map('/app-materiais-json', function() use ($app){
    $status = 200;
    $ret = ['success'=>false, 'data'=>[]];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $statusParam = $app->request->params('status') ?: ($app->request->post('status') ?: '');
                $id_empresas = function_exists('getIdEmpresasLogado') ? getIdEmpresasLogado() : 0;
                if (empty($id_empresas) && function_exists('_getHeaderValue')) {
                    $hdr = _getHeaderValue('X-Company-Id');
                    if (!empty($hdr)) $id_empresas = (int)$hdr;
                }

                $class_materiais = new MateriaisModel();
                $arr = $class_materiais->loadAll($statusParam, $id_empresas);
                $ret = ['success'=>true, 'data'=>($arr?:[])];
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao listar materiais', 'detail'=>$e->getMessage()];
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

$app->post('/materiais-da-categoria-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
   
    if (valida_logado()) {
        
        try {
            $id_empresas = getIdEmpresasLogado();

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
            
            $id_materiais_categorias = '';
            if ($app->request->post('id_materiais_categorias')) {
                $id_materiais_categorias = $app->request->post('id_materiais_categorias');
            }
    
            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadIdMaterialCategoria($status,$id_materiais_categorias, $id_empresas);
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

// API compat (mobile app) - /app-materiais-da-categoria (GET|POST)
$app->map('/app-materiais-da-categoria', function() use ($app){
    $status = 200;
    $ret = ['success'=>false, 'data'=>[]];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $statusParam = $app->request->params('status') ?: ($app->request->post('status') ?: '');
                $id_materiais_categorias = $app->request->params('id_materiais_categorias') ?: $app->request->post('id_materiais_categorias');
                $id_empresas = function_exists('getIdEmpresasLogado') ? getIdEmpresasLogado() : 0;
                if (empty($id_empresas) && function_exists('_getHeaderValue')) {
                    $hdr = _getHeaderValue('X-Company-Id');
                    if (!empty($hdr)) $id_empresas = (int)$hdr;
                }

                $class_materiais = new MateriaisModel();
                $arr = $class_materiais->loadIdMaterialCategoria($statusParam, $id_materiais_categorias, $id_empresas);
                $ret = ['success'=>true, 'data'=>($arr?:[])];
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao listar materiais por categoria', 'detail'=>$e->getMessage()];
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

$app->post('/detalhes-materiais-json', function() use ($app){
    $status = 200;
	$data = array();
   
    if (valida_logado()) {
        
        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
            
            $id_materiais = '';
            if ($app->request->post('id_materiais')) {
                $id_materiais = $app->request->post('id_materiais');
            }
    
            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadIdMaterialDetalhes($status,$id_materiais, $id_empresas);
            if ($arr) {
                if(!empty($arr['peso'])) {
                    $arr['peso'] = numberformat($arr['peso'], false);
                }

                $data = $arr;
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

// API compat (mobile app) - /app-detalhes-materiais (GET|POST)
$app->map('/app-detalhes-materiais', function() use ($app){
    $status = 200;
    $ret = [];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $id_materiais = $app->request->params('id_materiais') ?: $app->request->post('id_materiais');
                $id_empresas = isset($_SESSION['usuario']['id_empresas']) ? $_SESSION['usuario']['id_empresas'] : 0;
                if (empty($id_empresas) && function_exists('_getHeaderValue')) {
                    $hdr = _getHeaderValue('X-Company-Id');
                    if (!empty($hdr)) $id_empresas = (int)$hdr;
                }

                $class_materiais = new MateriaisModel();
                $arr = $class_materiais->loadIdMaterialDetalhes('', $id_materiais, $id_empresas);
                if ($arr) {
                    if(!empty($arr['peso'])) $arr['peso'] = numberformat($arr['peso'], false);
                    $ret = ['success'=>true, 'data'=>$arr];
                } else {
                    $ret = ['success'=>false, 'msg'=>'Nenhum material localizado'];
                    $status = 404;
                }
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao buscar detalhes do material', 'detail'=>$e->getMessage()];
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

$app->post('/materiais-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_materiais = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('material_', '', $key))] = $value;
            }
    
            if (isset($post['id_materiais'])) {
                $id_materiais = $post['id_materiais'];
                unset($post['id_materiais']);
            } 
            
            if (!isset($post['status'])) {
                $post['status'] = 'A';
            }

            $post['id_empresas'] = getIdEmpresasLogado();
            
            try {
                $class_materiais = new MateriaisModel();
    
                if (!empty($id_materiais)) {
                    // Em edição, se existir usuário logado, manter/atualizar responsável
                    if (isset($_SESSION['usuario']['id_usuarios']) && $_SESSION['usuario']['id_usuarios'] > 0) {
                        $post['id_usuarios'] = $_SESSION['usuario']['id_usuarios'];
                    }
                    $data = $class_materiais->edit($post, array('id_materiais'=>$id_materiais));
                } else {
                    $idUsuarioSessao = (isset($_SESSION['usuario']['id_usuarios']) ? (int)$_SESSION['usuario']['id_usuarios'] : 0);
                    $post['id_usuarios'] = $idUsuarioSessao;
                    $data = $class_materiais->add($post);
                    if ($idUsuarioSessao === 0) {
                        error_log('[MATERIAIS_SAVE_WEB][DEBUG] id_usuarios não resolvido para inserção (web). descricao='.(isset($post['descricao'])?$post['descricao']:'').', empresa='.(isset($post['id_empresas'])?$post['id_empresas']:'').');');
                    }
                }
                
                if ($data) {

                    $class_produtos = new ProdutosModel();
                    $add_produto = false;
                    $fg_produto = $class_produtos->loadCodigoBarras($post['cod_barras']);
                    if (!$fg_produto) {
                        $class_produtos->add(
                            array(
                                'descricao'=>$post['descricao'],
                                'codigo_barras'=>$post['cod_barras'],
                                'dias_vencimento'=>$post['dias_vencimento'],
                                'dias_vencimento_aberto'=>$post['dias_vencimento_aberto'],
                                'peso'=>$post['peso'],
                                'id_unidades_medidas'=>$post['id_unidades_medidas'],
                                'id_materiais_marcas'=>$post['id_materiais_marcas'],
                                'id_materiais_tipos'=>(isset($post['id_materiais_tipos']) ? $post['id_materiais_tipos'] : ''),
                                'id_materiais_categorias'=>$post['id_materiais_categorias'],
                                'id_pessoas_fabricante'=>$post['id_pessoas_fabricante'],
                                'id_modo_conservacao'=>$post['id_modo_conservacao'],
                                'status'=>'A'
                            )
                        );
                    }

                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id_materiais) ? 'update' : 'register'),
                        'data'=>$data,
                        'produto'=>$add_produto
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

// API compat (mobile app) - /app-relatorio-materiais-recebimento (GET)
$app->map('/app-relatorio-materiais-recebimento', function() use ($app){
    $status = 400;
    $ret = ['success'=>false];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $params = $app->request->params();
                $dt_ini = isset($params['dt_ini']) ? $params['dt_ini'] : '';
                $dt_fim = isset($params['dt_fim']) ? $params['dt_fim'] : '';
                $statusFiltro = isset($params['status']) ? $params['status'] : '';
                $tipoSaida = isset($params['tipo']) ? $params['tipo'] : '';
                $busca = isset($params['busca']) ? $params['busca'] : '';

                // Default range: últimos 7 dias se não informado
                if (empty($dt_ini) || empty($dt_fim)) {
                    $dt_fim = date('d/m/Y');
                    $dt_ini = date('d/m/Y', strtotime('-7 days'));
                }

                // Empresa: sessão ou header
                $id_empresas = function_exists('getIdEmpresasLogado') ? getIdEmpresasLogado() : 0;
                if (empty($id_empresas) && function_exists('_getHeaderValue')) {
                    $hdr = _getHeaderValue('X-Company-Id');
                    if (!empty($hdr)) $id_empresas = (int)$hdr;
                }

                $class_materiais = new MateriaisModel();
                $dados = $class_materiais->loadRelatorioMateriaisRecebimento($id_empresas, $dt_ini, $dt_fim, $statusFiltro, $busca);
                if ($dados) {
                    // Normalizações leves para app
                    foreach ($dados as &$row) {
                        if (isset($row['quantidade'])) $row['quantidade'] = (string)(int)$row['quantidade'];
                        if (isset($row['dh_cadastro'])) $row['dh_cadastro'] = date('d/m/Y H:i', strtotime($row['dh_cadastro']));
                        if (isset($row['dt_vencimento']) && !empty($row['dt_vencimento'])) {
                            $row['dt_vencimento'] = date('d/m/Y', strtotime($row['dt_vencimento']));
                        }
                        if (!isset($row['nm_responsavel']) || empty($row['nm_responsavel'])) {
                            $row['nm_responsavel'] = ''; // manter chave presente
                        }
                    }
                    if ($tipoSaida === 'pdf') {
                        $usuarioInfo = getUsuario($app);
                        $nmEmpresa = '';
                        $nmResponsavel = '';
                        if (is_array($usuarioInfo)) {
                            $nmEmpresa = $usuarioInfo['nm_empresa'] ?? '';
                            $nmResponsavel = $usuarioInfo['nm_pessoa'] ?? '';
                        } else if (isset($_SESSION['usuario'])) {
                            $nmEmpresa = $_SESSION['usuario']['nm_empresa'] ?? '';
                            $nmResponsavel = $_SESSION['usuario']['nm_pessoa'] ?? '';
                        }

                        $table = '<h4 style="text-align:center">PLANILHA DE CONTROLE DE RECEBIMENTO DE PRODUTOS PERECÍVEIS (CONGELADOS / RESFRIADOS)</h4>';
                        $table.= '<h5 style="text-align:center">ESTABELECIMENTO: '.$nmEmpresa.' - RESPONSÁVEL: '.$nmResponsavel.' - PERÍODO '.$dt_ini.' até '.$dt_fim.' </h5>';
                        $table.= '<h6 style="text-align:center">gerado: '.date('d/m/Y H:i:s').'</h6>';
                        $table.= '<table style="width:100%;border: 1px solid black;border-collapse: collapse; font-size:12px">';
                        $table.= '<thead><tr>';
                        $table.= '<th style="border:1px solid">DATA / HORA</th>';
                        $table.= '<th style="border:1px solid">MATERIAL</th>';
                        $table.= '<th style="border:1px solid">FORNECEDOR</th>';
                        $table.= '<th style="border:1px solid">VALIDADE</th>';
                        $table.= '<th style="border:1px solid">QTDE</th>';
                        $table.= '<th style="border:1px solid">TEMPERATURA</th>';
                        $table.= '<th style="border:1px solid">SIF</th>';
                        $table.= '<th style="border:1px solid">LOTE</th>';
                        $table.= '<th style="border:1px solid">Nº NOTA</th>';
                        $table.= '<th style="border:1px solid">CONDIÇÕES EMB.</th>';
                        $table.= '<th style="border:1px solid">RESPONSÁVEL</th>';
                        $table.= '</tr></thead><tbody>';
                        foreach ($dados as $value) {
                            $table.= '<tr>';
                            $table.= '<td style="border:1px solid">'.($value['dh_cadastro'] ?? '').'</td>';
                            $table.= '<td style="border:1px solid">'.($value['descricao'] ?? '').'</td>';
                            $table.= '<td style="border:1px solid">'.($value['nm_fornecedor'] ?? '').'</td>';
                            $table.= '<td style="border:1px solid">'.($value['dt_vencimento'] ?? '').'</td>';
                            $table.= '<td style="border:1px solid; text-align:center">'.($value['quantidade'] ?? '').'</td>';
                            $table.= '<td style="border:1px solid; text-align:center">'.(!empty($value['temperatura']) ? $value['temperatura'].'ºC' : '').'</td>';
                            $table.= '<td style="border:1px solid; text-align:center">'.($value['sif'] ?? '').'</td>';
                            $table.= '<td style="border:1px solid">'.($value['lote'] ?? '').'</td>';
                            $table.= '<td style="border:1px solid">'.($value['nro_nota'] ?? '').'</td>';
                            $table.= '<td style="border:1px solid">'.($value['ds_embalagem_condicoes'] ?? '').'</td>';
                            $table.= '<td style="border:1px solid">'.($value['nm_responsavel'] ?? '').'</td>';
                            $table.= '</tr>';
                        }
                        $table.= '</tbody></table>';

                        while (ob_get_level()) { ob_end_clean(); }
                        $app->response['Content-Type'] = 'application/pdf';
                        $mpdf = new \Mpdf\Mpdf([
                            'mode' => 'utf-8',
                            'format' => 'A4-L',
                            'orientation' => 'L',
                            'tempDir' =>'/tmp',
                            'default_font' => 'arial'
                        ]);
                        $mpdf->WriteHTML($table, 2);
                        $mpdf->Output('relatorio_materiais_recebimento_'.date('dmYHis').'.pdf', \Mpdf\Output\Destination::INLINE);
                        return;
                    }

                    $status = 200;
                    $ret = ['success'=>true, 'data'=>$dados];
                } else {
                    $status = 200; // Sem dados não é erro
                    $ret = ['success'=>true, 'data'=>[]];
                }
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao carregar relatório', 'detail'=>$e->getMessage()];
            }
        } else {
            $status = 401;
            $ret = ['success'=>false, 'msg'=>'Não autorizado'];
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

// API compat (mobile app) - /app-materiais-audit-sem-responsavel (GET)
$app->map('/app-materiais-audit-sem-responsavel', function() use ($app){
    $status = 400;
    $ret = ['success'=>false];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true, 'data'=>[]];
    } else {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $id_empresas = function_exists('getIdEmpresasLogado') ? getIdEmpresasLogado() : 0;
                if (empty($id_empresas) && function_exists('_getHeaderValue')) {
                    $hdr = _getHeaderValue('X-Company-Id');
                    if (!empty($hdr)) $id_empresas = (int)$hdr;
                }
                // Usa conexão direta como nos models
                $conn = new Connection();
                $arr = [];
                $whereEmpresa = '';
                if (!empty($id_empresas)) {
                    $whereEmpresa = ' AND m.id_empresas = :ID_EMPRESAS';
                    $arr[':ID_EMPRESAS'] = $id_empresas;
                }
                $sql = "SELECT m.id_materiais, m.descricao, m.dh_cadastro, m.id_usuarios, m.id_empresas
                        FROM tb_materiais m
                        WHERE (m.id_usuarios IS NULL OR m.id_usuarios = 0)
                          AND m.status <> 'D' $whereEmpresa
                        ORDER BY m.dh_cadastro DESC";
                $dados = $conn->select($sql, $arr);
                if ($dados) {
                    foreach ($dados as &$row) {
                        if (isset($row['dh_cadastro'])) $row['dh_cadastro'] = date('d/m/Y H:i', strtotime($row['dh_cadastro']));
                    }
                    $status = 200;
                    $ret = ['success'=>true, 'data'=>$dados];
                } else {
                    $status = 200;
                    $ret = ['success'=>true, 'data'=>[]];
                }
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao auditar materiais sem responsável', 'detail'=>$e->getMessage()];
            }
        } else {
            $status = 401;
            $ret = ['success'=>false, 'msg'=>'Não autorizado'];
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

// API compat (mobile app) - /app-materiais-fix-responsavel (POST)
$app->map('/app-materiais-fix-responsavel', function() use ($app){
    $status = 400;
    $ret = ['success'=>false];

    if ($app->request->isOptions()) {
        $status = 200;
        $ret = ['success'=>true];
    } else if ($app->request->isPost()) {
        if (valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'))) {
            try {
                $id_empresas = function_exists('getIdEmpresasLogado') ? getIdEmpresasLogado() : 0;
                if (empty($id_empresas) && function_exists('_getHeaderValue')) {
                    $hdr = _getHeaderValue('X-Company-Id');
                    if (!empty($hdr)) $id_empresas = (int)$hdr;
                }
                // Usuário padrão = usuário atual se getUsuario disponível
                $idUsuarioFix = 0;
                if (function_exists('getUsuario')) {
                    $usuario = getUsuario($app);
                    if ($usuario && isset($usuario['id_usuarios'])) {
                        $idUsuarioFix = (int)$usuario['id_usuarios'];
                    }
                }
                // Override via body ou query
                $body = $app->request->post();
                if (isset($body['id_usuario'])) {
                    $tmp = (int)$body['id_usuario'];
                    if ($tmp > 0) $idUsuarioFix = $tmp;
                }
                $qryParam = $app->request->get('id_usuario');
                if (!empty($qryParam)) {
                    $tmp = (int)$qryParam;
                    if ($tmp > 0) $idUsuarioFix = $tmp;
                }
                if ($idUsuarioFix <= 0) {
                    $status = 422;
                    $ret = ['success'=>false, 'msg'=>'id_usuario para correção não informado'];
                } else {
                    if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
                        $pdo = $GLOBALS['pdo'];
                        $whereEmpresa = '';
                        $params = [':u'=>$idUsuarioFix];
                        if (!empty($id_empresas)) {
                            $whereEmpresa = ' AND id_empresas = :e';
                            $params[':e'] = $id_empresas;
                        }
                        $st = $pdo->prepare("UPDATE tb_materiais SET id_usuarios = :u WHERE (id_usuarios IS NULL OR id_usuarios = 0) AND status <> 'D' $whereEmpresa");
                        $st->execute($params);
                        $afetados = $st->rowCount();
                        error_log('[MATERIAIS_FIX_RESPONSAVEL][INFO] id_usuario='.$idUsuarioFix.' afetados='.$afetados);
                        $status = 200;
                        $ret = ['success'=>true, 'afetados'=>$afetados];
                    } else {
                        $status = 500;
                        $ret = ['success'=>false, 'msg'=>'PDO não disponível'];
                    }
                }
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao corrigir responsáveis', 'detail'=>$e->getMessage()];
            }
        } else {
            $status = 401;
            $ret = ['success'=>false, 'msg'=>'Não autorizado'];
        }
    } else {
        $status = 405;
        $ret = ['success'=>false, 'msg'=>'Método não suportado'];
    }

    while (ob_get_level()) { ob_end_clean(); }
    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Methods'] = 'POST, OPTIONS';
    $response['Content-Type'] = 'application/json';
    $response->status($status);
    $response->body(json_encode($ret));
})->via('POST','OPTIONS');

?>
