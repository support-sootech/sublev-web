<?php
require_once('header.php');
$titulo = 'Baixa de Materiais Fracionados';
$prefix = 'material_fracionado';
$arr_permissoes = array();
if (isset($_SESSION['usuario']['endpoints'][returnPage()])) {
    $arr_permissoes = $_SESSION['usuario']['endpoints'][returnPage()];
}

$arr_perfil = array_column($_SESSION['usuario']['perfil'], 'ds_perfil');

?>
<style>
    table tbody {
        font-size: 13px;
    }

    table thead {
        font-size: 14px;
    }
</style>
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
                    <h1 class="h3 mb-2 text-gray-800"><?=$titulo?> </h1>

                    <div class="row">
                        <div class="col">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
                                        </div>
                                        <div class="col text-right"></div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form name="form-filtros" method="post" action="#">
                                        <div class="row">
                                            
                                            <?php if(in_array('ADMINISTRADOR', $arr_perfil) || in_array('ROOT', $arr_perfil)): ?>
                                                <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                                    <div class="form-group">
                                                        <label for="fil_setor">Setores</label>
                                                        <select name="fil_setor" id="fil_setor" class="form-select"></select>
                                                    </div>
                                                </div>
                                            <?php endif;?>

                                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                                <div class="form-group">
                                                    <label for="fil_usuarios">Usuários</label>
                                                    <select name="fil_usuarios" id="fil_usuarios" class="form-select"></select>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                                <div class="form-group">
                                                    <label for="fil_status">Status</label>
                                                    <select name="fil_status" id="fil_status" class="form-select">
                                                        <option value="">--Selecione--</option>
                                                        <option value="A">Ativo</option>
                                                        <option value="D">Descartado</option>
                                                        <option value="V">Utilizado</option>
                                                        <option value="C">Vencido</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3" style="padding-top: 30px;">
                                                <a href="#" rel="btn-form-filtro" class="btn btn-secondary" role="button"><i class="fas fa-search"></i>Filtrar</a>
                                                <a href="#" rel="btn-form-pdf" class="btn btn-secondary" role="button"><i class="fas fa-file-pdf"></i> Exportar PDF</a>
                                            </div>

                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="m-0 font-weight-bold text-primary"><?=$titulo?></h6>
                                        </div>
                                        <div class="col text-right"></div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="table-<?=str_replace('_','-',$prefix)?>" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Etiqueta</th>
                                                    <th>Fracionado</th>
                                                    <th >Material</th>
                                                    <th>Setor</th>
                                                    <th >Peso</th>
                                                    <th >Validade</th>
                                                    <th >Status</th>
                                                    <th >Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
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
    <div class="modal fade" id="modal-materiais-fracionados-descarte" tabindex="-1" role="dialog" aria-labelledby="modal<?=$prefix?>" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal<?=$prefix?>">Descarte</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-materiais-francionados-descarte" class="formValidate">
                        <input type="hidden" class="" id="id_materiais_fracionados" name="id_materiais_fracionados" value="">
                        <input type="hidden" class="" id="status" name="status" value="">

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="motivo_descarte">Motivo do descarte</label>
                                    <textarea name="motivo_descarte" id="motivo_descarte" rows="3" class="form-control requered"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fa fa-xmark"></i> Fechar
                    </button>
                    <a class="btn btn-primary" href="#" rel="btn-materiais-fracionados-descarte-salvar">
                        <i class="fa fa-save"></i> Salvar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal historico-->
    <div class="modal fade" id="modal-materiais-fracionados-historico" tabindex="-1" role="dialog" aria-labelledby="modal<?=$prefix?>historico" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal<?=$prefix?>historico">Log</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <table class="table table-bordered" id="table-materiais-fracionados-log" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Log</th>
                                        <th>Ação</th>
                                        <th>Material</th>                                        
                                        <th>Qtd.</th>
                                        <th>Vencimento</th>
                                        <th>Fracionamento</th>
                                        <th>Status</th>
                                        <th>Motivo</th>
                                        <th>Usuário</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fa fa-xmark"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form name="form-materiais-fracionados-pdf" action="/materiais-fracionados-json" target="_blank" method="post"></form>

