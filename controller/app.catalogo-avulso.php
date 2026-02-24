<?php
if (!class_exists('CatalogoAvulsoController')) {
    require_once __DIR__ . '/CatalogoAvulsoController.php';
}

// Helpers de Auth (replicados de app.etiquetas.php com proteção function_exists)
if (!function_exists('_app_getUsuarioByToken')) {
    function _app_getUsuarioByToken($app)
    {
        try {
            $token = $app->request->headers->get('Token-User');
            if (!$token)
                return null;
            $pdo = $GLOBALS['pdo'];
            $st = $pdo->prepare("SELECT id_usuarios FROM tb_usuarios WHERE hash = :h AND status = 'A' LIMIT 1");
            $st->execute([':h' => $token]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row ? (int) $row['id_usuarios'] : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
if (!function_exists('_app_getEmpresaFromContext')) {
    function _app_getEmpresaFromContext($app)
    {
        if (function_exists('getIdEmpresasLogado')) {
            $id = getIdEmpresasLogado();
            if (!empty($id))
                return (int) $id;
        }
        $hdr = $app->request->headers->get('X-Company-Id');
        if (!empty($hdr))
            return (int) $hdr;
        $id = $app->request->params('id_empresas');
        return !empty($id) ? (int) $id : 0;
    }
}

/**
 * Busca o id_empresas diretamente do banco pelo token do usuário autenticado.
 * Mais confiável que depender do header X-Company-Id enviado pelo app.
 */
if (!function_exists('_app_getEmpresaByToken')) {
    function _app_getEmpresaByToken($app)
    {
        try {
            $token = $app->request->headers->get('Token-User');
            if (!$token)
                return 0;
            $pdo = $GLOBALS['pdo'];
            $st = $pdo->prepare(
                "SELECT p.id_empresas
                   FROM tb_usuarios u
                   INNER JOIN tb_pessoas p ON p.id_pessoas = u.id_pessoas
                  WHERE u.hash = :h AND u.status = 'A'
                  LIMIT 1"
            );
            $st->execute([':h' => $token]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row ? (int) $row['id_empresas'] : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
if (!function_exists('_json_response')) {
    function _json_response($app, $status, $arr)
    {
        $r = $app->response();
        $r['Access-Control-Allow-Origin'] = '*';
        $r['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS, PUT, DELETE';
        $r['Content-Type'] = 'application/json';
        $r->status($status);
        $r->body(json_encode($arr));
    }
}

// LISTAR
$app->get('/app-catalogo-avulso', function () use ($app) {
    try {
        $id_usuario = _app_getUsuarioByToken($app);
        if (!$id_usuario) {
            return _json_response($app, 401, ['success' => false, 'msg' => 'Não autorizado']);
        }

        // 1) Busca empresa pelo token do usuario no banco (mais confiavel)
        $id_empresas = _app_getEmpresaByToken($app);
        // 2) Fallback: header X-Company-Id
        if ($id_empresas <= 0) {
            $id_empresas = (int) ($app->request->headers->get('X-Company-Id') ?: 0);
        }
        // 3) Fallback: context (session/param)
        if ($id_empresas <= 0) {
            $id_empresas = _app_getEmpresaFromContext($app);
        }
        if ($id_empresas <= 0) {
            return _json_response($app, 400, ['success' => false, 'msg' => 'Empresa não identificada']);
        }

        $filtro = $app->request->params('busca'); // parametro da query string

        $ctrl = new CatalogoAvulsoController();
        $lista = $ctrl->loadAll($filtro, $id_empresas);

        return _json_response($app, 200, ['success' => true, 'data' => $lista]);
    } catch (\Throwable $e) {
        return _json_response($app, 500, ['success' => false, 'msg' => 'Erro interno', 'detail' => $e->getMessage()]);
    }
});

// SALVAR (Incluir ou Editar)
// Payload JSON: { id: (opcional), descricao, qtde_dias_vencimento, peso... }
$app->post('/app-catalogo-avulso-save', function () use ($app) {
    try {
        $id_usuario = _app_getUsuarioByToken($app);
        if (!$id_usuario)
            return _json_response($app, 401, ['success' => false, 'msg' => 'Auth required']);

        $id_empresas = _app_getEmpresaByToken($app);
        if ($id_empresas <= 0)
            $id_empresas = (int) ($app->request->headers->get('X-Company-Id') ?: 0);
        if ($id_empresas <= 0)
            $id_empresas = _app_getEmpresaFromContext($app);
        if ($id_empresas <= 0)
            return _json_response($app, 400, ['success' => false, 'msg' => 'Empresa não identificada']);

        $raw = $app->request->getBody();
        $data = json_decode($raw, true);
        if (!$data)
            $data = $app->request->post(); // fallback form-data

        $ctrl = new CatalogoAvulsoController();

        // Dados comuns
        $saveData = [
            'descricao' => $data['descricao'],
            'qtde_dias_vencimento' => $data['qtde_dias_vencimento'],
            'peso' => str_replace(',', '.', (string) $data['peso']),
            'id_unidades_medidas' => $data['id_unidades_medidas'],
            'id_modo_conservacao' => $data['id_modo_conservacao'],
            'id_empresas' => $id_empresas,
            'id_usuarios' => $id_usuario
        ];

        // Favorito pode vir no payload tanto pra insert quanto update
        // Se vier, usa. Se nao vier e for insert, default 1. Se update, mantem o que tem (nao passa o campo ou passa o que veio).
        // Melhor logica: Se passou no payload, atualiza.
        if (isset($data['favorito'])) {
            $saveData['favorito'] = $data['favorito'] ? 1 : 0;
        } elseif (empty($data['id'])) {
            // Se insert e nao veio, default 1
            $saveData['favorito'] = 1;
        }

        if (!empty($data['id'])) {
            // EDIT
            $res = $ctrl->edit($saveData, ['id' => $data['id']]);
        } else {
            // INSERT
            $res = $ctrl->save($saveData);
        }

        if ($res === TRUE || is_numeric($res)) {
            return _json_response($app, 200, ['success' => true]);
        } else {
            return _json_response($app, 400, ['success' => false, 'msg' => 'Erro ao salvar', 'detail' => $res]);
        }

    } catch (\Throwable $e) {
        return _json_response($app, 500, ['success' => false, 'msg' => 'Erro interno', 'detail' => $e->getMessage()]);
    }
});

// TOGGLE FAVORITO
// Payload: { id: 123, favorito: true/false }
$app->post('/app-catalogo-avulso-favorito', function () use ($app) {
    try {
        $id_usuario = _app_getUsuarioByToken($app);
        if (!$id_usuario)
            return _json_response($app, 401, ['success' => false]);

        $raw = $app->request->getBody();
        $data = json_decode($raw, true);

        if (empty($data['id']))
            return _json_response($app, 400, ['success' => false, 'msg' => 'ID obrigatorio']);

        $ctrl = new CatalogoAvulsoController();
        $isFav = !empty($data['favorito']);

        $res = $ctrl->toggleFavorito($data['id'], $isFav);

        return _json_response($app, 200, ['success' => true]);
    } catch (\Throwable $e) {
        return _json_response($app, 500, ['success' => false, 'detail' => $e->getMessage()]);
    }
});

// EXCLUIR
// Payload: { id: 123 } ou URL param ?id=123
$app->post('/app-catalogo-avulso-del', function () use ($app) {
    try {
        $id_usuario = _app_getUsuarioByToken($app);
        if (!$id_usuario)
            return _json_response($app, 401, ['success' => false]);

        $raw = $app->request->getBody();
        $data = json_decode($raw, true);
        $id = $data['id'] ?? $app->request->params('id');

        if (empty($id))
            return _json_response($app, 400, ['success' => false, 'msg' => 'ID obrigatorio']);

        $ctrl = new CatalogoAvulsoController();
        $res = $ctrl->del($id);

        if ($res === TRUE) {
            return _json_response($app, 200, ['success' => true]);
        } else {
            return _json_response($app, 400, ['success' => false, 'msg' => 'Erro ao excluir', 'detail' => $res]);
        }
    } catch (\Throwable $e) {
        return _json_response($app, 500, ['success' => false, 'detail' => $e->getMessage()]);
    }
});

