<?php
$app->get('/controle-materiais', function() use ($app){
    if (valida_logado(true)) {
        $app->render('/materiais-page.php');
    } else {
        $app->notFound();
    }
});

$app->get('/materiais-edit/:id_materiais', function($id_materiais='') use ($app){
    $status = 200;
	$data = array();
    if (valida_logado()) {
        $class_materiais = new MateriaisModel();

        if (!empty($id_materiais)) {
            $arr = $class_materiais->loadId($id_materiais);
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

$app->get('/materiais-del/:id_materiais', function($id_materiais='') use ($app){
    $status = 400;
	$data = array();
    if (valida_logado()) {
        $class_materiais = new MateriaisModel();

        if (!empty($id_materiais)) {
            $del = $class_materiais->del($id_materiais);
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

$app->post('/materiais-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
    if (valida_logado()) {
        
        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
    
            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadAll($status);
            if ($arr) {
                foreach ($arr as $key => $value) {
                    $data['data'][] = $value;
                }
            }
        } catch (Exception $e) {
            die('ERROR: '.$e->getMessage().'');
        }
        

    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->post('/materiais-da-categoria-json', function() use ($app){
    $status = 200;
	$data['data'] = array();
   
    if (valida_logado()) {
        
        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
            
            $id_materiais_categorias = '';
            if ($app->request->post('id_materiais_categorias')) {
                $id_materiais_categorias = $app->request->post('id_materiais_categorias');
            }
    
            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadIdMaterialCategoria($status,$id_materiais_categorias);
            if ($arr) {
                foreach ($arr as $key => $value) {
                    $data['data'][] = $value;
                }
            }
        } catch (Exception $e) {
            die('ERROR: '.$e->getMessage().'');
        }
        

    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->post('/detalhes-materiais-json', function() use ($app){
    $status = 200;
	$data = array();
   
    if (valida_logado()) {
        
        try {
            $id_empresas = $_SESSION['usuario']['id_empresas'];

            $status = '';
            if ($app->request->post('status')) {
                $status = $app->request->post('status');
            }
            
            $id_materiais = '';
            if ($app->request->post('id_materiais')) {
                $id_materiais = $app->request->post('id_materiais');
            }
    
            $class_materiais = new MateriaisModel();
            $arr = $class_materiais->loadIdMaterialDetalhes($status,$id_materiais);
            if ($arr) {
                if(!empty($arr['peso'])) {
                    $arr['peso'] = numberformat($arr['peso'], false);
                }

                $data = $arr;
            }
        } catch (Exception $e) {
            die('ERROR: '.$e->getMessage().'');
        }
        

    }
    $response = $app->response();
	$response['Access-Control-Allow-Origin'] = '*';
	$response['Access-Control-Allow-Methods'] = 'POST';
	$response['Content-Type'] = 'application/json';

	$response->status($status);
	$response->body(json_encode($data));
});

$app->post('/materiais-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        if (valida_logado()) {            
            $id_materiais = '';
            $post = array();
    
            foreach ($app->request->post() as $key => $value) {
                $post[(str_replace('material_', '', $key))] = $value;
            }
    
            if (isset($post['id_materiais'])) {
                $id_materiais = $post['id_materiais'];
                unset($post['id_materiais']);
            } 
            
            if (!isset($post['status'])) {
                $post['status'] = 'A';
            }

            $post['id_empresas'] = $_SESSION['usuario']['id_empresas'];
            
            try {
                $class_materiais = new MateriaisModel();
    
                if (!empty($id_materiais)) {
                    $data = $class_materiais->edit($post, array('id_materiais'=>$id_materiais));
                } else {
                    $post['id_usuarios'] = $_SESSION['usuario']['id_usuarios'];
                    $data = $class_materiais->add($post);
                }
                
                if ($data) {

                    $class_produtos = new ProdutosModel();
                    $add_produto = false;
                    $fg_produto = $class_produtos->loadCodigoBarras($post['cod_barras']);
                    if (!$fg_produto) {
                        $class_produtos->add(
                            array(
                                'descricao'=>$post['descricao'],
                                'codigo_barras'=>$post['cod_barras'],
                                'dias_vencimento'=>$post['dias_vencimento'],
                                'dias_vencimento_aberto'=>$post['dias_vencimento_aberto'],
                                'peso'=>$post['peso'],
                                'id_unidades_medidas'=>$post['id_unidades_medidas'],
                                'id_materiais_marcas'=>$post['id_materiais_marcas'],
                                'id_materiais_tipos'=>(isset($post['id_materiais_tipos']) ? $post['id_materiais_tipos'] : ''),
                                'id_materiais_categorias'=>$post['id_materiais_categorias'],
                                'id_pessoas_fabricante'=>$post['id_pessoas_fabricante'],
                                'id_modo_conservacao'=>$post['id_modo_conservacao'],
                                'status'=>'A'
                            )
                        );
                    }

                    $status = 200;
                    $retorno = array(
                        'success'=>true, 
                        'type'=>'success', 
                        'msg'=>messagesDefault(!empty($id_materiais) ? 'update' : 'register'),
                        'data'=>$data,
                        'produto'=>$add_produto
                    );
                } else {
                    $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$data);    
                }   
            } catch (Exception $e) {
                $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
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