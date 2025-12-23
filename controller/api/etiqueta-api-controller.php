<?php
$app->map('/app-etiqueta-info(/:num_etiqueta)', function ($num_etiqueta = '') use ($app) {
    $response_status = 400;
    $response_metodo = 'GET';
    $data = array();

    if ($app->request->isOptions()) {
        $response_status = 200;
        $response_metodo = 'GET, OPTIONS';
        $data = array('OK');
    } else if ($app->request->isGet()) {

        try {
            $usuario = getUsuario($app);
            if ($usuario == false) {
                throw new Exception("Usuário não localizado!");
            }

            if (empty($num_etiqueta)) {
                throw new Exception("É necessário informar o código da etiqueta!");
            }

            $class_etiquetas = new EtiquetasModel();
            $arr = $class_etiquetas->loadNumEtiquetaInfo($num_etiqueta);
            if (!$arr) {
                throw new Exception("Nenhuma etiqueta localizada!", 1);
            }

            $response_status = 200;
            $data = array('success' => true, 'type' => 'success', 'msg' => 'OK', 'data' => $arr);

        } catch (Exception $e) {
            $data = array('error' => true, 'type' => 'danger', 'msg' => $e->getMessage());
        }

    } else {
        $data = array('success' => false, 'type' => 'danger', 'msg' => 'Método incorreto!');
    }

    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Headers'] = '*';
    $response['Access-Control-Allow-Methods'] = $response_metodo;
    $response['Content-Type'] = 'application/json';

    $response->status($response_status);
    $response->body(json_encode($data));

})->via('GET', 'OPTIONS');

$app->map('/app-etiquetas', function ($id_etiquetas = '') use ($app) {
    $response_status = 400;
    $response_metodo = 'GET';
    $data = array();

    if ($app->request->isOptions()) {
        $response_status = 200;
        $response_metodo = 'GET, OPTIONS';
        $data = array('OK');
    } else if ($app->request->isGet()) {

        try {
            $usuario = getUsuario($app);
            if ($usuario == false) {
                throw new Exception("Usuário não localizado!");
            }

            $class_etiquetas = new EtiquetasModel();
            $arr = $class_etiquetas->loadEtiquetasIdUsuarios($usuario['id_usuarios'], $usuario['id_empresas']);
            if (!$arr) {
                throw new Exception("Nenhuma etiqueta localizada!", 1);
            }
            $response_status = 200;
            $data = array('success' => true, 'type' => 'success', 'msg' => 'OK', 'data' => $arr);

        } catch (Exception $e) {
            $data = array('error' => true, 'type' => 'danger', 'msg' => $e->getMessage());
        }

    } else {
        $data = array('success' => false, 'type' => 'danger', 'msg' => 'Método incorreto!');
    }

    $response = $app->response();
    $response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Headers'] = '*';
    $response['Access-Control-Allow-Methods'] = $response_metodo;
    $response['Content-Type'] = 'application/json';

    $response->status($response_status);
    $response->body(json_encode($data));

})->via('GET', 'OPTIONS');

$app->options('/api/etiquetas/avulsas', function () use ($app) {
    $app->response->headers->set('Access-Control-Allow-Origin', '*');
    $app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    $app->response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
});

$app->post('/api/etiquetas/avulsas', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
    $app->response->headers->set('Access-Control-Allow-Origin', '*');

    // --- Sessão / Autorização ---
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $idUsuario = isset($_SESSION['usuario']['id_usuarios']) ? (int) $_SESSION['usuario']['id_usuarios'] : null;
    $idEmpresa = isset($_SESSION['usuario']['id_empresas']) ? (int) $_SESSION['usuario']['id_empresas'] : null;

    if (!$idUsuario || !$idEmpresa) {
        $app->response->setStatus(401);
        echo json_encode(['ok' => false, 'erro' => 'Sessão inválida: usuário/empresa não encontrados.']);
        return;
    }

    // --- Entrada ---
    $raw = $app->request()->getBody();
    $data = json_decode($raw, true) ?: [];

    // Adaptação dos campos para o EtiquetasController
    $normalizedData = [
        'descricao' => $data['descricao_produto'] ?? '',
        'quantidade' => (int) ($data['quantidade'] ?? 1),
        'validade' => null, // Será preenchido abaixo
        'peso' => (float) str_replace(['.', ','], ['', '.'], (string) ($data['peso'] ?? '0')),
        'id_unidades_medidas' => (int) ($data['id_unidades_medidas'] ?? 0),
        'id_modo_conservacao' => (int) ($data['id_modo_conservacao'] ?? 0),
        'salvar_catalogo' => true // Sempre salva no catálogo agora
    ];

    // Converte dd/mm/aaaa -> aaaa-mm-dd
    if (!empty($data['dt_validade'])) {
        $p = explode('/', $data['dt_validade']);
        if (count($p) === 3) {
            $normalizedData['validade'] = sprintf('%04d-%02d-%02d', (int) $p[2], (int) $p[1], (int) $p[0]);
        }
    }

    require_once __DIR__ . '/../EtiquetasController.php';
    $ctrl = new EtiquetasController();
    $res = $ctrl->criarAvulsa($normalizedData, $idUsuario, $idEmpresa);

    if ($res['ok']) {
        $app->response->setStatus(201);
        echo json_encode($res);
    } else {
        $app->response->setStatus(500);
        echo json_encode($res);
    }
});

?>