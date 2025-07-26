<?php
$app->get('/fracionar-material', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/fracionar-material-page.php');
    } else {
        $app->notFound();
    }
});

$app->post('/fracionar-material-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $id_empresas = getIdEmpresasLogado();
        $class_materiais_categorias = new MateriaisCategoriasModel();
        $arr_materiais_categorias = $class_materiais_categorias->loadAll($id_empresas, 'A');
        if ($arr_materiais_categorias) {
            foreach ($arr_materiais_categorias as $key => $value) {
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

$app->post('/fracionar-materiais', function() use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {

        $id_materiais = $app->request->post('id_materiais');
        $arr_quantidades = $app->request->post('arr_quantidades');

        if (!empty($id_materiais)) {
            
            $data = fracionarMateriais($id_materiais, $arr_quantidades);
            $status = isset($data['success']) ? 200 : 400;
            
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


$app->get('/materiais-fracionados', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/materiais-fracionados-page.php');
    } else {
        $app->notFound();
    }
});

$app->post('/materiais-fracionados-json', function() use ($app){
    $status = 200;
	$data['data'] = array();

    $tipo_retorno = $app->request->post('tipo') ? $app->request->post('tipo') : 'json';

    if (valida_logado()) {
        
        try {
            $id_empresas = getIdEmpresasLogado();

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }

            $id_setor = '';
            if ($app->request->post('id_setor')) {
                $id_setor = $app->request->post('id_setor');
            }

            $id_usuarios = '';
            if ($app->request->post('id_usuarios')) {
                $id_usuarios = $app->request->post('id_usuarios');
            }
    
            $class_materiais = new MateriaisFracionadosModel();
            $arr = $class_materiais->load(
                $id_empresas, 
                $status,
                $id_setor,
                $id_usuarios
            );
            if ($arr) {
                foreach ($arr as $key => $value) {
                    $data['data'][] = $value;
                }
            }
        } catch (Exception $e) {
            die('ERROR: '.$e->getMessage().'');
        }
        

    }

    if ($tipo_retorno=='pdf') {

        ini_set('max_execution_time', 600);
		$app->response->headers->set('Content-Type', 'application/pdf');
		$mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'orientation' => 'L',
            'tempDir' =>'/tmp'
        ]);

		$html = '';

        $html.= '<h3 style="text-align:center">Lista de materiais fracionados</h3>';
        $html.= '<h5 style="text-align:center">gerado: '.date('d/m/Y H:i:s').'</h5>';
		$html.= '<br><br>';

		$html.= '<table style="width:100%">';
			$html.= '<thead>';
				$html.= '<tr style="border-bottom:1px solid">';
					$html.= '<th style="text-align:left" >Código</th>';
					$html.= '<th style="text-align:left">Fracionado</th>';
                    $html.= '<th style="text-align:left">Material</th>';
                    $html.= '<th style="text-align:left">Peso</th>';
                    $html.= '<th style="text-align:left">Vencimento</th>';
                    $html.= '<th style="text-align:left">Status</th>';
                    $html.= '<th style="text-align:left">Usuário</th>';
				$html.= '</tr>';
			$html.= '</thead>';
			$html.= '<tbody>';

                foreach ($data['data'] as $key => $value) {
                    $html.= '<tr>';
                        $html.= '<td>'.$value['id_materiais_fracionados'].'</td>';
                        $html.= '<td>'.$value['dt_fracionamento'].'</td>';
                        $html.= '<td>'.$value['ds_materiais'].'</td>';
                        $html.= '<td>'.$value['qtd_fracionada_formatado'].' '.$value['ds_unidade_medida'].'</td>';
                        $html.= '<td>'.$value['dt_vencimento'].'</td>';
                        $html.= '<td>'.$value['ds_status'].'</td>';
                        $html.= '<td>'.$value['nm_usuario'].'</td>';
                    $html.= '</tr>';
                }

			$html.= '</tbody>';
			
		$html.= '</table>';

		if(is_array($html)){
			$style = 2;
			foreach($html as $h){
				if($h==='<quebra_pagina>') {
					$mpdf->AddPage();
					$mpdf->WriteHTML($h, $style);
				}else{
					$mpdf->WriteHTML($h, $style);
				}
			}
		}else{
			$mpdf->WriteHTML($html, 2);
		}
		$mpdf->Output('relatorio_materiais_fracionados_'.date('dmYHis').'.pdf', \Mpdf\Output\Destination::INLINE);
        
    } else {
        $response = $app->response();
        $response['Access-Control-Allow-Origin'] = '*';
        $response['Access-Control-Allow-Methods'] = 'POST';
        $response['Content-Type'] = 'application/json';
    
        $response->status($status);
        $response->body(json_encode($data));
    }
});

$app->get('/materiais-fracionados-baixa', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/materiais-fracionados-baixa-page.php');
    } else {
        $app->notFound();
    }
});

$app->post('/materiais-fracionados-status', function() use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        try {
            $id_materiais_fracionados = $app->request->post('id_materiais_fracionados');;
            $arr['id_usuarios'] = $_SESSION['usuario']['id_usuarios'];
            $arr['status'] = $app->request->post('status');
            $arr['motivo_descarte'] = $app->request->post('motivo');

            if (empty($id_materiais_fracionados)) {
                throw new Exception('É necessário informar o material!');
            }

            if (empty($arr['status'])) {
                throw new Exception('É necessário informar o status!');
            }

            if ($arr['status']=='D' && empty($arr['motivo_descarte'])) {
                throw new Exception('Para descarte do material é necessário informar o motivo!');
            }

            $class_materiais = new MateriaisFracionadosModel();

            $material_fracionado = $class_materiais->loadId($id_materiais_fracionados);
            $material_fracionado['status'] = $arr['status'];
            $material_fracionado['id_usuarios'] = $arr['id_usuarios'];
            $material_fracionado['motivo_descarte'] = $arr['motivo_descarte'];
    
            $salvar = $class_materiais->edit($material_fracionado, array('id_materiais_fracionados'=>$id_materiais_fracionados));
            $status = 200;
            $data = array('success'=>true, 'type'=>'success', 'msg'=>'Registro alterado com sucesso!');
        } catch (Exception $e) {
            $data = array('error'=>true, 'type'=>'danger', 'msg'=>$e->getMessage());
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