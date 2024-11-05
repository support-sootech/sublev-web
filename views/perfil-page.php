<?php
require_once('header.php');
$titulo = 'Perfil';
$prefix = 'perfil';
$arr_permissoes = array();
if (isset($_SESSION['usuario']['endpoints'][returnPage()])) {
    $arr_permissoes = $_SESSION['usuario']['endpoints'][returnPage()];
}

?>

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <?php
    require_once('menu.php');
    ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <?php
            require_once('nav.php');
            ?>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Controle de <?=$titulo?> </h1>
                    

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary"><?=$titulo?></h6>
                                </div>
                                <div class="col text-right">
                                    <?php if(in_array('CADASTRAR', $arr_permissoes)):?>
                                        <a href="#" rel="btn-<?=$prefix?>-novo" class="btn btn-primary btn-sm">
                                            <i class="fa fa-plus"></i> Novo
                                        </a>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-<?=$prefix?>" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">ID</th>
                                            <th style="width: 60%;">Descrição</th>
                                            <th style="width: 15%;">Status</th>
                                            <th style="width: 20%;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <?php
        require_once('footer_description.php');
        ?>
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Modal Form -->
    <div class="modal fade" id="modal-<?=$prefix?>" tabindex="-1" role="dialog" aria-labelledby="modal<?=$prefix?>" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal<?=$prefix?>"><?=$titulo?></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-perfil" class="formValidate">
                        <input type="hidden" class="" id="<?=$prefix?>_id_perfil" name="<?=$prefix?>_id_perfil" value="">
                        <div class="form-group">
                            <label for="<?=$prefix?>_descricao">Descrição</label>
                            <input type="text" class="form-control requered" id="<?=$prefix?>_descricao" name="<?=$prefix?>_descricao" placeholder="Descrição">
                        </div>
                        <div class="form-group">
                            <label for="<?=$prefix?>_status">Status</label>
                            <select class="form-select requered" id="<?=$prefix?>_status" name="<?=$prefix?>_status">
                                <option value="A">Ativo</option>
                                <option value="I">Inativo</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fa fa-xmark"></i> Fechar
                    </button>
                    <a class="btn btn-primary" href="#" rel="btn-<?=$prefix?>-salvar">
                        <i class="fa fa-save"></i> Salvar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Menu Permissões -->
    <div class="modal fade" id="modal-<?=$prefix?>-menu-permissao" tabindex="-1" role="dialog" aria-labelledby="modal<?=$prefix?>menupermissao" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal<?=$prefix?>menupermissao">Menu / Permissões</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-<?=$prefix?>-menu-permissao" class="form"></form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fa fa-xmark"></i> Fechar
                    </button>
                    <a class="btn btn-primary" href="#" rel="btn-<?=$prefix?>-menu-permissao-salvar">
                        <i class="fa fa-save"></i> Salvar
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php
require_once('footer.php');
?>
<script>
function carrega_lista(){
    $('#table-<?=$prefix?>').DataTable({
        "ajax": {
            "url": '/<?=$prefix?>-json',
            "type": "post",
            "data":{}
        },
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.10.13/i18n/Portuguese-Brasil.json", "search": "Pesquisar:", },
        "processing": true,
        "destroy": true,
        "order": [],
        "columnDefs": [],
        "columns":
                [
                    { "data": function ( data, type, row ) {
                                    return data.id_perfil;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.descricao;
                                }
                    },
                    { "data": function ( data, type, row ) {

                                    var status_desc 	= '';
                                    var status_label 	= '';

                                    if(data.status=='I'){
                                        status_desc 	= 'Inativo';
                                        status_label 	= 'danger';
                                    }else{
                                        status_desc 	= 'Ativo';
                                        status_label 	= 'success';
                                    }

                                    var campo = '<span class="badge text-bg-'+status_label+'" title="'+status_desc+'">'+status_desc+'</span>';

                                    return campo;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    var campo = '';

                                    <?php if(in_array('CADASTRAR', $arr_permissoes)):?>
                                    <?php endif;?>
                                        campo+= '<a href="#" title="Menus / Permissões " id="'+data.id_perfil+'" rel="btn-<?=$prefix?>-menu-permissao" role="button" class="btn btn-dark btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-list"></i>'+
                                                '</a>';

                                    <?php if(in_array('ALTERAR', $arr_permissoes)):?>
                                        campo+= '<a href="#" title="Editar" id="'+data.id_perfil+'" rel="btn-<?=$prefix?>-editar" role="button" class="btn btn-primary btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-edit"></i>'+
                                                '</a>';
                                    <?php endif;?>
                                    
                                    <?php if(in_array('DELETAR', $arr_permissoes)):?>
                                        campo+= '<a href="#" title="Deletar" rel="btn-<?=$prefix?>-deletar" id="'+data.id_perfil+'" role="button" class="btn btn-danger btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-trash"></i>'+
                                                '</a>';
                                    <?php endif;?>

                                    return campo;
                                }
                    }
                ]

    });
}

