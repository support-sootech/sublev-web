<?php
$app->map('/app-etiqueta-info(/:num_etiqueta)', function($num_etiqueta='') use ($app){
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
            if ($usuario==false) {
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
            $data = array('success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$arr);

        } catch (Exception $e) {
            $data = array('error'=>true, 'type'=>'danger', 'msg'=>$e->getMessage());
        }        
        
    } else {
        $data = array('success'=>false, 'type'=>'danger', 'msg'=>'Método incorreto!');
    }

	$response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Headers'] = '*';
	$response['Access-Control-Allow-Methods'] = $response_metodo;
	$response['Content-Type'] = 'application/json';

	$response->status($response_status);
	$response->body(json_encode($data));

})->via('GET','OPTIONS');

$app->map('/app-etiquetas', function($id_etiquetas='') use ($app){
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
            if ($usuario==false) {
                throw new Exception("Usuário não localizado!");
            }

            $class_etiquetas = new EtiquetasModel();
            $arr = $class_etiquetas->loadEtiquetasIdUsuarios($usuario['id_usuarios'], $usuario['id_empresas']);
            if (!$arr) {
                throw new Exception("Nenhuma etiqueta localizada!", 1);
            }
            $response_status = 200;
            $data = array('success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$arr);

        } catch (Exception $e) {
            $data = array('error'=>true, 'type'=>'danger', 'msg'=>$e->getMessage());
        }        
        
    } else {
        $data = array('success'=>false, 'type'=>'danger', 'msg'=>'Método incorreto!');
    }

	$response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
    $response['Access-Control-Allow-Headers'] = '*';
	$response['Access-Control-Allow-Methods'] = $response_metodo;
	$response['Content-Type'] = 'application/json';

	$response->status($response_status);
	$response->body(json_encode($data));

})->via('GET','OPTIONS');

$app->options('/api/etiquetas/avulsas', function() use ($app) {
    $app->response->headers->set('Access-Control-Allow-Origin', '*');
    $app->response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    $app->response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
});

