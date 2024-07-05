<?php
$app->get('/controle-fornecedores-fabricantes', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/fornecedores-fabricantes-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/controle-fornecedores-fabricantes-teste', function() use ($app){
    if (valida_logado(false)) {
        
        try {
            $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();
            $fornecedor = $class_fornecedores_fabricantes->loadId(10);
            //verMatriz($class_fornecedores);
            unset($fornecedor['nome']);
            unset($fornecedor['tp_juridico']);
            $add = $class_fornecedores_fabricantes->add($fornecedor);
            verMatriz($add);
        } catch (Exception $e) {            
            verMatriz(array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage()));
        }

    } else {
        $app->notFound();
    }
});

$app->get('/fornecedores-fabricantes-edit/:id_pessoas', function($id_pessoas='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();
        if (!empty($id_pessoas)) {
            $arr = $class_fornecedores_fabricantes->loadId($id_pessoas);
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

$app->get('/fornecedores-fabricantes-del/:id', function($id='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();

        if (!empty($id)) {
            $del = $class_fornecedores_fabricantes->del($id);
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

$app->post('/fornecedores-fabricantes-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();
        $id_empresas = $_SESSION['usuario']['id_empresas'];
        $id_tipos_pessoas = $app->request->post('id_tipos_pessoas');
        $status = $app->request->post('status');

        $data['data'] = $class_fornecedores_fabricantes->loadAll($id_empresas, $status, $id_tipos_pessoas);
    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->post('/fornecedores-fabricantes-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            
            $obj_pessoas = array();
            
            try {
                $class_fornecedores_fabricantes = new FornecedoresFabricantesModel();

                $obj_pessoas['id_pessoas'] = $app->request->post('fornecedores_fabricantes_id_pessoas');
                $obj_pessoas['nome'] = $app->request->post('fornecedores_fabricantes_nome');
                $obj_pessoas['tp_juridico'] = $app->request->post('fornecedores_fabricantes_tp_juridico');
                $obj_pessoas['cpf_cnpj'] = $app->request->post('fornecedores_fabricantes_cpf_cnpj');
                $obj_pessoas['status'] = $app->request->post('fornecedores_fabricantes_status');
                $obj_pessoas['email'] = $app->request->post('fornecedores_fabricantes_email');
                $obj_pessoas['dt_nascimento'] = $app->request->post('fornecedores_fabricantes_dt_nascimento');
                $obj_pessoas['id_empresas'] = $_SESSION['usuario']['id_empresas'];
                $obj_pessoas['id_tipos_pessoas'] = $app->request->post('fornecedores_fabricantes_id_tipos_pessoas');
                $obj_pessoas['telefone'] = $app->request->post('fornecedores_fabricantes_telefone');
                $obj_pessoas['cep'] = $app->request->post('fornecedores_fabricantes_cep');
                $obj_pessoas['logradouro'] = $app->request->post('fornecedores_fabricantes_logradouro');
                $obj_pessoas['numero'] = $app->request->post('fornecedores_fabricantes_numero');
                $obj_pessoas['complemento'] = $app->request->post('fornecedores_fabricantes_complemento');
                $obj_pessoas['bairro'] = $app->request->post('fornecedores_fabricantes_bairro');
                $obj_pessoas['cidade'] = $app->request->post('fornecedores_fabricantes_cidade');
                $obj_pessoas['estado'] = $app->request->post('fornecedores_fabricantes_estado');
                $obj_pessoas['cod_ibge'] = $app->request->post('fornecedores_fabricantes_cod_ibge');
                
                $id_pessoas = $obj_pessoas['id_pessoas'];
                //unset($obj_pessoas['id_pessoas']);
                if (empty($id_pessoas)) {
                    $data = $class_fornecedores_fabricantes->addFornecedoresFabricantes($obj_pessoas);
                    $id_pessoas = $data;
                } else {
                    $data = $class_fornecedores_fabricantes->edit($obj_pessoas, array('id_pessoas'=>$id_pessoas));
                }
                
                if ($data) {
                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(isset($obj_pessoas['id_pessoas']) && !empty($obj_pessoas['id_pessoas']) ? 'update' : 'register'),
                        'data'=>$data,
                        'obj_pessoas'=>$obj_pessoas,
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