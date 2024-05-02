<?php
$app->get('/controle-fornecedores', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/fornecedores-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/fornecedores-edit/:id_usuarios', function($id_usuarios='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_usuarios = new UsuariosModel();
        $class_pessoas = new PessoasModel();

        if (!empty($id_usuarios)) {
            $arr = $class_usuarios->loadId($id_usuarios);
            if ($arr) {
                $status = 200;
                $data = array('success'=>true, 'type'=>'success', 'msg'=>'OK', 'data'=>$arr);
            } else {
                $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
            }
        } else {
            $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
        }
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'GET';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->get('/fornecedores-del/:id_perfil', function($id_perfil='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_usuarios = new PerfilModel();

        if (!empty($id_perfil)) {
            $del = $class_usuarios->del($id_perfil);
            if ($del) {
                $status = 200;
                $data = array('success'=>true, 'type'=>'success', 'msg'=>messagesDefault('delete'));
            } else {
                $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
            }
        } else {
            $data = array('success'=>false, 'type'=>'danger', 'msg'=>messagesDefault('register_not_found'));
        }
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'GET';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->post('/fornecedores-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_usuarios = new UsuariosModel();
        $data['data'] = $class_usuarios->loadAll(1);
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->post('/fornecedores-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_perfil = '';
            $post = array();
    
            $obj_usuarios = array();
            $obj_pessoas = array();
            
            try {
                $class_usuarios = new UsuariosModel();
                $class_pessoas = new PessoasModel();

                $obj_pessoas['id_pessoas'] = $app->request->post('usuarios_id_pessoas');
                $obj_pessoas['nome'] = $app->request->post('usuarios_nome');
                $obj_pessoas['tp_juridico'] = $app->request->post('usuarios_tp_juridico');
                $obj_pessoas['cpf_cnpj'] = $app->request->post('usuarios_cpf_cnpj');
                $obj_pessoas['genero'] = $app->request->post('usuarios_genero');
                $obj_pessoas['email'] = $app->request->post('usuarios_email');
                $obj_pessoas['dt_nascimento'] = $app->request->post('usuarios_dt_nascimento');
                $obj_pessoas['id_empresas'] = $_SESSION['usuario']['id_empresas'];
                $obj_pessoas['id_tipos_pessoas'] = 1; //USUARIOS
                $obj_pessoas['telefone'] = $app->request->post('usuarios_telefone');
                $obj_pessoas['cep'] = $app->request->post('usuarios_cep');
                $obj_pessoas['logradouro'] = $app->request->post('usuarios_logradouro');
                $obj_pessoas['numero'] = $app->request->post('usuarios_numero');
                $obj_pessoas['complemento'] = $app->request->post('usuarios_complemento');
                $obj_pessoas['bairro'] = $app->request->post('usuarios_bairro');
                $obj_pessoas['cidade'] = $app->request->post('usuarios_cidade');
                $obj_pessoas['estado'] = $app->request->post('usuarios_estado');
                $obj_pessoas['cod_ibge'] = $app->request->post('usuarios_cod_ibge');

                $id_pessoas = $obj_pessoas['id_pessoas'];
                unset($obj_pessoas['id_pessoas']);
                if (empty($id_pessoas)) {
                    $data_pessoas = $class_pessoas->add($obj_pessoas);
                    $id_pessoas = $data_pessoas;
                } else {
                    $data_pessoas = $class_pessoas->edit($obj_pessoas, array('id_pessoas'=>$id_pessoas));
                }

                $obj_usuarios['id_usuarios'] = $app->request->post('usuarios_id_usuarios');
                $obj_usuarios['senha'] = $app->request->post('usuarios_senha');
                $obj_usuarios['id_pessoas'] = (!empty($id_pessoas) ? $id_pessoas : '');
                $obj_usuarios['status'] = $app->request->post('usuarios_status');

                $id_usuarios = $obj_usuarios['id_usuarios'];
                if (empty($id_usuarios)) {
                    unset($obj_usuarios['id_usuarios']);
                    $data_usuarios = $class_usuarios->add($obj_usuarios);
                    $id_usuarios = $data_usuarios;
                } else {
                    $data_usuarios = $class_usuarios->edit($obj_usuarios, array('id_usuarios'=>$id_usuarios));
                }

                $data = true;
                /*
                if (!empty($id_perfil)) {
                    $data = $class_usuarios->edit($post, array('id_perfil'=>$id_perfil));
                } else {
                    $data = $class_usuarios->add($post);
                }
                */
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(isset($obj_usuarios['id_usuarios']) && !empty($obj_usuarios['id_usuarios']) ? 'update' : 'register'),
                        'data'=>$data,
                        'obj_pessoas'=>$obj_pessoas,
                        'data_pessoas'=>$data_pessoas,
                        'obj_usuarios'=>$obj_usuarios,
                        'data_usuarios'=>$data_usuarios,
                    );
                } else {
                    $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$data);    
                }   
            } catch (Exception $e) {
                $msg = messagesDefault($e->getCode());
                if (empty($msg)) {
                    $msg = $e->getMessage();
                }
                $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$msg);
            }
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