<?php
$app->post('/embalagem-condicoes-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_embalagem_condicoes = new EmbalagemCondicoesModel();

        $arr = $class_embalagem_condicoes->loadAll();
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

// API compat (mobile app) - /app-embalagens-condicoes (GET|POST)
$app->map('/app-embalagens-condicoes', function() use ($app){
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
            $class_embalagem_condicoes = new EmbalagemCondicoesModel();
            $arr = $class_embalagem_condicoes->loadAll();
            $ret = ['success'=>true, 'data'=>($arr?:[])];
        } catch (Exception $e) {
            $status = 500;
            $ret = ['success'=>false, 'msg'=>'Erro ao listar condições de embalagem', 'detail'=>$e->getMessage()];
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
?>