<?php
require_once('header.php');
$titulo = 'Usuários';
$prefix = 'usuarios';
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
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Setor</th>
                                            <th>Perfil</th>
                                            <th>CPF / CNPJ</th>
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

    <!-- Modal Form -->
    <div class="modal fade" id="modal-<?=$prefix?>" tabindex="-1" role="dialog" aria-labelledby="modal<?=$prefix?>" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal<?=$prefix?>"><?=$titulo?></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-<?=$prefix?>" class="formValidate">
                        <input type="hidden" class="" id="<?=$prefix?>_id_usuarios" name="<?=$prefix?>_id_usuarios" value="">
                        <input type="hidden" class="" id="<?=$prefix?>_id_pessoas" name="<?=$prefix?>_id_pessoas" value="">
                        
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-9 col-xl-9 col-xxl-9">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_nome">Nome</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_nome" name="<?=$prefix?>_nome" placeholder="Nome Completo">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_tp_juridico">Tipo Jurídico</label>
                                    <select class="form-select requered" id="<?=$prefix?>_tp_juridico" name="<?=$prefix?>_tp_juridico">
                                        <option value="F">Pessoa Física</option>
                                        <option value="J">Pessoa Jurídica</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_cpf_cnpj">CPF / CNPJ</label>
                                    <input type="text" class="form-control requered mask-cpf" id="<?=$prefix?>_cpf_cnpj" name="<?=$prefix?>_cpf_cnpj" placeholder="999.999.999-99">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                <label for="<?=$prefix?>_dt_nascimento">Dt. Nascimento</label>
                                    <input type="text" class="form-control requered mask-data" id="<?=$prefix?>_dt_nascimento" name="<?=$prefix?>_dt_nascimento" placeholder="99/99/9999">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_genero">Gênero</label>
                                    <select class="form-select requered" id="<?=$prefix?>_genero" name="<?=$prefix?>_genero">
                                        <option value="F">Feminino</option>
                                        <option value="M">Masculino</option>
                                        <option value="O">Outro</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_email">E-mail</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_email" name="<?=$prefix?>_email" placeholder="aaaaa@aaaa.com">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_telefone">Telefone</label>
                                    <input type="text" class="form-control requered mask-celular" id="<?=$prefix?>_telefone" name="<?=$prefix?>_telefone" placeholder="(99) 99999-9999">
                                </div>
                            </div>
                            <!--
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                <label for="<?=$prefix?>_senha">Senha</label>
                                    <input type="password" class="form-control requered" id="<?=$prefix?>_senha" name="<?=$prefix?>_senha" placeholder="99999">
                                </div>
                            </div>
                            -->
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_status">Status</label>
                                    <select class="form-select requered" id="<?=$prefix?>_status" name="<?=$prefix?>_status">
                                        <option value="A">Ativo</option>
                                        <option value="I">Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-xxl-2">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_cep">Cep</label>
                                    <input type="text" class="form-control requered mask-cep" id="<?=$prefix?>_cep" name="<?=$prefix?>_cep" placeholder="99999-999">
                                    <input type="hidden" class="" id="<?=$prefix?>_cod_ibge" name="<?=$prefix?>_cod_ibge" placeholder="">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8 col-xxl-8">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_logradouro">Logradouro</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_logradouro" name="<?=$prefix?>_logradouro" placeholder="Rua Teste...">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2 col-xxl-2">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_numero">Número</label>
                                    <input type="text" class="form-control" id="<?=$prefix?>_numero" name="<?=$prefix?>_numero" placeholder="99-99">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_complemento">Complemento</label>
                                    <input type="text" class="form-control" id="<?=$prefix?>_complemento" name="<?=$prefix?>_complemento" placeholder="Fundos">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_bairro">Bairro</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_bairro" name="<?=$prefix?>_bairro" placeholder="Bairro">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_cidade">Cidade</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_cidade" name="<?=$prefix?>_cidade" placeholder="Cidade">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_estado">Estado</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_estado" name="<?=$prefix?>_estado" placeholder="Estado">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_id_setor">Setor</label>
                                    <select class="form-select requered" id="<?=$prefix?>_id_setor" name="<?=$prefix?>_id_setor"></select>
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

    <div class="modal fade" id="modal-<?=$prefix?>-perfil" tabindex="-1" role="dialog" aria-labelledby="modal<?=$prefix?>perfil" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal<?=$prefix?>perfil"><?=$titulo?> / Perfil</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-<?=$prefix?>-perfil" class="formValidate"></form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fa fa-xmark"></i> Fechar
                    </button>
                    <a class="btn btn-primary" href="#" rel="btn-<?=$prefix?>-perfil-salvar">
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
                                    return data.id_usuarios;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.nm_pessoa;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.nm_setor;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.ds_perfil;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.cpf_cnpj;
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

                                    let color_send = 'primary';
                                    let title = 'E-mail enviado e senha de usuário definida';
                                    if(data.senha == '') {
                                        color_send = 'warning';
                                        title = 'Senha de usuário não definida';
                                    }

                                    campo+= '<a href="#" title="'+title+'" id="'+data.id_usuarios+'" rel="btn-<?=$prefix?>-register-password" role="button" class="btn btn-'+color_send+' btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-envelope"></i>'+
                                                '</a>';

                                    campo+= '<a href="#" title="relacionamento de perfil" id="'+data.id_usuarios+'" rel="btn-<?=$prefix?>-perfil" role="button" class="btn btn-dark btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-list"></i>'+
                                                '</a>';                                   

                                    <?php if(in_array('ALTERAR', $arr_permissoes)):?>
                                        campo+= '<a href="#" title="Editar" id="'+data.id_usuarios+'" rel="btn-<?=$prefix?>-editar" role="button" class="btn btn-primary btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-edit"></i>'+
                                                '</a>';
                                    <?php endif;?>
                                    
                                    <?php if(in_array('DELETAR', $arr_permissoes)):?>
                                        campo+= '<a href="#" title="Deletar" rel="btn-<?=$prefix?>-deletar" id="'+data.id_usuarios+'" role="button" class="btn btn-danger btn-sm" style="margin: 1px 2px">'+
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

