<?php
$app->post('/login', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {
        
        $cpf = $app->request->post('cpf');
        $senha = $app->request->post('senha');

        if (empty($cpf)) {
            $erro = 'É necessário informar o CPF.';
        }

        if (empty($senha)) {
            $erro = 'É necessário informar a senha.';
        }

        if (empty($erro)) {

            $class_usuarios = new UsuariosModel();
            $data = $class_usuarios->login($cpf, md5($senha));
            
            if ($data) {
                $status = 200;
                $_SESSION['usuario'] = $data;
                $retorno = array('success'=>true, 'type'=>'success', 'msg'=>'OK.', 'page'=>'/dashboard', );
            } else {
                $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>'E-mail ou senha incorreto.');    
            }
            
        } else {
            $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$erro);
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
?>