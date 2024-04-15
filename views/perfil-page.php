<?php
require_once('header.php');
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
                    <h1 class="h3 mb-2 text-gray-800">Controle de Perfil </h1>
                    

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary">Perfil</h6>
                                </div>
                                <div class="col text-right">
                                    <a href="#" rel="btn-perfil-novo" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Novo
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-perfil" width="100%" cellspacing="0">
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
    <div class="modal fade" id="modal-perfil" tabindex="-1" role="dialog" aria-labelledby="modalPerfil" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPerfil">Perfil</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-perfil">
                        <input type="hidden" class="" id="id_perfil" name="id_perfil" value="">
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <input type="text" class="form-control" id="descricao" name="descricao" placeholder="Descrição">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
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
                    <a class="btn btn-primary" href="#">
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
    console.log('data');
    $('#table-perfil').DataTable({
        "ajax": {
            "url": '/perfil-json',
            "type": "post",
            "data":{}
        },
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.10.13/i18n/Portuguese-Brasil.json" },
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

                                    campo+= '<a href="#" title="Editar" id="'+data.id_perfil+'" rel="btn-perfil-editar" role="button" class="btn btn-primary btn-sm" style="margin: 1px 2px">'+
                                                '<i class="fas fa-edit"></i>'+
                                            '</a>';

                                    campo+= '<a href="#" title="Deletar" rel="btn-perfil-deletar" id="'+data.id_perfil+'" role="button" class="btn btn-danger btn-sm" style="margin: 1px 2px">'+
                                                '<i class="fas fa-trash"></i>'+
                                            '</a>';

                                    return campo;
                                }
                    }
                ]

    });
}

function deletaRegistro(id){
    $.ajax({
        url:'/controle-tipo-pessoas-delete/'+id,
        type:'get',
        dataType:'json',
        data:{},
        success:function(data){
            console.log('data', data);
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
    carrega_lista();

    $(document).on('click', 'a[rel=btn-marcas-novo]', function(e){
        e.preventDefault();
        $('div#modal-perfil').modal('show');
    });

    $('div#modal-perfil').on('hidden.bs.modal', function (e) {
        $('form[name=form-perfil]').find('input, select').each(function(){
            $(this).val('');
        });
    });

    $(document).on('click','a[rel=btn-perfil-editar]', function(e){
        e.preventDefault();
        const id = $(this).attr('id');
        if (id) {            
            $.ajax({
                url:'/perfil-edit/'+id,
                type:'get',
                dataType:'json',
                data:{},
                success:function(data){
                    console.log('data', data);
                    if (data.data) {
                        $.each(data.data, function(i,v){
                            console.log(i, v);
                            $('form[name=form-perfil] #'+i+'').val(v);
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
            $('div#modal-perfil').modal('show');
        }
    });    

    $(document).on('click','a[rel=btn-tipos-pessoas-deletar]', async function(e) {
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

    $(document).on('click','a[rel=btn-salvar]', function(e){
		e.preventDefault();

        if($('input[name=descricao]').val() == ''){
            gerarAlerta('É necessário informar o campo descrição');
            return false;
        }

        if($('select[name=status]').val() == ''){
            gerarAlerta('É necessário selecionar um status.', 'Aviso', 'danger');
            return false;
        }

        $('form[name=form-tipo-pessoas]').ajaxForm({
			data:{},
    		success : function(data) {
				gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                if (data.success) {
                    $('div#modal-form-tipo-pessoas').modal('hide');
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
			url: '/controle-tipo-pessoas-salvar',
			resetForm:false
		}).submit();
	});

});
</script>