function usuario_perfil(id_usuarios) {
    $.ajax({
        url:'/usuarios-perfil-json',
        type:'post',
        dataType:'json',
        data:{
            'id_usuarios':id_usuarios
        },
        success:function(data){
            console.log('data', data);
            
            if (data.data) {
                let el = $('form[name=form-<?=$prefix?>-perfil]');
                let body = '';
                body += '<input type="hidden" name="id_usuarios_perfil" value="'+id_usuarios+'" />';

                if (data.data) {
                    body += '<table class="table table-bordered">';
                    $.each(data.data, function(i, v){
                        body += '<tr>';
                                body += '<td class="">'+
                                            '<div class="form-check">'+
                                                '<input class="form-check-input" '+(v.fg_usuarios_perfil=='S'?'checked':'')+' type="checkbox" value="'+v.id_perfil+'" id="arr_id_perfil_'+v.id_perfil+'" name="arr_id_perfil[]">'+
                                            '</div>'+
                                        '</td>';
                                body += '<td>'+v.descricao+'</td>';
                        body += '</tr>';
                    });
                    body += '</table>';
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

function fieldCpfCnpj(tipo) {
    $('input[name=<?=$prefix?>_cpf_cnpj]').unmask().val('');
    if(tipo=='F') {
        let mask = "999.999.999-99";
        $('input[name=<?=$prefix?>_cpf_cnpj]').mask(mask).attr('placeholder', mask);
    } else {
        let mask = "99.999.999/9999-99";
        $('input[name=<?=$prefix?>_cpf_cnpj]').mask(mask).attr('placeholder', mask);
    }
}

function comboSetores(id_setor=''){
    const el = $('select[name=<?=$prefix?>_id_setor]')
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
                    opt+= '<option value="'+v.id_setor+'" '+(v.id_setor==id_setor?'selected':'')+' >'+v.nome+'</option>'
                });                
            }
            el.html(opt);
        },
        beforeSend:function(){
            opt = '<option>Carregando...</option>';
        },
        complete:function(){

        },
        error:function(a,b,c){
            console.log('a',a);
            console.log('b',b);
            console.log('c',c);
        }
    });
}

