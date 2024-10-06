<?php

use Slim\View;

$app->get('/relatorio-materiais-recebimento', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/relatorio-materiais-recebimento-page.php');
    } else {
        $app->notFound();
    }
});

$app->post('/relatorio-materiais-recebimento', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        
        try {

            $arr_perfil = array_column($_SESSION['usuario']['perfil'], 'ds_perfil');
            $fg_root = array_search('ROOT', $arr_perfil);
            
            $id_empresas = $_SESSION['usuario']['id_empresas'];
            if ($fg_root >= 0) {
                $id_empresas = '';
            }

            $dt_ini = $app->request->post('fil_dt_ini');
            $dt_fim = $app->request->post('fil_dt_fim');
            $tipo = $app->request->post('tipo');

            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadRelatorioMateriaisRecebimento($id_empresas, $dt_ini, $dt_fim);
            if ($arr) {
                foreach ($arr as $key => $value) {
                    if (!empty($value['dh_cadastro'])) {
                        $value['dh_cadastro'] = dh_br($value['dh_cadastro']);
                    }

                    if (!empty($value['dt_vencimento'])) {
                        $value['dt_vencimento'] = dt_br($value['dt_vencimento']);
                    }

                    if (!empty($value['peso'])) {
                        $value['peso'] = numberformat($value['peso'], false);
                    }

                    $data['data'][] = $value;
                }
            }
        } catch (Exception $e) {
            die('ERROR: '.$e->getMessage().'');
        }       

    }

    if ($tipo=='pdf') {
        
        if (isset($data['data']) && count($data['data']) > 0) {
            $table = '<h4 style="text-align:center">PLANILHA DE CONTROLE DE RECEBIMENTO DE PRODUTOS PERECÍVEIS (CONGELADOS / RESFRIADOS)</h4>';
            $table.= '<h5 style="text-align:center">ESTABELECIMENTO: '.$_SESSION['usuario']['nm_empresa'].' - RESPONSÁVEL: '.$_SESSION['usuario']['nm_pessoa'].' - PERÍODO '.$dt_ini.' até '.$dt_fim.' </h5>';
            $table.= '<h6 style="text-align:center">gerado: '.date('d/m/Y H:i:s').'</h6>';
            //$table.= '<br><br>';

            $table.= '<table style="width:100%;border: 1px solid black;border-collapse: collapse; font-size:12px">';
                $table.= '<thead>';
                    $table.= '<tr>';
                        $table.= '<th style="border:1px solid">DATA / HORA</th>';
                        $table.= '<th style="border:1px solid">MATERIAL</th>';
                        $table.= '<th style="border:1px solid">FORNECEDOR</th>';
                        $table.= '<th style="border:1px solid">VALIDADE</th>';
                        $table.= '<th style="border:1px solid">QTDE</th>';
                        $table.= '<th style="border:1px solid">TEMPERATURA</th>';
                        $table.= '<th style="border:1px solid">SIF</th>';
                        $table.= '<th style="border:1px solid">LOTE</th>';
                        $table.= '<th style="border:1px solid">Nº NOTA</th>';
                        $table.= '<th style="border:1px solid">CONDIÇÕES EMBALAGENS</th>';
                        $table.= '<th style="border:1px solid">RESPONSÁVEL</th>';
                    $table.= '</tr>';
                $table.= '</thead>';
                $table.= '<tbody>';
                    foreach ($data['data'] as $key => $value) {
                        $table.= '<tr>';
                            $table.= '<td style="border:1px solid">'.$value['dh_cadastro'].'</td>';
                            $table.= '<td style="border:1px solid">'.$value['descricao'].'</td>';
                            $table.= '<td style="border:1px solid">'.$value['nm_fornecedor'].'</td>';
                            $table.= '<td style="border:1px solid">'.$value['dt_vencimento'].'</td>';
                            $table.= '<td style="border:1px solid; text-align:center">'.$value['quantidade'].'</td>';
                            $table.= '<td style="border:1px solid; text-align:center">'.(!empty($value['temperatura']) ?$value['temperatura'].'ºC' : '').'</td>';
                            $table.= '<td style="border:1px solid; text-align:center">'.$value['sif'].'</td>';
                            $table.= '<td style="border:1px solid">'.$value['lote'].'</td>';
                            $table.= '<td style="border:1px solid">'.$value['nro_nota'].'</td>';
                            $table.= '<td style="border:1px solid">'.$value['ds_embalagem_condicoes'].'</td>';
                            $table.= '<td style="border:1px solid">'.$value['nm_responsavel'].'</td>';
                        $table.= '</tr>';
                    }
                $table.= '</tbody>';
            $table.= '</table>';

            ini_set('max_execution_time', 600);
            $app->response->headers->set('Content-Type', 'application/pdf');
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'orientation' => 'L',
                'tempDir' =>'/tmp',
                'default_font' => 'arial'
            ]);

            if(is_array($table)){
                $style = 2;
                foreach($table as $h){
                    if($h==='<quebra_pagina>') {
                        $mpdf->AddPage();
                        $mpdf->WriteHTML($h, $style);
                    }else{
                        $mpdf->WriteHTML($h, $style);
                    }
                }
            }else{
                $mpdf->WriteHTML($table, 2);
            }
            $mpdf->Output('relatorio_materiais_recebimento_'.date('dmYHis').'.pdf', \Mpdf\Output\Destination::INLINE);

            echo $table;
            
        } else {
            die('<center><h5>Nenhum item localizado.</h5></center>');
        }



    } else {
        $response = $app->response();
        $response['Access-Control-Allow-Origin'] = '*';
        $response['Access-Control-Allow-Methods'] = 'POST';
        $response['Content-Type'] = 'application/json';
    
        $response->status($status);
        $response->body(json_encode($data));
    }

});
?>