<?php
$app->post('/menu-permissoes-perfil-save', function() use ($app){
	$status = 400;
	$data = array();
    $retorno = array();
    $erro = '';
    
    if ($app->request->isPost()) {

        try {
            $class_menu = new MenuModel();
            $class_menu_permissoes_perfil = new MenuPermissaoPerfilModel();

            $post = $app->request->post();
            $id_perfil = $app->request->post('id_perfil');
            
            $del_menu_permissoes_perfil = $class_menu_permissoes_perfil->del($id_perfil);
            
            $status = 200;
            $retorno = array(
                'success'=>true, 
                'type'=>'success', 
                'msg'=>messagesDefault('register'),
                'data'=>$data,
                'post'=>$post,
                'del'=>$del_menu_permissoes_perfil
            );

        } catch (Exception $e) {
            $retorno = array('success'=>false, 'type'=>'danger', 'msg'=>$e->getMessage());
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