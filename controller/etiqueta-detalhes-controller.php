<?php

$app->get('/etiqueta-detalhes', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/etiqueta-detalhes-page.php');
    } else {
        $app->notFound();
    }
});

$app->post('/detalhes-etiqueta-json', function() use ($app){
    $status = 200;
	$data = array();
   
    if (valida_logado()) {
        
        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];
            
            $id_etiqueta = '';
            if ($app->request->post('id_etiqueta')) {
                $id_etiqueta = $app->request->post('id_etiqueta');
            }
    
            $class_etiquetas = new EtiquetasModel();
            $arr = $class_etiquetas->loadEtiquetaDetalhes($id_etiqueta);
            if ($arr) {
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

/*$app->get('/fracionar-imprimir-material', function() use ($app){


    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'GET';
	$response['Content-Type'] = 'application/pdf';
    $id_materiais = $_GET['id'];
    $dt_vencimento_material = $_GET['dt_venc'];

    $client = new GuzzleHttp\Client();
    $res = $client->request('GET', 'https://arodevsistemas.com.br/qrcode3/victor');
    $data_qrcode = json_decode($res->getBody(), true);
   
   
    $data = array();

    if (!empty($id_materiais)) {
        
        $arr_qtd_fracionada = array();
        $data = fracionarMateriais($id_materiais, $dt_vencimento_material, $arr_qtd_fracionada);
        $status = isset($data['success']) ? 200 : 400;
        
    } else {
        $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
    }

    $class_materiais = new MateriaisModel();
    $arr = $class_materiais->loadIdMaterialDetalhes('A',$id_materiais);
    if ($arr) {
        $data = $arr;
    }
    
    $html  = "<table align='center' style='page-break-inside:avoid; alignpadding: 0mm; width: 100mm;height: 30mm;border: 0.5mm solid black;'>";
    $html .= "<tr><td><h5>Material: ".$data['descricao']."</h5>";
    $html .= "<h5>Marca: ".$data['marca']."</h5>";
    $html .= "<h5>Data de Manipulação: ".$data['dt_fracionamento']."</h5>";
    $html .= "<h5>Data de Vencimento: ".$data['dt_vencimento']."</h5>";
    $html .= "<h5>Manipulado por: Victor Carvalho</h5></td>";
    $html .= "<td><img src='".$data_qrcode['img']."'></td></tr></table>";
    
    $mpdf = new \Mpdf\Mpdf(
        [
            'mode' => 'utf-8', 
            'format' => [100, 50],
            'margin_left' => 3,
            'margin_right' => 3,
            'margin_top' => 3,
            'margin_bottom' => 3,
            'margin_header' => 3,
            'margin_footer' => 3

        ]
    );

    $mpdf->WriteHTML($html);
    $mpdf->Output();

});*/

?>