<?php
require_once('header.php');
$titulo = 'Menu';
$prefix = 'menu';
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
                                    <h6 class="m-0 font-weight-bold text-primary">Menus</h6>
                                </div>
                                <div class="col text-right">
                                    <a href="#" rel="btn-<?=$prefix?>-novo" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Novo
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-<?=$prefix?>" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Icone</th>
                                            <th>Nome</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                            <th>Ações</th>
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

    <!-- Logout Modal-->
    <div class="modal fade" id="modal-<?=$prefix?>" tabindex="-1" role="dialog" aria-labelledby="<?=$prefix?>ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="<?=$prefix?>ModalLabel">Controle de <?=$titulo?></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-<?=$prefix?>" class="formValidate">
                    <input type="hidden" class="form-control" id="<?=$prefix?>_id_menu" name="<?=$prefix?>_id_menu" placeholder="">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_nome">Nome</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_nome" name="<?=$prefix?>_nome" placeholder="Nome do menu">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_link">Link</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_link" name="<?=$prefix?>_link" placeholder="Rota da página">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_icone">Ícone</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_icone" name="<?=$prefix?>_icone" placeholder="fas fa-fw fa-cog">
                                    <div class="form-text" id="basic-addon4">Exemplo <a href="https://fontawesome.com/v5/search?o=r&m=free" target="_blank"><i class="fas fa-external-link-alt"></i></a>.</div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_tipo">Tipo</label>
                                    <select class="form-select requered" id="<?=$prefix?>_tipo" name="<?=$prefix?>_tipo">
                                        <option value="">--Selecione--</option>
                                        <option value="P">Principal</option>
                                        <option value="S">Sub-Menu</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_ordem">Ordem</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_ordem" name="<?=$prefix?>_ordem" placeholder="11">
                                    <div class="form-text" id="basic-addon4">Ordenação para apresentação.</div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_status">Status</label>
                                    <select class="form-select requered" id="<?=$prefix?>_status" name="<?=$prefix?>_status">
                                        <option value="">--Selecione--</option>
                                        <option value="A">Ativo</option>
                                        <option value="I">Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-4 hide" id="div-id-menu-principal" style="display:none">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_id_menu_principal">Menus principais</label>
                                    <select class="form-select" id="<?=$prefix?>_id_menu_principal" name="<?=$prefix?>_id_menu_principal"></select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-4 hide" id="div-id-menu-descricao" style="display:none">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_descricao">Sub-título</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_descricao" name="<?=$prefix?>_descricao" placeholder="Cadastros...">
                                    <div class="form-text" id="basic-addon4">Breve informação do menu.</div>
                                </div>
                            </div>
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

<?php
require_once('footer.php');
?>
<script>
function carrega_lista_menu(){
    
    $('#table-<?=$prefix?>').DataTable({
        "ajax": {
            "url": '/<?=$prefix?>-json',
            "type": "get",
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
                                    return data.id_menu;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    let campo = '<i class="'+data.icone+'"></i>';
                                    return campo;
                                }
                    },
                    { "data": function ( data, type, row ) {

                                    let campo = data.nome;
                                    if(data.tipo=='S' && data.nm_menu_principal!=''){
                                        campo+= '<br><small>Menu principal: '+data.nm_menu_principal+'<small>';
                                    }
                                    return campo;
                                }
                    },
                    { "data": function ( data, type, row ) {

                                    var desc = '';
                                    var label = '';

                                    if(data.tipo=='P'){
                                        desc = 'Principal';
                                        label = 'primary';
                                    }else{
                                        desc = 'Sub-Menu';
                                        label = 'warning';
                                    }

                                    var campo = '<span class="badge text-bg-'+label+'" title="'+desc+'">'+desc+'</span>';

                                    return campo;
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

                                    campo+= '<a href="#" title="Editar" id="'+data.id_menu+'" rel="btn-<?=$prefix?>-editar" role="button" class="btn btn-primary btn-sm" style="margin: 1px 2px">'+
                                                '<i class="fas fa-edit"></i>'+
                                            '</a>';

                                    campo+= '<a href="#" title="Deletar" rel="btn-<?=$prefix?>-deletar" id="'+data.id_menu+'" role="button" class="btn btn-danger btn-sm" style="margin: 1px 2px">'+
                                                '<i class="fas fa-trash"></i>'+
                                            '</a>';

                                    return campo;
                                }
                    }
                ]

    });
}

