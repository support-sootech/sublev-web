<?php
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

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
    $dt_vencimento_material = $_GET['dt_venc'];

    /*$qrCode = new QrCode('https://ootech.com.br/');  
    $qrCode->setSize(100);
    $qrCode->setWriterByName('png');
    echo '<img src="'.$qrCode->writeDataUri().'">';*/
    
   
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

    try {
        
        $class_etiquetas = new EtiquetasModel();
        $data_etiqueta = array();
        $id_etiquetas = '';
        $data_etiqueta['id_etiquetas'] = '';
        $data_etiqueta['descricao'] = 'Etiqueta '.$arr['descricao'].' - '.dt_br(date("Ymd"));
        $data_etiqueta['codigo'] = $arr['cod_barras'];
        $data_etiqueta['id_materiais_fracionados'] = $arr['id_materiais_fracionados'];
        $data_etiqueta['id_materiais'] = $arr['id_materiais'];
        $data_etiqueta['status'] = 'A';
        $status_data_etiqueta = $class_etiquetas->add($data_etiqueta);
        
        if ($status_data_etiqueta) {
            $status = 200;
            $retorno = array(
                'success'=>true, 
                'type'=>'success', 
                'msg'=>messagesDefault(!empty($id_etiquetas) ? 'update' : 'register'),
                'data'=>$status_data_etiqueta
            );
        } else {
            $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$data);    
        }   
    } catch (Exception $e) {
        $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
    }
    
    $client = new GuzzleHttp\Client();
    $res = $client->request('GET', 'https://arodevsistemas.com.br/qrcode3/'.$status_data_etiqueta);
    $data_qrcode = json_decode($res->getBody(), true);

    $html  = "<table align='center' style='page-break-inside:avoid; alignpadding: 0mm; width: 100mm;height: 50mm;border: 0.5mm solid black;'>";
    $html .= "<tr><td><h4>Material: ".$data['descricao']."</h4><br>";
    $html .= "<h4>Marca: ".$data['marca']."</h4><br>";
    $html .= "<h4>Data de Manipulação: ".$data['dt_fracionamento']."</h4><br>";
    $html .= "<h4>Data de Vencimento: ".$data['dt_vencimento']."</h4><br>";
    $html .= "<h4>Manipulado por: Victor Carvalho</h4></td><br>";
    $html .= "<td><img height='140' width='140' src='".$data_qrcode['img']."'></td></tr></table>";
    
    $mpdf = new \Mpdf\Mpdf(
        [
            'mode' => 'utf-8', 
            'format' => [100, 50],
            'margin_left' => 3,
            'margin_right' => 3,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 5

        ]
    );

    $mpdf->WriteHTML($html);
    $mpdf->Output();

});

?>