<?php
require_once('footer.php');
?>
<script>
function carrega_lista(){

    const el_setor = $('select[name=fil_setor]');
    let id_setor;
    if (el_setor.is(':visible')) {
        id_setor = el_setor.val();
    } else {
        id_setor = '<?=$_SESSION['usuario']['id_setor']?>';
    }
    
    const el_usuarios = $('select[name=fil_usuarios]');
    let id_usuarios;
    if (el_usuarios.is(':visible')) {
        id_usuarios = el_usuarios.val();
    } else {
        id_usuarios = '<?=$_SESSION['usuario']['id_usuarios']?>';
    }
    

    const status = $('select[name=fil_status]').val();

    $('#table-<?=str_replace('_','-',$prefix)?>').DataTable({
        "ajax": {
            "url": '/materiais-fracionados-json',
            "type": "post",
            "data":{
                id_setor:id_setor,
                id_usuarios:id_usuarios,
                status:status
            }
        },
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.10.13/i18n/Portuguese-Brasil.json", "search": "Pesquisar:", },
        "processing": true,
        "destroy": true,
        "order": [],
        "columnDefs": [],
        "columns":
                [
                    { "data": function ( data, type, row ) {
                                    return data.id_etiqueta;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.dt_fracionamento+'<br><small>'+data.nm_usuario+'</small>';
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.ds_materiais;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.nm_setor;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.qtd_fracionada_formatado;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.dt_vencimento;
                                }
                    },
                    { "data": function ( data, type, row ) {

                                    var status_desc 	= '';
                                    var status_label 	= '';

                                    if (data.status=='V') {
                                        status_desc 	= 'Utilizado';
                                        status_label 	= 'warning';
                                    } else if(data.status=='C') {
                                        status_desc 	= 'Vencido';
                                        status_label 	= 'danger';
                                    } else if(data.status=='D') {
                                        status_desc 	= 'Descartado';
                                        status_label 	= 'secondary';
                                    } else {
                                        status_desc 	= 'Ativo';
                                        status_label 	= 'success';
                                    }

                                    var campo = '<span class="badge text-bg-'+status_label+'" title="'+status_desc+'">'+status_desc+'</span>';

                                    return campo;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    var campo = '';

                                    //A-tivo, (V)endido, Ven(C)ido, (D)escartado
                                    if (data.status!='A') {
                                        campo+= '<a href="#" title="Ativar o material" id="'+data.id_materiais_fracionados+'" data-status="A" rel="btn-material-status" role="button" class="btn btn-primary btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fa fa-undo"></i>'+
                                                '</a>';
                                    }

                                    if (data.status!='V' && data.status!='C' && data.status!='D') {                                        
                                        campo+= '<a href="#" title="Material utilizado" id="'+data.id_materiais_fracionados+'" data-status="V" rel="btn-material-status" role="button" class="btn btn-success btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-check"></i>'+
                                                '</a>';
                                    }

                                    if (data.status!='C' && data.status!='V') {
                                        campo+= '<a href="#" title="Material vencido" id="'+data.id_materiais_fracionados+'" data-status="C" rel="btn-material-status" role="button" class="btn btn-danger btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fa fa-calendar"></i>'+
                                                '</a>';
                                    }

                                    if (data.status!='D' && data.status!='V') {
                                        campo+= '<a href="#" title="Material descartado" id="'+data.id_materiais_fracionados+'" data-status="D" rel="btn-material-status" role="button" class="btn btn-dark btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-trash"></i>'+
                                                '</a>';
                                    }

                                    campo+= '<a href="#" title="Histórico" id="'+data.id_materiais_fracionados+'" rel="btn-material-historico" role="button" class="btn btn-secondary btn-sm" style="margin: 1px 2px">'+
                                                '<i class="fas fa-list-ul"></i>'+
                                            '</a>';

                                    return campo;
                                }
                    }
                ]

    });
}

function carrega_lista_log(id_materiais_fracionados){

    $('#table-materiais-fracionados-log').DataTable({
        "ajax": {
            "url": '/materiais-fracionados-log-json',
            "type": "post",
            "data":{id_materiais_fracionados:id_materiais_fracionados}
        },
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.10.13/i18n/Portuguese-Brasil.json", "search": "Pesquisar:", },
        "processing": true,
        "destroy": true,
        "order": [],
        "columnDefs": [],
        "columns":
                [
                    { "data": function ( data, type, row ) {
                                    return data.id_log;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.dt_log;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.acao;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.ds_materiais;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.qtd_fracionada_formatado+' '+data.ds_unidade_medida;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.dt_vencimento;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.dt_fracionamento;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    var campo = '<span class="badge text-bg-'+data.label_status+'" title="'+data.ds_status+'">'+data.ds_status+'</span>';
                                    return campo;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.motivo_descarte;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.nm_usuario;
                                }
                    }
                ]

    });
}

