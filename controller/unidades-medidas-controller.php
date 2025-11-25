<?php
$app->get('/controle-unidades-medidas', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/unidades-medidas-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/unidades-medidas-edit/:id_unidades_medidas', function($id_unidades_medidas='') use ($app){
    $status = 200;
    $data = array();
    if (valida_logado()) {
        $class_unidades_medidas = new UnidadesMedidasModel();

        if (!empty($id_unidades_medidas)) {
            $arr = $class_unidades_medidas->loadId($id_unidades_medidas);
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

$app->get('/unidades-medidas-del/:id_unidades_medidas', function($id_unidades_medidas='') use ($app){
    $status = 400;
    $data = array();
    if (valida_logado()) {
        $class_unidades_medidas = new UnidadesMedidasModel();

        if (!empty($id_unidades_medidas)) {
            $del = $class_unidades_medidas->del($id_unidades_medidas);
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

$app->post('/unidades-medidas-json', function() use ($app){
    $status = 200;
    $data['data'] = array();
    if (valida_logado()) {
        try {
            $id_empresas = getIdEmpresasLogado();

            $statusParam = '';
            if ($app->request->post('status')) {
                $statusParam = $app->request->post('status');
            }

            $class_unidades_medidas = new UnidadesMedidasModel();
            $arr = $class_unidades_medidas->loadAll($id_empresas, $statusParam);
            if ($arr) {
                foreach ($arr as $key => $value) {
                    $data['data'][] = $value;
                }
            }
        } catch (Exception $e) {
            $status = 500;
            $data = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
        }
    }
    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Methods'] = 'POST';
    $response['Content-Type'] = 'application/json';

    $response->status($status);
    $response->body(json_encode($data));
});

$app->post('/unidades-medidas-save', function() use ($app){
    $status = 400;
    $retorno = array();

    if ($app->request->isPost()) {
        if (valida_logado()) {
            $id_unidades_medidas = '';
            $post = array();

            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('unidades_medidas_', '', $key))] = $value;
            }

            if (isset($post['id_unidades_medidas'])) {
                $id_unidades_medidas = $post['id_unidades_medidas'];
                unset($post['id_unidades_medidas']);
            }

            $post['id_empresas'] = $_SESSION['usuario']['id_empresas'];

            try {
                $class_unidades_medidas = new UnidadesMedidasModel();

                if (!empty($id_unidades_medidas)) {
                    $data = $class_unidades_medidas->edit($post, array('id_unidades_medidas'=>$id_unidades_medidas));
                } else {
                    $data = $class_unidades_medidas->add($post);
                }

                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true,
                        'type'=>'success',
                        'msg'=>messagesDefault(!empty($id_unidades_medidas) ? 'update' : 'register'),
                        'data'=>$data
                    );
                } else {
                    $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$data);
                }
            } catch (Exception $e) {
                $status = 500;
                $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
            }
        } else {
            $status = 401;
            $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>'Não autorizado');
        }
    } else {
        $status = 405;
        $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>'Método incorreto!');
    }

    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Methods'] = 'POST';
    $response['Content-Type'] = 'application/json';

    $response->status($status);
    $response->body(json_encode($retorno));
});

/** Model */
require_once __DIR__ . '/../model/UnidadesMedidasModel.php';

/** Helper: resolve usuário por Token-User (quando a chamada vem do app) */
function _getUsuarioByTokenHeader($app) {
    try {
        $token = _getHeaderValue('Token-User'); // <--- usa helper robusto
        if (!$token) return null;
        $pdo = $GLOBALS['pdo'];
        $st  = $pdo->prepare("SELECT id_usuarios FROM tb_usuarios WHERE hash = :h AND status = 'A' LIMIT 1");
        $st->execute([':h' => $token]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['id_usuarios'] : null;
    } catch (Exception $e) {
        return null;
    }
}

/** Helper: resolve empresa (sessão -> header -> query/post) */
function _getEmpresaFromContext($app) {
    if (function_exists('getIdEmpresasLogado')) {
        $id = getIdEmpresasLogado();
        if (!empty($id)) return (int)$id;
    }
    $hdr = _getHeaderValue('X-Company-Id'); // <--- usa helper robusto
    if (!empty($hdr)) return (int)$hdr;
    $id = $app->request->params('id_empresas');
    return !empty($id) ? (int)$id : 0;
}

/**
 * NOVO: /app-unidades-medidas
 * - Reusa UnidadesMedidasModel->loadAll($id_empresas, $status)
 * - Aceita sessão (painel) OU header Token-User (app)
 * - Aceita GET e POST; `status` via query/post (default 'A')
 * - Resposta JSON limpinha
 */
$app->map('/app-unidades-medidas', function() use ($app) {
    $status = 200;
    $ret    = ['success'=>false, 'data'=>[]];

    // tenta sessão do painel...
    $logado = function_exists('valida_logado') ? valida_logado() : false;

    // ...ou Token-User do app
    $id_usuario = null;
    if ($logado) {
        $id_usuario = $_SESSION['usuario']['id_usuarios'] ?? null;
    }
    if (!$id_usuario) {
        $id_usuario = _getUsuarioByTokenHeader($app);
    }

    if (!$id_usuario) {
        $status = 401;
        $ret = ['success'=>false, 'msg'=>'Não autorizado'];
    } else {
        // empresa: sessão -> header -> param
        $id_empresas = _getEmpresaFromContext($app);

        if ($id_empresas <= 0) {
            $status = 400;
            $ret = ['success'=>false, 'msg'=>'Empresa não informada'];
        } else {
            try {
                $statusParam = $app->request->params('status') ?: 'A';
                $class = new UnidadesMedidasModel();
                $arr   = $class->loadAll($id_empresas, $statusParam);
                $ret   = ['success'=>true, 'data'=>($arr ?: [])];
            } catch (Exception $e) {
                $status = 500;
                $ret = ['success'=>false, 'msg'=>'Erro ao listar unidades', 'detail'=>$e->getMessage()];
            }
        }
    }

    while (ob_get_level()) { ob_end_clean(); }

    $response = $app->response();
    $response['Access-Control-Allow-Origin']  = '*';
    $response['Access-Control-Allow-Methods'] = 'GET, POST';
    $response['Content-Type'] = 'application/json';
    $response->status($status);
    $response->body(json_encode($ret));
})->via('GET','POST');
