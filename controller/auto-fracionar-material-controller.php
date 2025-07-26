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

        $id_empresas = getIdEmpresasLogado();

        $class_computadores = new ComputadoresModel();
        $arr_computadores = $class_computadores->loadIdUsuarios($_SESSION['usuario']['id_usuarios'], '', $id_empresas);

        $app->render('/auto-fracionar-material-page.php', array('arr_computadores'=>$arr_computadores));
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

$app->post('/auto-fracionar-material', function() use ($app){
    $response_status = 400;
    $data = array();
    if (valida_logado()) {

        try {
            $id_materiais = $app->request->post('id_materiais');
            $dt_vencimento_material = $app->request->post('dt_vencimento');

            $id_empresas = getIdEmpresasLogado();
           
            $class_materiais = new MateriaisModel();
            $material = $class_materiais->loadIdMaterialDetalhes('A',$id_materiais,$id_empresas);

            if ($material) {
               
                $fg_fracionado = fracionarMateriais(
                    $id_materiais, 
                    $dt_vencimento_material, 
                    array(),
                    'UNIDADE',
                    true,
                    $_SESSION['usuario']['id_usuarios']
                );
                
                if ($fg_fracionado['success']) {
                    $response_status = 200;
                    $class_etiquetas = new EtiquetasModel();

                    foreach ($fg_fracionado['arr_fracionado'] as $key => $value) {
                        $arr_etiqueta = array();
                        $id_etiquetas = '';
                        $arr_etiqueta['id_etiquetas'] = '';
                        $arr_etiqueta['descricao'] = 'Etiqueta '.$material['descricao'].' - '.dt_br(date("Ymd"));
                        $arr_etiqueta['codigo'] = $material['cod_barras'];
                        $arr_etiqueta['id_materiais_fracionados'] = $material['id_materiais_fracionados'];
                        $arr_etiqueta['id_materiais'] = $material['id_materiais'];
                        $arr_etiqueta['status'] = 'A';
                        $arr_etiqueta['id_usuarios'] = $_SESSION['usuario']['id_usuarios'];
                        $data_etiqueta = $class_etiquetas->add($arr_etiqueta);
                    }

                    

                    $data = array('success'=>true, 'type'=>'success', 'msg'=>'Fracionamento efetuado com sucesso!', 'id_etiquetas'=>$data_etiqueta);
                } else {
                    $data = array('error'=>true, 'type'=>'danger', 'msg'=>$fg_fracionado['msg']);
                }
            }
        } catch (Exception $e) {
            $data = array('error'=>true, 'type'=>'danger', 'msg'=>$e->getMessage());
        }
    }

    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';
    
	$response->status($response_status);
	$response->body(json_encode($data));
});

$app->get('/fracionar-imprimir-material/:id_etiquetas', function($id_etiquetas='') use ($app){

    if (!empty($id_etiquetas)) {        
        
        try {

            $id_empresas = getIdEmpresasLogado();
            $class_etiquetas = new EtiquetasModel();
            $etiqueta = $class_etiquetas->loadId($id_etiquetas);
    
            $class_usuarios = new UsuariosModel();
            $usuario = $class_usuarios->loadId($etiqueta['id_usuarios']);
    
            $class_materiais = new MateriaisModel();
            $material = $class_materiais->loadIdMaterialDetalhes('A',$etiqueta['id_materiais'],$id_empresas);            

            if ($etiqueta) {
                $client = new GuzzleHttp\Client();
                $res = $client->request('GET', 'https://arodevsistemas.com.br/qrcode3/'.$etiqueta['id_etiquetas']);
                $data_qrcode = json_decode($res->getBody(), true);

                $html = "<table align='center' style='page-break-inside:avoid; alignpadding: 0mm; width: 100%;height: 29mm;border: 0.5mm solid black;'>";
                    $html.="<tr>";
                        $html.="<td>";  
                            $html.="<p>Material: ".$material['descricao']."</p>";
                            $html.="<p>Marca: ".$material['marca']."</p>";
                            $html.="<p>Data de Manipulação: ".$material['dt_fracionamento']."</p>";
                            $html.="<p>Data de Vencimento: ".$material['dt_vencimento']."</p>";
                            $html.="<p>Manipulado por: ".(isset($usuario['nm_pessoa']) ? $usuario['nm_pessoa'] : '')."</p>";
                        $html.="</td>";
                        $html.='<td><img src="'.$data_qrcode['img'].'"/></td>';
                    $html.="</tr>";
                $html.= "</table>";

                $mpdf = new \Mpdf\Mpdf(
                    [
                        //'mode' => 'utf-8', 
                        'format' => [160, 50],
                        'margin_left' => 4,
                        'margin_right' => 4,
                        'margin_top' => 2,
                        'margin_bottom' => 2,
                        'margin_header' => 1,
                        'margin_footer' => 1,
                        //'orientation' => 'L',
                        'tempDir' => './temp'
                    ]
                );

                $response = $app->response();
                $response['Access-Control-Allow-Origin'] = '*';
                $response['Access-Control-Allow-Methods'] = 'GET';
                $response['Content-Type'] = 'application/pdf';
            
                $mpdf->WriteHTML($html);
                
                $mpdf->Output('etiqueta.pdf', \Mpdf\Output\Destination::INLINE);
            } else {
                $response = $app->response();
                $response['Access-Control-Allow-Origin'] = '*';
                $response['Access-Control-Allow-Methods'] = 'GET';
                $response['Content-Type'] = 'application/json';
                
                $response->status(400);
                $response->body(json_encode(array('ERRO AO GERAR A ETIQUETA')));
            }
        } catch (Exception $e) {
            $response = $app->response();
            $response['Access-Control-Allow-Origin'] = '*';
            $response['Access-Control-Allow-Methods'] = 'GET';
            $response['Content-Type'] = 'application/json';
            
            $response->status(400);
            $response->body(json_encode(array('ERRO AO GERAR A ETIQUETA ('.$e->getMessage().')')));
        }
    }
});

?>