function deletaRegistro(id){
    $.ajax({
        url:'/<?=$prefix?>-del/'+id,
        type:'get',
        dataType:'json',
        data:{},
        success:function(data){
            if (data) {
                gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                carrega_lista();
            }
        },
        beforeSend:function(){
            preloaderStart();
        },
        error:function(a,b,c){
            preloaderStop();
            gerarAlerta(a, 'Aviso', 'danger');
            console.error('a',a);
            console.error('b',b);
            console.error('c',c);
        },
        complete:function(){
            preloaderStop();
        }
    });
}

function menu_permissao(id_perfil) {
    $.ajax({
        url:'/<?=$prefix?>-menu-permissao',
        type:'post',
        dataType:'json',
        data:{
            'id_perfil':id_perfil
        },
        success:function(data){
            
            if (data.success) {
                let el = $('form[name=form-<?=$prefix?>-menu-permissao]');
                let body = '<small>*Em vermelho são itens inativos.</small><br>';
                body += '<input type="hidden" name="id_perfil_menu" value="'+id_perfil+'" />';

                if (data.data) {
                    $.each(data.data, function(i, v){
                        
                        let menu_principal_selected = (data.menu_permissao_perfil[v.id_menu] ? 'checked="checked"' : '');

                        body += '<div class="row">';                                
                            body += '<div class="col">';
                                body += '<div class="form-check">'+
                                            '<input class="form-check-input" type="checkbox" '+menu_principal_selected+' value="'+v.id_menu+'" id="menu_principal" name="menu_principal[]">'+
                                            '<label class="form-check-label '+(v.status=='I' ? 'text-danger' : 'text-black')+'" for="menu_principal"><h6 style="font-weight: bold;">'+v.nome+'<small> (Principal)</small></h6></label>'+
                                        '</div>';

                                if (v.menu_sub) {
                                    body += '<table class="table table-bordered">';
                                    $.each(v.menu_sub, function(a, b){

                                        let sub_menu_selected = (data.menu_permissao_perfil[b.id_menu] ? 'checked="checked"' : '');

                                        body += '<tr>';
                                            body += '<td>';
                                                body += '<div class="form-check">'+
                                                            '<input class="form-check-input" '+sub_menu_selected+' type="checkbox" value="'+b.id_menu+'" data-menu="'+v.id_menu+'" id="sub_menu" name="sub_menu[]">'+
                                                            '<label class="form-check-label '+(b.status=='I' ? 'text-danger' : '')+'" for="sub_menu"><span style="font-size:11px">'+b.nome+' <small>(Sub-Menu)</small></span></label>'+
                                                        '</div>';
                                            body += '</td>';

                                            if (data.permissoes) {
                                                $.each(data.permissoes, function(x,y) {

                                                    let permissao_selected = '';
                                                    if(data.menu_permissao_perfil[b.id_menu]) {
                                                        permissao_selected = data.menu_permissao_perfil[b.id_menu].some((i)=>i.id_permissao===y.id_permissoes) ? 'checked="checked"' : '';
                                                    }
                                                    

                                                    body += '<td>';
                                                        body += '<div class="form-check form-check-inline">'+
                                                                    '<input class="form-check-input" '+permissao_selected+' type="checkbox" data-menu="'+v.id_menu+'" data-submenu="'+b.id_menu+'" id="permissoes" name="permissoes[]" value="'+y.id_permissoes+'">'+
                                                                    '<label class="form-check-label '+(y.status=='I' ? 'text-danger' : '')+'" title="'+(y.status=='I' ? 'Permissão Inativa' : '')+'" for="permissoes" style="font-size:11px">'+y.descricao+'</label>'+
                                                                '</div>';
                                                    body += '</td>';
                                                })
                                            }

                                        body += '</tr>';
                                    });
                                    body += '</table>';
                                }

                            body += '</div>';
                        body += '</div>';
                    });
                }
                el.html(body);
            }
        },
        beforeSend:function(){
            preloaderStart();
        },
        error:function(a,b,c){
            preloaderStop();
            gerarAlerta(a, 'Aviso', 'danger');
            console.error('a',a);
            console.error('b',b);
            console.error('c',c);
        },
        complete:function(){
            preloaderStop();
        }
    });
}