$(document).ready(function(){

    formFieldsRequered();
    carrega_lista();

    $(document).on('click', 'a[rel=btn-<?=$prefix?>-novo]', function(e){
        e.preventDefault();
        comboSetores();
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
                        $('input[name=<?=$prefix?>_id_usuarios]').val(data.data.id_usuarios);
                        $('input[name=<?=$prefix?>_id_pessoas]').val(data.data.id_pessoas);
                        $('input[name=<?=$prefix?>_nome]').val(data.data.nm_pessoa);
                        $('select[name=<?=$prefix?>_tp_juridico]').val(data.data.tp_juridico);
                        $('input[name=<?=$prefix?>_dt_nascimento]').val(data.data.dt_nascimento);
                        $('select[name=<?=$prefix?>_genero]').val(data.data.genero);
                        $('input[name=<?=$prefix?>_email]').val(data.data.email);
                        $('input[name=<?=$prefix?>_senha]').val(data.data.senha);
                        $('select[name=<?=$prefix?>_status]').val(data.data.status);
                        $('input[name=<?=$prefix?>_cep]').val(data.data.cep);
                        $('input[name=<?=$prefix?>_logradouro]').val(data.data.logradouro);
                        $('input[name=<?=$prefix?>_numero]').val(data.data.numero);
                        $('input[name=<?=$prefix?>_bairro]').val(data.data.bairro);
                        $('input[name=<?=$prefix?>_cidade]').val(data.data.cidade);
                        $('input[name=<?=$prefix?>_estado]').val(data.data.estado);
                        $('input[name=<?=$prefix?>_cod_ibge]').val(data.data.cod_ibge);
                        $('input[name=<?=$prefix?>_telefone]').val(data.data.telefone);
                        
                        fieldCpfCnpj(data.data.tp_juridico);
                        $('input[name=<?=$prefix?>_cpf_cnpj]').val(data.data.cpf_cnpj);
                        comboSetores(data.data.id_setor);

                        $('div#modal-<?=$prefix?>').modal('show');
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
                    $('div#modal-<?=$prefix?>').modal('hide');
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

    $(document).on('change', 'select[name=<?=$prefix?>_tp_juridico]', function(){
        fieldCpfCnpj($(this).val());
    });

    $(document).on('click', 'a[rel=btn-<?=$prefix?>-perfil]', function(e){
        usuario_perfil($(this).attr('id'));
        $('div[id=modal-<?=$prefix?>-perfil]').modal('show');
    });

    $('div#modal-<?=$prefix?>-perfil').on('hidden.bs.modal', function (e) {
        $('form[name=form-<?=$prefix?>-perfil]').html('');
    });

    $(document).on('click', 'a[rel=btn-<?=$prefix?>-perfil-salvar]', function(e){
        e.preventDefault();
        const id_usuarios = $('form[name=form-<?=$prefix?>-perfil] input[name=id_usuarios_perfil]').val();
        let arr_perfil = [];
        $('form[name=form-<?=$prefix?>-perfil]').find('input[name^=arr_id_perfil]').each(function(){
            if($(this).is(':checked')) {
                arr_perfil.push($(this).val());
            }
        });

        $.ajax({
            url:'/<?=$prefix?>-perfil-save',
            type:'post',
            dataType:'json',
            data:{
                'id_usuarios':id_usuarios,
                'arr_perfil': arr_perfil
            },
            success:function(data){
                gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                $('div[id=modal-<?=$prefix?>-perfil]').modal('hide');
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

    $(document).on('click', 'a[rel=btn-<?=$prefix?>-register-password]', function(e){
        e.preventDefault();
        const id_usuarios = $(this).attr('id');
        
        if (id_usuarios) {
            $.ajax({
                url:'/usuarios-send-password',
                type:'post',
                dataType:'json',
                data:{
                    'id_usuarios':id_usuarios,
                },
                success:function(data){
                    gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                    if (data.success) {
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

    });

    $(document).on('focusout', 'input[name=<?=$prefix?>_cep]', function(e){
        const _cep = $(this).val();
        let cep = $('input[name=<?=$prefix?>_cep]');
        let logradouro = $('input[name=<?=$prefix?>_logradouro]');
        let numero = $('input[name=<?=$prefix?>_numero]');
        let bairro = $('input[name=<?=$prefix?>_bairro]');
        let cidade = $('input[name=<?=$prefix?>_cidade]');
        let estado = $('input[name=<?=$prefix?>_estado]');
        let cod_ibge = $('input[name=<?=$prefix?>_cod_ibge]');
        if (_cep) {          
            logradouro.val('Consultando...');  
            consultaViaCep(_cep)
                .then((data) => {
                    if(data.erro){
                        logradouro.val('');
                        cep.val('');
                        bairro.val('');
                        cidade.val('');
                        estado.val('');
                        cod_ibge.val('');
                    } else {
                        logradouro.val(data.logradouro);
                        bairro.val(data.bairro);
                        cidade.val(data.localidade);
                        estado.val(data.uf);
                        cod_ibge.val(data.ibge);
                    }
                })
                .catch(e => {
                    logradouro.val('');
                    cep.val('');
                    bairro.val('');
                    cidade.val('');
                    estado.val('');
                    cod_ibge.val('');
                    gerarAlerta('Erro ao consultar o Cep, tente novamente!', 'Erro', 'danger');
                });

            //let endereco = consultaViaCep(cep);
        }
    });

});
</script>