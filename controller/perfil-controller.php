<?php
$app->get('/controle-perfil', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/perfil-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/perfil-edit/:id_perfil', function($id_perfil='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_perfil = new PerfilModel();

        if (!empty($id_perfil)) {
            $arr = $class_perfil->loadId($id_perfil);
            if ($arr) {
                $status = 200;
                $data = array('success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$arr);
            } else {
                $data = array('success'=>false, 'type'=>'danger', 'msg'=>'Perfil não localizado!');
            }
        } else {
            $data = array('success'=>false, 'type'=>'danger', 'msg'=>'Deve ser informado o código do perfil!');
        }
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'GET';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->post('/perfil-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_perfil = new PerfilModel();
        $arr_perfil = $class_perfil->loadAll();
        if ($arr_perfil) {
            foreach ($arr_perfil as $key => $value) {
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

$app->post('/controle-perfil-save', function() use ($app){
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