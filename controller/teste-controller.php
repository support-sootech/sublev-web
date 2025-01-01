<?php
    
$app->get('/teste-pdf', function() use ($app){
    
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'GET';
	$response['Content-Type'] = 'application/pdf';

    $url = "https://ootech.com.br";
    $qr = '<barcode code="'.$url.'" type="QR" class="barcode" size="0.8" error="M" disableborder="1" />';
    $html = "<table><tr><td>'.$qr.'</td><td>Teste</td></tr></table>";
    
    $mpdf = new \Mpdf\Mpdf(
        [
            'mode' => 'utf-8', 
            'format' => [100, 50]
        ]
);
    $mpdf->WriteHTML('<h5>'.$url.'</h5>');
    $mpdf->Output();
});

?>