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
                $retorno = array('success'=>true, 'type'=>'success', 'msg'=>'OK.', 'page'=>'/home', );
            } else {
                $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('invalid_credentials'));    
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

$app->get('/register-password/:hash', function($hash) use ($app){
    $class_usuarios = new UsuariosModel();
    $usuario = $class_usuarios->loadHash($hash);
    if ($usuario) {
        $usuario['hash'] = md5($usuario['id_usuarios'].$usuario['email']);
        $app->render('/register-password-page.php', array('usuario'=>$usuario));
    } else {
        $app->notFound();
    }
});

$app->post('/register-password', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {
        
        $hash = $app->request->post('hash');
        $senha = $app->request->post('senha');
        $senha_confirm = $app->request->post('senha_confirm');

        if (empty($senha)) {
            $erro = 'É necessário informar a senha.';
        }

        if (empty($senha_confirm)) {
            $erro = 'É necessário informar a confirmação de senha.';
        }

        if ($senha != $senha_confirm) {
            $erro = 'A senha está diferente da confirmação de senha.';
        }

        if (empty($erro)) {

            $class_usuarios = new UsuariosModel();
            $data = $class_usuarios->loadHash($hash);

            if ($data) {
                try {
                    //$arr['senha'] = $senha;
                    //$arr['id_usuarios'] = $data['id_usuarios'];
                    //$arr['status'] = $data['status'];
                    //$arr['id_pessoas'] = $data['id_pessoas'];

                    $data['senha'] = $senha;

                    $edit = $class_usuarios->edit($data, array('id_usuarios'=>$data['id_usuarios']));
                    if ($edit) {
                        $status = 200;
                        $retorno = array(
                            'success'=>true, 
                            'type'=>'success', 
                            'msg'=>messagesDefault('register_password'), 
                            'page'=>'/',
                        );
                    } else {
                        $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$edit);
                    }
                } catch (Exception $e) {
                    $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
                }
            } else {
                $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('invalid_credentials'));    
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

$app->post('/reset-password', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {
        
        $email = $app->request->post('email');

        if (empty($email)) {
            $erro = 'É necessário informar o e-mail.';
        }

        if (empty($erro)) {

            $class_usuarios = new UsuariosModel();
            $data = $class_usuarios->loadEmail($email);
            if ($data) {
                try {
                    $class_envio_email = new EnvioEmailModel();
                    $envio = $class_envio_email->emailRegisterPassword($data['id_usuarios']);
                    if ($envio) {
                        $status = 200;
                        $retorno = array(
                            'success'=>true, 
                            'type'=>'success', 
                            'msg'=>messagesDefault('register_password_send'), 
                            'page'=>'/',
                        );
                    } else {
                        $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$envio);
                    }
                } catch (Exception $e) {
                    $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
                }
            } else {
                $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('invalid_credentials'));    
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