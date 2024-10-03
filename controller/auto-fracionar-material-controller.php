<?php
$app->get('/auto-fracionar-material', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/auto-fracionar-material-page.php');
    } else {
        $app->notFound();
    }
});

$app->post('/buscar-material-cod-barras', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        
        $id_empresas = $_SESSION['usuario']['id_empresas'];
        $cod_barras = $app->request->post('cod_barras');

        $class_materiais = new MateriaisModel();
        $arr_materiais = $class_materiais->loadCodBarrasMaterialDetalhes('', $cod_barras);
        if ($arr_materiais) {
            $data = $arr_materiais;
        }
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';
    
	$response->status($status);
	$response->body(json_encode($data));
});

$app->get('/fracionar-imprimir-material', function() use ($app){

    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'GET';
	$response['Content-Type'] = 'application/pdf';
    $id_materiais = $_GET['id'];

    $url = "https://ootech.com.br";
    $qr = '<barcode code="'.$url.'" type="QR" class="barcode" size="0.8" error="M" disableborder="1" />';

    $data = array();

    if (!empty($id_materiais)) {
        
        $arr_qtd_fracionada = array();
        $data = fracionarMateriais($id_materiais,$arr_qtd_fracionada);
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
    $html .= "<tr><td><h5>Material: ".$data['descricao']."</h5></td></tr>";
    $html .= "<tr><td><h5>Marca: ".$data['marca']."</h5></td></tr>";
    $html .= "<tr><td><h5>Data de Manipulação: ".$data['dt_fracionamento']."</h5></td></tr>";
    $html .= "<tr><td><h5>Data de Vencimento: ".$data['dt_vencimento']."</h5></td></tr>";
    $html .= "<tr><td><h5>Data de Venc. após aberto: ".$data['dt_vencimento_aberto']."</h5></td></tr>";
    $html .= "<tr><td><h5>Manipulado por: Victor Carvalho</h5></td></tr></table>";

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

});

?>