function carrega_combo_menu_principal(id_menu_principal='') {
    let elemento = $('select[name=<?=$prefix?>_id_menu_principal]');

    $.ajax({
        url:'/menu-principal-json',
        type:'get',
        data:{},
        dataType:'json',
        success:function(data){
            if (data.data.length > 0) {
                let opt = '<option value="">--Selecione--</option>';
                $.each(data.data, function(i, v){
                    let selected = '';
                    if (id_menu_principal != '' && id_menu_principal == v.id_menu) {
                        selected = 'selected="selected"';
                    }
                    opt+= '<option value="'+v.id_menu+'" '+selected+' >'+v.nome+'</option>';
                });
                elemento.html(opt).addClass('requered');
            }
        },
        beforeSend:function(){
            elemento.html('<option value="">Carregando...</option>');
        },
        error:function(a,b,c){
            gerarAlerta('Erro Combo Menu Principal: '+a, 'Aviso', 'danger');
            console.error('a',a);
            console.error('b',b);
            console.error('c',c);
        },
        complete:function(){
            preloaderStop();
        }
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
                carrega_lista_menu();
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

function tipo_menu(tipo='P', id_menu_principal=''){
    $('select[name=<?=$prefix?>_id_menu_principal]').val('');
    $('input[name=<?=$prefix?>_descricao]').val('');

    if(tipo!='') {
        if (tipo=='P') {
            $('div[id=div-id-menu-principal]').hide();
            $('div[id=div-id-menu-descricao]').show();
            $('input[name=<?=$prefix?>_link]').val('').removeClass('requered').prop('readonly', true);
            $('select[name=<?=$prefix?>_id_menu_principal]').removeClass('requered');
        } else {
            $('div[id=div-id-menu-principal]').show();
            $('div[id=div-id-menu-descricao]').show();
            $('input[name=<?=$prefix?>_link]').val('').addClass('requered').prop('readonly', false);
            $('select[name=<?=$prefix?>_id_menu_principal]').addClass('requered');
            carrega_combo_menu_principal(id_menu_principal);
            
        }
    } else {
        $('div[id=div-id-menu-principal]').hide();
        $('div[id=div-id-menu-descricao]').hide();
        $('input[name=<?=$prefix?>_link]').val('').addClass('requered').prop('readonly', false);
        $('select[name=<?=$prefix?>_id_menu_principal]').removeClass('requered');
    }

    formFieldsRequered();

}

$(document).ready(function(){

    formFieldsRequered();
    carrega_lista_menu();
    
    $(document).on('click', 'a[rel=btn-<?=$prefix?>-novo]', function(e){
        e.preventDefault();
        $('div#modal-<?=$prefix?>').modal('show');
    });

    $('div#modal-<?=$prefix?>').on('hidden.bs.modal', function (e) {
        $('form[name=form-<?=$prefix?>]').find('input, select').each(function(){
            $(this).val('');
        });
        tipo_menu('')
    });

    $(document).on('change', 'select[name=<?=$prefix?>_tipo]', function(e){
        e.preventDefault();
        const tipo = $(this).val();
        if (tipo != '') {            
            tipo_menu(tipo);
        }
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
                    if (data) {

                        tipo_menu(data.tipo, data.id_menu_principal);

                        $.each(data, function(i,v){
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
                gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                if (data.success) {
                    $('div#modal-<?=$prefix?>').modal('hide');
                    carrega_lista_menu();
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

});
</script>