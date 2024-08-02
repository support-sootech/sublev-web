<?php
require_once('header.php');
$titulo = 'Tipos de embalagens';
$prefix = 'embalagens_tipos';
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
                                        <a href="#" rel="btn-<?=str_replace('_','-',$prefix)?>-novo" class="btn btn-primary btn-sm">
                                            <i class="fa fa-plus"></i> Novo
                                        </a>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-<?=str_replace('_','-',$prefix)?>" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">ID</th>
                                            <th style="width: 65%;">Descrição</th>
                                            <th style="width: 20%;">Status</th>
                                            <th style="width: 10%;">Ações</th>
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
    <div class="modal fade" id="modal-<?=str_replace('_','-',$prefix)?>" tabindex="-1" role="dialog" aria-labelledby="modal<?=$prefix?>" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal<?=$prefix?>"><?=$titulo?></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-<?=str_replace('_','-',$prefix)?>" class="formValidate">
                        <input type="hidden" class="" id="<?=$prefix?>_id_embalagens_tipos" name="<?=$prefix?>_id_embalagens_tipos" value="">
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
                    <a class="btn btn-primary" href="#" rel="btn-<?=str_replace('_','-',$prefix)?>-salvar">
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
    $('#table-<?=str_replace('_','-',$prefix)?>').DataTable({
        "ajax": {
            "url": '/<?=str_replace('_','-',$prefix)?>-json',
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
                                    return data.id_embalagens_tipos;
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

                                    <?php if(in_array('ALTERAR', $arr_permissoes)):?>
                                        campo+= '<a href="#" title="Editar" id="'+data.id_embalagens_tipos+'" rel="btn-<?=str_replace('_','-',$prefix)?>-editar" role="button" class="btn btn-primary btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-edit"></i>'+
                                                '</a>';
                                    <?php endif;?>

                                    <?php if(in_array('DELETAR', $arr_permissoes)):?>
                                        campo+= '<a href="#" title="Deletar" rel="btn-<?=str_replace('_','-',$prefix)?>-deletar" id="'+data.id_embalagens_tipos+'" role="button" class="btn btn-danger btn-sm" style="margin: 1px 2px">'+
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
        url:'/<?=str_replace('_','-',$prefix)?>-del/'+id,
        type:'get',
        dataType:'json',
        data:{},
        success:function(data){
            gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
            if (data) {
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

$(document).ready(function(){

    formFieldsRequered();

    carrega_lista();

    $(document).on('click', 'a[rel=btn-<?=str_replace('_','-',$prefix)?>-novo]', function(e){
        e.preventDefault();
        $('div#modal-<?=str_replace('_','-',$prefix)?>').modal('show');
    });

    $('div#modal-<?=str_replace('_','-',$prefix)?>').on('hidden.bs.modal', function (e) {
        $('form[name=form-<?=str_replace('_','-',$prefix)?>]').find('input, select').each(function(){
            $(this).val('');
        });
    });

    $(document).on('click','a[rel=btn-<?=str_replace('_','-',$prefix)?>-editar]', function(e){
        e.preventDefault();
        const id = $(this).attr('id');
        if (id) {            
            $.ajax({
                url:'/<?=str_replace('_','-',$prefix)?>-edit/'+id,
                type:'get',
                dataType:'json',
                data:{},
                success:function(data){
                    if (data.data) {
                        $.each(data.data, function(i,v){
                            console.log(i, v);
                            $('form[name=form-<?=str_replace('_','-',$prefix)?>] #<?=$prefix?>_'+i+'').val(v);
                        });
                        $('div#modal-<?=str_replace('_','-',$prefix)?>').modal('show');
                    } else {
                        gerarAlerta(data.msg, 'Aviso', data.type);
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
    });    

    $(document).on('click','a[rel=btn-<?=str_replace('_','-',$prefix)?>-deletar]', async function(e) {
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

    $(document).on('click','a[rel=btn-<?=str_replace('_','-',$prefix)?>-salvar]', function(e){
		e.preventDefault();
        
        if(!isFormValidate($('form[name=form-<?=str_replace('_','-',$prefix)?>]'))) {
            gerarAlerta('<?=messagesDefault('fields_requered')?>', 'Aviso', 'danger');
            return false;
        }

        $('form[name=form-<?=str_replace('_','-',$prefix)?>]').ajaxForm({
			data:{},
    		success : function(data) {
                console.log('data', data);
                gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                if (data.success) {
                    $('div#modal-<?=str_replace('_','-',$prefix)?>').modal('hide');
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
			url: '/<?=str_replace('_','-',$prefix)?>-save',
			resetForm:false
		}).submit();
	});

});
</script>