<?php
$app->map('/app-materiais-fracionados-info', function($codigo='') use ($app){
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
            
            $class_materiais_fracionados = new MateriaisFracionadosModel();

            $arr = $class_materiais_fracionados->load($usuario['id_empresas'],'','',$usuario['id_usuarios']);
            if ($arr==false) {
                throw new Exception("Nenhum material fracionado localizado!");
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

$app->map('/app-materiais-fracionar', function() use ($app){
	$response_status = 400;
    $response_metodo = 'POST';
    $data = array();
    $res = false;

    if ($app->request->isOptions()) {
        $response_status = 200;
        $response_metodo = 'POST, OPTIONS';
        $data = array('OK');
    } else if ($app->request->isPost()) {

        try {
            $usuario = getUsuario($app);
            if ($usuario==false) {
                throw new Exception("Usuário não localizado!");
            }

            $params = retornaParametros($app);

            if (!isset($params['id_materiais']) || empty($params['id_materiais'])) {
                throw new Exception("É necessário informar o código do material!");
            }

            if (!isset($params['quantidade']) || empty($params['quantidade'])) {
                throw new Exception("É necessário informar a quantidade!");
            }

            if (!isset($params['tipo']) || empty($params['tipo'])) {
                throw new Exception("É necessário informar a tipo do fracionamento (UNIDADE OU FRACAO)!");
            }
           
            $class_materiais = new MateriaisModel();
            $material = $class_materiais->loadIdMaterialDetalhes('A',$params['id_materiais'], $usuario['id_empresas']);

            //$etiqueta = gerarEtiquetas($material, 419, $usuario['id_usuarios'], $usuario['id_empresas']);

            if ($material) {
                $quantidade = array();
                for ($i=1; $i <= $params['quantidade']; $i++) { 
                    $quantidade[] = 1;
                }

                $tipo_fracionamento = mb_strtoupper($params['tipo']) == 'FRACAO' ? 'FRACAO' : 'UNIDADE';
                
                $fracionamento = fracionarMateriais(
                    $params['id_materiais'], 
                    $quantidade, 
                    $tipo_fracionamento,
                    true,
                    $usuario['id_usuarios'],
                    $usuario['id_empresas']
                );                
                $response_status = 200;
                $data = $fracionamento;
            } else {
                throw new Exception("Material não localizado!", 1);
            }
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

})->via('POST','OPTIONS');

$app->map('/app-materiais-fracionados-vencimento', function() use ($app){
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

            $class_materiais = new MateriaisModel();
            $arr['vencem_hoje'] = $class_materiais->loadQuantMateriaisVencimento('','texto_vencem_hoje', $usuario['id_empresas']);
            $arr['vencem_hoje'] = isset($arr['vencem_hoje'][0]['quantidade']) ? $arr['vencem_hoje'][0]['quantidade'] : 0;

            $arr['vencem_amanha'] = $class_materiais->loadQuantMateriaisVencimento('','texto_vencem_amanha', $usuario['id_empresas']);
            $arr['vencem_amanha'] = isset($arr['vencem_amanha'][0]['quantidade']) ? $arr['vencem_amanha'][0]['quantidade'] : 0;

            $arr['vencem_semana'] = $class_materiais->loadQuantMateriaisVencimento('','texto_vencem_semana', $usuario['id_empresas']);
            $arr['vencem_semana'] = isset($arr['vencem_semana'][0]['quantidade']) ? $arr['vencem_semana'][0]['quantidade'] : 0;

            $arr['vencem_mais_1_semana'] = $class_materiais->loadQuantMateriaisVencimento('','texto_vencem_mais_1_semana', $usuario['id_empresas']);
            $arr['vencem_mais_1_semana'] = isset($arr['vencem_mais_1_semana'][0]['quantidade']) ? $arr['vencem_mais_1_semana'][0]['quantidade'] : 0;
            
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

$app->map('/app-materiais-fracionados-baixa', function() use ($app){
	$response_status = 400;
    $response_metodo = 'PUT';
    $data = array();

    if ($app->request->isOptions()) {
        $response_status = 200;
        $response_metodo = 'PUT, OPTIONS';
        $data = array('OK');
    } else if ($app->request->isPut()) {

        try {
            $usuario = getUsuario($app);
            $params = retornaParametros($app);

            if ($usuario==false) {
                throw new Exception("Usuário não localizado!");
            }

            if (empty($params['id_materiais_fracionados'])) {
                throw new Exception('É necessário informar o material!');
            }

            if (empty($params['status'])) {
                throw new Exception('É necessário informar o status!');
            }

            if ($params['status']=='D' && empty($params['motivo_descarte'])) {
                throw new Exception('Para descarte do material é necessário informar o motivo!');
            }

            $class_materiais = new MateriaisFracionadosModel();

            $material_fracionado = $class_materiais->loadId($params['id_materiais_fracionados']);
            if (!$material_fracionado || $material_fracionado['status']=='D') {
                throw new Exception('Material fracionado não localizado!');
            }

            // Atualiza direto via PDO para evitar efeitos colaterais do model
            $pdo = $GLOBALS['pdo'];
            $stUp = $pdo->prepare("UPDATE tb_materiais_fracionados
                                      SET status = :s,
                                          motivo_descarte = :m,
                                          id_usuarios = :u
                                    WHERE id_materiais_fracionados = :id");
            $stUp->execute([
                ':s'  => $params['status'],
                ':m'  => ($params['status']==='D' ? (string)$params['motivo_descarte'] : null),
                ':u'  => (int)$usuario['id_usuarios'],
                ':id' => (int)$params['id_materiais_fracionados'],
            ]);

            // Regra: ao dar baixa (V) ou descartar (D) o fracionado,
            // a(s) etiqueta(s) associada(s) não devem mais aparecer na lista.
            // Portanto, marcamos tb_etiquetas.status = 'D' para o id_materiais_fracionados informado.
            // Observação: não alteramos para outros status (ex.: 'C').
            if (in_array($params['status'], ['V','D'])) {
                try {
                    if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
                        $pdo = $GLOBALS['pdo'];
                        $st = $pdo->prepare("UPDATE tb_etiquetas SET status = :st WHERE id_materiais_fracionados = :mf AND status <> :st");
                        $st->execute([
                            ':mf' => (int)$params['id_materiais_fracionados'],
                            ':st' => $params['status'],
                        ]);
                    }
                } catch (\Throwable $t) {
                    // Não falha a operação principal por causa deste ajuste; apenas ignora erro da atualização auxiliar.
                }
            }
            $response_status = 200;
            $data = array('success'=>true, 'type'=>'success', 'msg'=>'Registro alterado com sucesso!');

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

})->via('PUT','OPTIONS');

$app->map('/app-materiais-fracionados-vencimento-json(/:acao)', function($acao='') use ($app){
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

            if (empty(trim($acao))) {
                throw new Exception("É necessário informar a ação do filtro!");
            }

            $arr = array();
            $class_materiais = new MateriaisModel();
            $lista = $class_materiais->loadMateriaisVencimento('',mb_strtolower($acao), $usuario['id_empresas']);
            if (!$lista) {
                throw new Exception("Nenhum material fracionado localizado!");
            }

            // Em alguns casos foi reportada diferença entre a contagem (endpoint /app-materiais-fracionados-vencimento)
            // e a lista retornada aqui (faltando 1 item em "Até 7 dias"). A lógica abaixo evita possíveis perdas por
            // falha pontual em loadIdEtiquetaInfo (ex.: status alterado entre as duas requisições) fazendo um fetch em lote.
            require_once __DIR__ . '/../../model/EtiquetasModel.php';
            $ids = [];
            foreach ($lista as $row) {
                if (isset($row['id_etiquetas'])) {
                    $ids[] = (int)$row['id_etiquetas'];
                }
            }
            $ids = array_values(array_unique(array_filter($ids))); // remove duplicados e zeros

            $etiquetas = [];
            if (!empty($ids)) {
                $etiquetas = EtiquetasModel::buscarPorIds($ids); // já formata datas/peso/responsável abreviado
            }

            // Se por algum motivo a busca em lote retornar menos que o esperado, fazemos fallback pontual
            if (count($etiquetas) < count($ids)) {
                $class_etiquetas = new EtiquetasModel();
                $existentes = [];
                foreach ($etiquetas as $e) { $existentes[(int)$e['id_etiquetas']] = true; }
                foreach ($ids as $id) {
                    if (!isset($existentes[$id])) {
                        $one = $class_etiquetas->loadIdEtiquetaInfo($id);
                        if ($one) { $etiquetas[] = $one; }
                    }
                }
            }

            // Ordena por id decrescente como outros endpoints
            usort($etiquetas, function($a,$b){ return ($b['id_etiquetas'] <=> $a['id_etiquetas']); });

            // Opcional: incluir metadados de depuração quando ?debug=1
            $debugInfo = null;
            if (isset($_GET['debug']) && $_GET['debug']=='1') {
                $debugInfo = [
                    'raw_rows' => count($lista),
                    'distinct_ids' => count($ids),
                    'returned_etiquetas' => count($etiquetas),
                    'ids' => $ids,
                ];
            }

            $response_status = 200;
            $payload = ['success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$etiquetas];
            if ($debugInfo) { $payload['debug'] = $debugInfo; }
            $data = $payload;

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

?>
