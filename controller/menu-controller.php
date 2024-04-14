<?php
$app->get('/controle-menu', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/menu-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/controle-menu1', function() use ($app){
    if (valida_logado(true)) {
        die('Teste');
    } else {
        $app->notFound();
    }
});

$app->post('/menu-salvar', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {
        
        $nome = $app->request->post('nome');
        $descricao = $app->request->post('descricao');
        $link = $app->request->post('link');
        $icone = $app->request->post('icone');
        $tipo = $app->request->post('tipo');
        $status = $app->request->post('status');
        $id_menu_principal = $app->request->post('id_menu_principal');
        

        if (empty($nome)) {
            $erro = 'É necessário informar o nome.';
        }

        if (empty($status)) {
            $erro = 'É necessário informar o status.';
        }

        if (empty($tipo)) {
            $erro = 'É necessário informar o tipo.';
        }

        if (empty($erro)) {

            $class_menu = new MenuModel();
            //$data = $class_menu->login($email, md5($senha));
            $data = false;
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