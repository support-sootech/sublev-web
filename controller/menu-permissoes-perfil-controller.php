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
            $arr_menu_principal = $app->request->post('arr_menu_principal');
            $arr_sub_menu = $app->request->post('arr_sub_menu');
            
            $del_menu_permissoes_perfil = $class_menu_permissoes_perfil->del($id_perfil);

            if (isset($arr_menu_principal) && count($arr_menu_principal) > 0) {
                foreach ($arr_menu_principal as $key => $value) {
                    $add_menu_principal = $class_menu_permissoes_perfil->add($value);
                }
            }

            if (isset($arr_sub_menu) && count($arr_sub_menu) > 0) {
                foreach ($arr_sub_menu as $key => $value) {
                    $add_menu_sub = $class_menu_permissoes_perfil->add($value);
                }
            }
            
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