function deletaRegistro(id){
    $.ajax({
        url:'/materiais-del/'+id,
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


//COMBO DE SETORES
function comboSetores(){
    const el = $('select[name=fil_setor]');
    if (el.is(':visible')) {
        let opt = '';
        $.ajax({
            url:'/setor-json',
            type:'post',
            dataType:'json',
            data:{status:'A'},
            success:function(data) {
                console.log('data', data);
                if (data.data.length > 0) {
                    opt = '<option value="">--Selecione--</option>';
                    $.each(data.data, function(i,v){
                        opt+= '<option value="'+v.id_setor+'">'+v.nome+'</option>'
                    });                
                }
                el.html(opt);
            },
            beforeSend:function(){
                opt = '<option>Carregando...</option>';
            },
            complete:function(){},
            error:function(a,b,c){
                console.log('a',a);
                console.log('b',b);
                console.log('c',c);
            }
        });
    }
}

//COMBO DE USUARIOS
function comboUsuarios(){
    const el = $('select[name=fil_usuarios]');

    if (el.is(':visible')) {
        const el_setor = $('select[name=fil_setor]');
        let id_setor;
        if (el_setor.is(':visible')) {
            id_setor = el_setor.val();
        } else {
            id_setor = '<?=$_SESSION['usuario']['id_setor']?>';
        }
        let opt = '';
        $.ajax({
            url:'/usuarios-json',
            type:'post',
            dataType:'json',
            data:{status:'A', 'id_setor':id_setor},
            success:function(data) {
                console.log('data', data);
                if (data.data.length > 0) {
                    opt = '<option value="">--Selecione--</option>';
                    $.each(data.data, function(i,v){
                        opt+= '<option value="'+v.id_usuarios+'">'+v.nm_pessoa+'</option>'
                    });                
                }
                el.html(opt);
            },
            beforeSend:function(){
                opt = '<option>Carregando...</option>';
            },
            complete:function(){},
            error:function(a,b,c){
                console.log('a',a);
                console.log('b',b);
                console.log('c',c);
            }
        });
    }

}

function alteraStatus(id, status, motivo='') {
        if (id && status) {
            $.ajax({
                url:'/materiais-fracionados-status',
                type:'post',
                dataType:'json',
                data:{
                    id_materiais_fracionados:id,
                    status:status,
                    motivo:motivo
                },
                success:function(data){
                    gerarAlerta(data.msg, 'Aviso', data.type);
                    if (data.success) {
                        $('div[id=modal-materiais-fracionados-descarte]').modal('hide');
                        carrega_lista();
                    }
                },
                beforeSend:function(){
                    preloaderStart();
                },
                error:function(a,b,c){
                    preloaderStop();
                    gerarAlerta(a.responseJSON.msg, 'Aviso', 'danger');
                    console.error('a',a);
                    console.error('b',b);
                    console.error('c',c);
                },
                complete:function(){
                    preloaderStop();
                }
            });
        }
    }

$(document).ready(function(){

    formFieldsRequered();

    comboSetores();
    comboUsuarios();
    carrega_lista();

    $('div[id=modal-materiais-fracionados-descarte]').on('hidden.bs.modal', function (e) {
        $('form[name=form-<?=str_replace('_','-',$prefix)?>]').find('input, select, textarea').each(function(){
            $(this).val('').removeClass('is-invalid');
        });
    });

    

    $(document).on('click','a[rel=btn-material-status]', function(e){
        e.preventDefault();
        const id = $(this).attr('id');
        const status = $(this).attr('data-status');
        if (id) {            
            if (status!='D') {
                alteraStatus(id, status);
            } else {
                $('form[name=form-materiais-francionados-descarte] input[name=id_materiais_fracionados]').val(id);
                $('form[name=form-materiais-francionados-descarte] input[name=status]').val(status);
                $('div[id=modal-materiais-fracionados-descarte]').modal('show');
            }
        }
    });

    $(document).on('click','a[rel=btn-materiais-fracionados-descarte-salvar]', function(e){
        e.preventDefault();
        const id = $('form[name=form-materiais-francionados-descarte] input[name=id_materiais_fracionados]').val();
        const status = $('form[name=form-materiais-francionados-descarte] input[name=status]').val();
        const motivo = $('form[name=form-materiais-francionados-descarte] textarea[name=motivo_descarte]').val();
        if (id) {            
            alteraStatus(id, status, motivo);
        }
    });

    $(document).on('click','a[rel=btn-material-historico]', function(e){
        e.preventDefault();
        carrega_lista_log($(this).attr('id'));
        $('div[id=modal-materiais-fracionados-historico]').modal('show');
    });
    

    $(document).on('click','a[rel=btn-form-pdf]', function(e){
        e.preventDefault();
        const form = $('form[name=form-materiais-fracionados-pdf]'); 

        const el_setor = $('select[name=fil_setor]');
        let id_setor;
        if (el_setor.is(':visible')) {
            id_setor = el_setor.val();
        } else {
            id_setor = '<?=$_SESSION['usuario']['id_setor']?>';
        }
        
        const el_usuarios = $('select[name=fil_usuarios]');
        let id_usuarios;
        if (el_usuarios.is(':visible')) {
            id_usuarios = el_usuarios.val();
        } else {
            id_usuarios = '<?=$_SESSION['usuario']['id_usuarios']?>';
        }        

        const status = $('select[name=fil_status]').val();

        form.append('<input type="hidden" name="tipo" value="pdf" />');
        form.append('<input type="hidden" name="id_setor" value="'+id_setor+'" />');
        form.append('<input type="hidden" name="id_usuarios" value="'+id_usuarios+'" />');
        form.append('<input type="hidden" name="status" value="'+status+'" />');
        form.submit();
    });

    $(document).on('click','a[rel=btn-form-filtro]', function(e){
        e.preventDefault();
        carrega_lista();
    });
    

});
</script>