$(document).ready(function(){

    formFieldsRequered();

    carrega_lista();

    $(document).on('click', 'a[rel=btn-<?=$prefix?>-novo]', function(e){
        e.preventDefault();
        $('div#modal-<?=$prefix?>').modal('show');
    });

    $('div#modal-<?=$prefix?>').on('hidden.bs.modal', function (e) {
        $('form[name=form-<?=$prefix?>]').find('input, select').each(function(){
            $(this).val('');
        });
    });

    $(document).on('click','a[rel=btn-<?=$prefix?>-editar]', function(e){
        e.preventDefault();
        const id = $(this).attr('id');
        if (id) {            
            $.ajax({
                url:'/<?=$prefix?>-edit/'+id,
                type:'get',
                dataType:'json',
                data:{},
                success:function(data){
                    if (data.data) {
                        $.each(data.data, function(i,v){
                            console.log(i, v);
                            $('form[name=form-<?=$prefix?>] #<?=$prefix?>_'+i+'').val(v);
                        });
                    }
                },
                beforeSend:function(){
                    preloaderStart();
                },
                error:function(a,b,c){
                    preloaderStop();
                    gerarAlerta(a, 'Aviso', 'danger');
                    console.error('a',a);
                    console.error('b',b);
                    console.error('c',c);
                },
                complete:function(){
                    preloaderStop();
                }
            });
            $('div#modal-<?=$prefix?>').modal('show');
        }
    });    

    $(document).on('click','a[rel=btn-<?=$prefix?>-deletar]', async function(e) {
        e.preventDefault();
        const id = $(this).attr('id');

        if (id) {
            Swal.fire({
                text: 'Você realmente deseja excluír esse registro?',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sim',
                cancelButtonText: 'Não',
                showCancelButton: true,
                icon: "question",
                background: "#fff",
            }).then((result) => {
                if (result.isConfirmed) {
                    deletaRegistro(id);
                }
            });
        }
        
    });

    $(document).on('click','a[rel=btn-<?=$prefix?>-salvar]', function(e){
		e.preventDefault();
        
        if(!isFormValidate($('form[name=form-<?=$prefix?>]'))) {
            gerarAlerta('<?=messagesDefault('fields_requered')?>', 'Aviso', 'danger');
            return false;
        }

        $('form[name=form-<?=$prefix?>]').ajaxForm({
			data:{},
    		success : function(data) {
                console.log('data', data);
                gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                if (data.success) {
                    $('div#modal-perfil').modal('hide');
                    carrega_lista();
				}
			},
			error : function(e) {
                preloaderStop();
				gerarAlerta(e.responseJSON.msg, 'Erro', 'danger');
			},
            complete:function(){
                preloaderStop();
            },
            beforeSend:function(){
                preloaderStart();
            },
			type:'post',
			dataType:'json',
			url: '/<?=$prefix?>-save',
			resetForm:false
		}).submit();
	});

    $(document).on('click', 'a[rel=btn-<?=$prefix?>-menu-permissao]', function(e){
        e.preventDefault();
        $('div#modal-<?=$prefix?>-menu-permissao').modal('show');
        menu_permissao($(this).attr('id'));
    });
    
    $('div#modal-<?=$prefix?>-menu-permissao').on('hidden.bs.modal', function (e) {
        $('input[name=id_perfil_menu]').val('');    
    });

    $(document).on('click', 'input[name^=menu_principal]', function(){
        const t = $(this);
        if(t.not(':checked')) {
            $('input[name^=sub_menu][data-menu='+t.val()+']').prop('checked', false);
        }
    });

    $(document).on('click', 'input[name^=sub_menu]', function(){
        const t = $(this);
        const menu_principal = $('input[name^=menu_principal][value='+t.attr('data-menu')+']');
        const permissoes = $('input[name^=permissoes][data-submenu='+t.val()+']');

        if(t.is(':checked')) {
            permissoes.prop('checked', true);
            if(!menu_principal.is(':checked')) {
                menu_principal.prop('checked', true);
            }

        } else {
            permissoes.prop('checked', false);
            const qtd_submenu = $('input[name^=sub_menu][data-menu='+t.attr('data-menu')+']:checked').length;
            if (qtd_submenu<1) {
                menu_principal.prop('checked', false);
            }
        }
        
    });

    $(document).on('click', 'input[name^=permissoes]', function(){
        const t = $(this);
        if(!t.is(':checked')) {
            const c = $('input[name^=permissoes][data-submenu='+t.attr('data-submenu')+']:checked').length;
            if(c==0) 
                $('input[name^=sub_menu][value='+t.attr('data-submenu')+']').prop('checked', false);
        } else {
            const sub_menu = $('input[name^=sub_menu][value='+t.attr('data-submenu')+']');
            if (!sub_menu.is(':checked')) {
                sub_menu.prop('checked', true);
            }
        }
    });

    $(document).on('click', 'a[rel=btn-perfil-menu-permissao-salvar]', function(e){
        e.preventDefault();
        const id_perfil = $('input[name=id_perfil_menu]').val();
        
        let arr_menu_principal = [];
        let arr_sub_menu = [];
        
        
        $('form[name=form-perfil-menu-permissao]').find('input[name^=menu_principal]').each(function(){
            let obj_menu_principal = {};
            if ($(this).is(':checked')) {
                obj_menu_principal.id_menu = $(this).val();
                obj_menu_principal.id_perfil = id_perfil;
                obj_menu_principal.id_permissoes = 1;
                arr_menu_principal.push(obj_menu_principal);
            }
        });
        
        $('form[name=form-perfil-menu-permissao]').find('input[name^=permissoes]').each(function(){
            let obj_sub_menu = {};
            if ($(this).is(':checked')) {
                obj_sub_menu.id_menu = $(this).attr('data-submenu');
                obj_sub_menu.id_permissoes = $(this).val();
                obj_sub_menu.id_perfil = id_perfil;
                arr_sub_menu.push(obj_sub_menu);
            }
        });

        $.ajax({
            url:'/menu-permissoes-<?=$prefix?>-save',
            type:'post',
            dataType:'json',
            data:{
                'arr_menu_principal':arr_menu_principal,
                'arr_sub_menu':arr_sub_menu,
                'id_perfil':id_perfil
            },
            success:function(data){
                gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                $('div#modal-<?=$prefix?>-menu-permissao').modal('hide');
            },
            beforeSend:function(){
                preloaderStart();
            },
            error:function(a,b,c){
                preloaderStop();
                gerarAlerta(a, 'Aviso', 'danger');
                console.error('a',a);
                console.error('b',b);
                console.error('c',c);
            },
            complete:function(){
                preloaderStop();
            }
        });
        

    });

});
</script>