$app->post('/api/etiquetas/avulsas', function() use ($app) {
    $app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
    $app->response->headers->set('Access-Control-Allow-Origin', '*');

    // --- Sessão / Autorização ---
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $idUsuario  = isset($_SESSION['usuario']['id_usuarios'])  ? (int)$_SESSION['usuario']['id_usuarios']  : null;
    $idEmpresa  = isset($_SESSION['usuario']['id_empresas'])  ? (int)$_SESSION['usuario']['id_empresas']  : null;

    if (!$idUsuario || !$idEmpresa) {
        $app->response->setStatus(401);
        echo json_encode(['ok'=>false, 'erro'=>'Sessão inválida: usuário/empresa não encontrados.']);
        return;
    }

    // --- Entrada ---
    $raw = $app->request()->getBody();
    $data = json_decode($raw, true) ?: [];

    // Campos obrigatórios (conforme a regra nova)
    $obrig = ['descricao_produto','dt_validade','peso','id_unidades_medidas','id_modo_conservacao','quantidade'];
    $faltando = [];
    foreach ($obrig as $c) {
        if (!isset($data[$c]) || $data[$c]==='' || $data[$c]===null) $faltando[] = $c;
    }
    if (!empty($faltando)) {
        $app->response->setStatus(400);
        echo json_encode(['ok'=>false,'erro'=>'Campos obrigatórios faltando','campos'=>$faltando]);
        return;
    }

    // Sanitização/conversões
    $descricaoProd  = trim($data['descricao_produto']);
    $dtValidadeBr   = trim($data['dt_validade']); // dd/mm/aaaa
    $pesoStr        = str_replace(['.', ','], ['', '.'], (string)$data['peso']); // "1,00" -> "1.00"
    $idUM           = (int)$data['id_unidades_medidas'];
    $idModoCons     = (int)$data['id_modo_conservacao'];
    $qtdItens       = (int)$data['quantidade'];

    if ($qtdItens < 1) { $qtdItens = 1; }
    $peso = (float)$pesoStr;
    if ($peso <= 0) {
        $app->response->setStatus(400);
        echo json_encode(['ok'=>false,'erro'=>'Peso inválido']);
        return;
    }

    // Converte dd/mm/aaaa -> aaaa-mm-dd
    $p = explode('/', $dtValidadeBr);
    if (count($p) !== 3) {
        $app->response->setStatus(400);
        echo json_encode(['ok'=>false,'erro'=>'Data inválida (use dd/mm/aaaa)']);
        return;
    }
    $dtValidadeDb = sprintf('%04d-%02d-%02d', (int)$p[2], (int)$p[1], (int)$p[0]);

    // --- Models necessários ---
    require_once __DIR__ . '/../../model/EtiquetasModel.php';
    require_once __DIR__ . '/../../model/MateriaisModel.php';
    require_once __DIR__ . '/../../model/MateriaisFracionadosModel.php';

    $mdlEtiq = new EtiquetasModel();
    $mdlMat  = new MateriaisModel();
    $mdlFrac = new MateriaisFracionadosModel();

    // Saída acumulada
    $resp = [
        'ok'         => false,
        'material'   => null,
        'fracionados'=> [],
        'etiquetas'  => [],
    ];

    try {
        // 1) Cria 1 registro em tb_materiais
        //    campos esperados (ajuste se necessário ao seu fields do MateriaisModel):
        //    descricao, cod_barras, id_unidades_medidas, id_modo_conservacao, peso, quantidade, status, id_empresas
        // Marca o material como criado via etiqueta avulsa (fg_avulsa = 'S')
        // Usamos o método estático para garantir o flag consistente
        $idMateriais = MateriaisModel::createFromAvulsa(
            mb_substr($descricaoProd, 0, 255),
            $dtValidadeDb,
            $peso,
            $idUM,
            $idModoCons,
            $idEmpresa,
            $idUsuario,
            $qtdItens
        );
        if (!$idMateriais) { throw new Exception('Falha ao inserir material.'); }

        $resp['material'] = (int)$idMateriais;

        // 2) Para cada item: cria 1 fracionamento e 1 etiqueta
        for ($i=1; $i <= $qtdItens; $i++) {
            // tb_materiais_fracionados:
            // campos usuais: id_materiais, qtd_fracionada, dt_fracionamento, dt_vencimento, id_usuarios
            $frArr = [
                'id_materiais'     => (int)$idMateriais,
                'qtd_fracionada'   => $peso,
                'dt_fracionamento' => date('Y-m-d'),
                'dt_vencimento'    => $dtValidadeDb,
                'id_usuarios'      => $idUsuario,
            ];
            $idFrac = $mdlFrac->add($frArr);
            if (!$idFrac) { throw new Exception('Falha ao inserir material fracionado.'); }
            $resp['fracionados'][] = (int)$idFrac;

            // tb_etiquetas: usa seu próprio add() via EtiquetasModel
            $etArr = [
                'descricao'                 => 'Etiqueta avulsa',
                'codigo'                    => '',
                'id_materiais_fracionados'  => (int)$idFrac,
                'id_materiais'              => (int)$idMateriais,
                'status'                    => 'A',
                'id_usuarios'               => $idUsuario,
                'id_empresas'               => $idEmpresa,
            ];
            $idEtiq = $mdlEtiq->add($etArr);
            if (!$idEtiq) { throw new Exception('Falha ao inserir etiqueta.'); }
            $resp['etiquetas'][] = (int)$idEtiq;
        }

        $resp['ok'] = true;
        $app->response->setStatus(201);
        echo json_encode($resp);

    } catch (Exception $e) {
        // Sem transação global? reporta erro.
        $app->response->setStatus(500);
        echo json_encode(['ok'=>false,'erro'=>$e->getMessage()]);
    }
});

?>