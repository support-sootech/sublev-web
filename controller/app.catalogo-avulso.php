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

        $debug = ['id_usuario' => $id_usuario];

        // 1) Busca empresa pelo token do usuario no banco (mais confiavel)
        $id_empresas = _app_getEmpresaByToken($app);
        $fonte = 'db';
        $debug['empresa_db'] = $id_empresas;
        $debug['header_x_company'] = $app->request->headers->get('X-Company-Id');

        // 2) Fallback: header X-Company-Id
        if ($id_empresas <= 0) {
            $id_empresas = (int) ($app->request->headers->get('X-Company-Id') ?: 0);
            $fonte = 'header';
        }
        // 3) Fallback: context (session/param)
        if ($id_empresas <= 0) {
            $id_empresas = _app_getEmpresaFromContext($app);
            $fonte = 'context';
        }
        if ($id_empresas <= 0) {
            return _json_response($app, 400, ['success' => false, 'msg' => 'Empresa não identificada', '_debug' => $debug]);
        }

        $filtro = $app->request->params('busca'); // parametro da query string

        $ctrl = new CatalogoAvulsoController();
        $lista = $ctrl->loadAll($filtro, $id_empresas);

        return _json_response($app, 200, ['success' => true, 'data' => $lista, '_empresa' => $id_empresas, '_fonte' => $fonte, '_debug' => $debug]);
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

// DIAGNOSTICO - mostra dados do usuario Bagarelli e todas as empresas
// Acesse: https://sublev.sootech.com.br/app-diagnostico-empresas
// SOMENTE LEITURA - nao altera nada no banco
// REMOVER APOS ANALISE
$app->get('/app-diagnostico-empresas', function () use ($app) {
    try {
        $pdo = $GLOBALS['pdo'];

        $resultado = [];

        // 1) Listar TODAS as empresas cadastradas
        $st1 = $pdo->query("SELECT e.* FROM tb_empresas e ORDER BY id_empresas");
        $resultado['todas_empresas'] = $st1->fetchAll(PDO::FETCH_ASSOC);

        // 2) Buscar usuario Bagarelli (CNPJ 60946985000174)
        $st2 = $pdo->prepare("
            SELECT u.id_usuarios, u.id_pessoas, u.status, p.nome, p.cpf_cnpj, p.id_empresas,
                   e.nome as empresa_nome
            FROM tb_usuarios u
            INNER JOIN tb_pessoas p ON p.id_pessoas = u.id_pessoas
            LEFT JOIN tb_empresas e ON e.id_empresas = p.id_empresas
            WHERE REPLACE(REPLACE(REPLACE(p.cpf_cnpj,'.',''),'/',''),'-','') = '60946985000174'
        ");
        $st2->execute();
        $resultado['usuario_bagarelli'] = $st2->fetchAll(PDO::FETCH_ASSOC);

        // 3) Catalogo avulso por empresa
        $st3 = $pdo->query("
            SELECT c.id_empresas, e.nome as empresa_nome, COUNT(*) as total_produtos
            FROM tb_catalogo_avulso c
            LEFT JOIN tb_empresas e ON e.id_empresas = c.id_empresas
            WHERE c.status = 'A'
            GROUP BY c.id_empresas
            ORDER BY c.id_empresas
        ");
        $resultado['catalogo_por_empresa'] = $st3->fetchAll(PDO::FETCH_ASSOC);

        return _json_response($app, 200, $resultado);
    } catch (\Throwable $e) {
        return _json_response($app, 500, ['error' => $e->getMessage()]);
    }
});

// DEBUG - endpoint temporario para diagnosticar problema de empresa
$app->get('/app-catalogo-avulso-debug', function () use ($app) {
    try {
        $token = $app->request->headers->get('Token-User');
        $headerEmpresa = $app->request->headers->get('X-Company-Id');
        $pdo = $GLOBALS['pdo'];

        $info = [
            'token_recebido' => $token ? substr($token, 0, 8) . '...' : null,
            'header_x_company_id' => $headerEmpresa,
        ];

        if ($token) {
            // Buscar usuario pelo token
            $st = $pdo->prepare("SELECT u.id_usuarios, u.hash, u.status, u.id_pessoas FROM tb_usuarios u WHERE u.hash = :h LIMIT 1");
            $st->execute([':h' => $token]);
            $user = $st->fetch(PDO::FETCH_ASSOC);
            $info['usuario_encontrado'] = $user ? true : false;

            if ($user) {
                $info['id_usuarios'] = (int) $user['id_usuarios'];
                $info['id_pessoas'] = (int) $user['id_pessoas'];
                $info['user_status'] = $user['status'];

                // Buscar pessoa e empresa
                $st2 = $pdo->prepare("SELECT p.id_empresas, p.nome, p.cpf_cnpj, e.razao_social FROM tb_pessoas p LEFT JOIN tb_empresas e ON e.id_empresas = p.id_empresas WHERE p.id_pessoas = :id");
                $st2->execute([':id' => $user['id_pessoas']]);
                $pessoa = $st2->fetch(PDO::FETCH_ASSOC);

                if ($pessoa) {
                    $info['pessoa_nome'] = $pessoa['nome'];
                    $info['pessoa_cpf_cnpj'] = $pessoa['cpf_cnpj'];
                    $info['pessoa_id_empresas'] = $pessoa['id_empresas'];
                    $info['empresa_razao_social'] = $pessoa['razao_social'];
                }

                // Contar produtos no catalogo para essa empresa
                $idEmp = $pessoa ? (int) $pessoa['id_empresas'] : 0;
                if ($idEmp > 0) {
                    $st3 = $pdo->prepare("SELECT COUNT(*) as total FROM tb_catalogo_avulso WHERE id_empresas = :e AND status = 'A'");
                    $st3->execute([':e' => $idEmp]);
                    $info['catalogo_count_empresa'] = (int) $st3->fetch(PDO::FETCH_ASSOC)['total'];
                }

                // Contar total de produtos ativos
                $st4 = $pdo->query("SELECT id_empresas, COUNT(*) as total FROM tb_catalogo_avulso WHERE status = 'A' GROUP BY id_empresas");
                $info['catalogo_por_empresa'] = $st4->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return _json_response($app, 200, $info);
    } catch (\Throwable $e) {
        return _json_response($app, 500, ['error' => $e->getMessage()]);
    }
});
