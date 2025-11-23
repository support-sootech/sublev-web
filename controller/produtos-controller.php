<?php
$app->get('/controle-produtos', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/produtos-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/produtos-edit/:id_produtos', function($id_produtos='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_produtos = new ProdutosModel();

        if (!empty($id_produtos)) {
            $arr = $class_produtos->loadId($id_produtos);
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

$app->get('/produtos-del/:id_produtos', function($id_produtos='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_produtos = new ProdutosModel();

        if (!empty($id_produtos)) {
            $del = $class_produtos->del($id_produtos);
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

$app->post('/prod-autocomplete-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    // Permitir fallback por headers (Token-User) assim como outros controllers
    $logado = valida_logado() || (function_exists('_getHeaderValue') && _getHeaderValue('Token-User'));
    if ($logado) {
        try {
            $id_empresas = getIdEmpresasLogado();
            $flagListaCampo = '';
            $campo = '';
            $flagListaCampo = '';
            if ($app->request->post('flagListaCampo')) {
                $flagListaCampo = $app->request->post('flagListaCampo');
            }
            if ($app->request->post('campo')) {
                $campo = $app->request->post('campo');
            }
            // Suporte a JSON: se não vier via form-urlencoded, tentar extrair do corpo
            if ((empty($flagListaCampo) || empty($campo)) && function_exists('retornaParametros')) {
                $params = retornaParametros($app);
                if (empty($flagListaCampo) && isset($params['flagListaCampo'])) {
                    $flagListaCampo = $params['flagListaCampo'];
                }
                if (empty($campo) && isset($params['campo'])) {
                    $campo = $params['campo'];
                }
            }
            // Sanitização simples
            $flagListaCampo = trim($flagListaCampo);
            $campo = trim($campo);
            $class_produtos = new ProdutosModel();
            $arr = $class_produtos->loadProdutos($campo, $flagListaCampo);
            
            if ($arr) {
                foreach ($arr as $key => $value) {
                    $data['data'][] = $value;
                }
            }
            // Adicionar metadados mínimos para depuração
            $data['success'] = true;
            $data['msg'] = 'OK';
            $data['q'] = array('flag'=>$flagListaCampo, 'campo'=>$campo);
        } catch (Exception $e) {
            // Em vez de die (interrompe PHP e pode retornar HTML), retornar estrutura JSON padronizada
            $status = 500;
            $data = array('success'=>false, 'type'=>'danger', 'msg'=>'Erro interno: '.$e->getMessage());
        }
    } else {
        // Não autenticado: manter status 401 para permitir tratamento claro no app
        $status = 401;
        $data = array('success'=>false, 'type'=>'danger', 'msg'=>'Não autorizado');
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST, OPTIONS';
	$response['Access-Control-Allow-Headers'] = 'Content-Type, Token-User, X-Company-Id';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	// Padronizar resposta: sempre incluir chave data como array (para compatibilidade existente)
	if (!isset($data['data'])) {
		$data = array_merge(array('data'=>array()), $data);
	}
	$response->body(json_encode($data));
});

$app->post('/produtos-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    $total = array();
    $totalRecords = 0;
    $output = array();
    $order_by = '';
    if (valida_logado()) {

        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
            
            $draw = $_REQUEST['draw'];
            $start = $_REQUEST['start'];
            $length = $_REQUEST['length'];
            $columns = array( 
                            0 => 'codigo_barras', 
                            1 => 'descricao',
                            2 => 'dias_vencimento',
                            3 => 'dias_vencimento_aberto',
                            4 => 'status'
                        );

            if (!empty($_REQUEST['order'])){
                $order_by = " ORDER BY ".$columns[$_REQUEST['order'][0]['column']]." ".$_REQUEST['order'][0]['dir']; 
            }

            $where = '';
            if(!empty($_REQUEST['search']['value'])) { 
                $where .= " AND  ( id_produtos LIKE '%".$_REQUEST['search']['value']."%' ";    
                $where .= " OR descricao LIKE '%".$_REQUEST['search']['value']."%' ";
                $where .= " OR dias_vencimento LIKE '%".$_REQUEST['search']['value']."%' ";
                $where .= " OR dias_vencimento_aberto LIKE '%".$_REQUEST['search']['value']."%' ";
                $where .= " OR status LIKE '%".$_REQUEST['search']['value']."%' )";
                
            }
            
            $class_produtos = new ProdutosModel();
            $arr = $class_produtos->loadAll($status,$start,$length,$order_by,$where);
            if ($arr) {
                foreach ($arr as $key => $value) {
                    
                    if (!empty($value['peso'])) {
                        $value['peso'] = numberformat($value['peso'], false);
                    }

                    $data['data'][] = $value;
                }
            }
            $total = $class_produtos->countAll($status,$where);
            $totalRecords = $total[0]['total'];
            //$totalRecords = count($arr);
    
            //die('Draw = '.$draw.' - Start = '.$start.' - LENGTH = '.$length.' - Total Records = '.$totalRecords);
        } catch (Exception $e) {
            die('ERROR: '.$e->getMessage().'');
        }

    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';
    
	$response->status($status);
    $output = array(
        "draw" => intval($draw),
        "recordsTotal" => intval($totalRecords), // Total records in DB
        "recordsFiltered" => intval($totalRecords), // Records after filtering
        "data" => $data['data'] // Array of data for the current page
    );
    
	$response->body(json_encode($output));
});

$app->post('/produtos-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_produtos = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('produtos_', '', $key))] = $value;
            }
    
            if (isset($post['id_produtos'])) {
                $id_produtos = $post['id_produtos'];
                unset($post['id_produtos']);
            }

            $post['id_empresas'] = $_SESSION['usuario']['id_empresas'];
            
            try {
                $class_produtos = new ProdutosModel();
    
                if (!empty($id_produtos)) {
                    $data = $class_produtos->edit($post, array('id_produtos'=>$id_produtos));
                } else {
                    $data = $class_produtos->add($post);
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id_produtos) ? 'update' : 'register'),
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

$app->get('/produtos-busca-codigo-barras/:codigo_barras', function($codigo_barras='') use ($app){

    $status = 400;
	$data = array();
    if (valida_logado() || 1==1) {
        $class_produtos = new ProdutosModel();

        if (!empty($codigo_barras)) {

            $arr = $class_produtos->loadCodigoBarras($codigo_barras);
            if ($arr) {
                $status = 200;

                $arr['dt_vencimento'] = dt_br(somar_dias(date('Y-m-d'), $arr['dias_vencimento']));
                $arr['dt_vencimento_aberto'] = dt_br(somar_dias(date('Y-m-d'), $arr['dias_vencimento_aberto']));
                if (!empty($arr['peso'])) {
                    $arr['peso'] = numberformat($arr['peso'], false);
                }

                $data = array('success'=>true, 'type'=>'success', 'msg'=>'Produto já cadastrado!', 'data'=>$arr);
            } else {
                /*
                try {
                    //$produto = buscaProdutosCodigoBarras($codigo_barras);

                    $ch = curl_init();
                    // set url
                    curl_setopt($ch, CURLOPT_URL, "https://pt.product-search.net/?q=".$codigo_barras);
                    //return the transfer as a string
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    // $output contains the output string
                    $produto = curl_exec($ch);
                    // close curl resource to free up system resources
                    curl_close($ch);  
    
                    $data = array('success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$produto);
                    $status = 200;
                } catch (Exception $e) {
                    $data = array('success'=>true, 'type'=>'success', 'msg'=>$e->getMessage());
                }
                */
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

$app->post('/produtos-calcula-datas', function() use ($app){

    $status = 400;
	$data = array();
    if (valida_logado()) {

        $qtd_dias_vencimento = $app->request->post('qtd_dias_vencimento');
        $qtd_dias_vencimento_aberto = $app->request->post('qtd_dias_vencimento_aberto');
        $dt_fabricacao = $app->request->post('dt_fabricacao');

        $dt_vencimento = '';
        $dt_vencimento_aberto = '';

        if (!empty($qtd_dias_vencimento) && !empty($qtd_dias_vencimento_aberto) && $dt_fabricacao) {

            $dt_vencimento = dt_br(somar_dias(dt_banco($dt_fabricacao), $qtd_dias_vencimento));
            $dt_vencimento_aberto = dt_br(somar_dias(dt_banco($dt_fabricacao), $qtd_dias_vencimento_aberto));
            $status = 200;
            $data = array(
                'success'=>true, 
                'type'=>'success', 
                'msg'=>'OK!', 
                'data'=>array('dt_vencimento'=>$dt_vencimento, 'dt_vencimento_aberto'=>$dt_vencimento_aberto)
            );
        } else {
            $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
        }
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

?>