<?php
require_once('header.php');
$titulo = 'Materiais Fracionados';
$prefix = 'material_fracionados';
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
                                <div class="col text-right"></div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-<?=str_replace('_','-',$prefix)?>" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th >ID</th>
                                            <th >Material</th>
                                            <th >Quantidade Fracionada</th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal<?=$prefix?>">Controle de <?=$titulo?></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-<?=str_replace('_','-',$prefix)?>" class="formValidate">
                        <input type="hidden" class="" id="<?=$prefix?>_id_materiais" name="<?=$prefix?>_id_materiais" value="">

                        <div class="row">

                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">                                
                                <div class="form-group">
                                    <label for="<?=$prefix?>_cod_barras">Código de Barras</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_cod_barras" name="<?=$prefix?>_cod_barras" placeholder="Descrição">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_descricao">Descrição</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_descricao" name="<?=$prefix?>_descricao" placeholder="Descrição">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_dias_vencimento">Qtd. dias vencimento</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_dias_vencimento" name="<?=$prefix?>_dias_vencimento" maxlength="2" placeholder="Ex.: 30">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_dias_vencimento_aberto">Qtd. dias venc. aberto</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_dias_vencimento_aberto" name="<?=$prefix?>_dias_vencimento_aberto" maxlength="2" placeholder="Ex.: 30">
                                </div>
                            </div>
                            
                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_id_materiais_categorias">Categoria</label>
                                    <select class="form-select requered" id="<?=$prefix?>_id_materiais_categorias" name="<?=$prefix?>_id_materiais_categorias"></select>
                                </div>
                            </div>
                            
                            <!--
                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_id_materiais_tipos">Tipo</label>
                                    <select class="form-select requered" id="<?=$prefix?>_id_materiais_tipos" name="<?=$prefix?>_id_materiais_tipos"></select>
                                </div>
                            </div>
                            -->

                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_id_pessoas_fornecedor">Fornecedores</label>
                                    <select class="form-select" id="<?=$prefix?>_id_pessoas_fornecedor" name="<?=$prefix?>_id_pessoas_fornecedor"></select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_id_pessoas_fabricante">Fabricantes</label>
                                    <select class="form-select" id="<?=$prefix?>_id_pessoas_fabricante" name="<?=$prefix?>_id_pessoas_fabricante"></select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_id_materiais_marcas">Marcas</label>
                                    <select class="form-select" id="<?=$prefix?>_id_materiais_categorias" name="<?=$prefix?>_id_materiais_marcas"></select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_id_unidades_medidas">Unidades de Medida</label>
                                    <select class="form-select requered" id="<?=$prefix?>_id_unidades_medidas" name="<?=$prefix?>_id_unidades_medidas"></select>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_dt_fabricacao">Data de Fabricação</label>
                                    <input type="text" class="form-control mask-data requered" id="<?=$prefix?>_dt_fabricacao" name="<?=$prefix?>_dt_fabricacao" maxlength="10" placeholder="Ex.: 99/99/9999">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_dt_vencimento">Data de Vencimento</label>
                                    <input type="text" class="form-control mask-data requered" id="<?=$prefix?>_dt_vencimento" name="<?=$prefix?>_dt_vencimento" maxlength="10" placeholder="Ex.: 99/99/9999">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_dt_vencimento_aberto">Data de Venc. Aberto</label>
                                    <input type="text" class="form-control mask-data requered" id="<?=$prefix?>_dt_vencimento_aberto" name="<?=$prefix?>_dt_vencimento_aberto" maxlength="10" placeholder="Ex.: 99/99/9999">
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_peso">Peso</label>
                                    <input type="text" class="form-control valor-decimal requered" id="<?=$prefix?>_peso" name="<?=$prefix?>_peso" maxlength="10" placeholder="Ex.: 3,00">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_quantidade">Quantidade</label>
                                    <input type="text" class="form-control somente_numeros requered" id="<?=$prefix?>_quantidade" name="<?=$prefix?>_quantidade" maxlength="2" placeholder="Ex.: 11">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_preco">Preço</label>
                                    <input type="text" class="form-control moeda_real requered" id="<?=$prefix?>_preco" name="<?=$prefix?>_preco" maxlength="10" placeholder="Ex.: 3,00">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_lote">Lote</label>
                                    <input type="text" class="form-control requered" id="<?=$prefix?>_lote" name="<?=$prefix?>_lote" maxlength="50" placeholder="">
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                <div class="form-group">
                                    <label for="<?=$prefix?>_status">Status</label>
                                    <select class="form-select requered" id="<?=$prefix?>_status" name="<?=$prefix?>_status">
                                        <option value="A">Ativo</option>
                                        <option value="I">Inativo</option>
                                    </select>
                                </div>
                            </div>
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
            "url": '/materiais-fracionados-json',
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
                                    return data.id_materiais_fracionados;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.ds_materiais;
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
                                        campo+= '<a href="#" title="Fracionar" id="'+data.id_materiais_fracionados+'" rel="btn-<?=str_replace('_','-',$prefix)?>-fracionar" role="button" class="btn btn-primary btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-divide"></i>'+
                                                '</a>';
                                    <?php endif;?>

                                    <?php if(in_array('DELETAR', $arr_permissoes)):?>
                                        /*
                                        campo+= '<a href="#" title="Deletar" rel="btn-<?=str_replace('_','-',$prefix)?>-deletar" id="'+data.id_materiais_fracionados+'" role="button" class="btn btn-danger btn-sm" style="margin: 1px 2px">'+
                                                    '<i class="fas fa-trash"></i>'+
                                                '</a>';*/
                                    <?php endif;?>

                                    return campo;
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

function busca_produto_codigo_barra(cod_barra='') {

    if (cod_barra!='') {
        $.ajax({
            url:'/produtos-busca-codigo-barras/'+cod_barra,
            type:'get',
            dataType:'json',
            data:{},
            success:function(data){
                console.log('data',data);

                if(data.data.descricao) {
                    $('input[name=<?=$prefix?>_descricao]').val(data.data.descricao);
                    $('input[name=<?=$prefix?>_dias_vencimento]').val(data.data.dias_vencimento).prop('readonly', true);;
                    $('input[name=<?=$prefix?>_dias_vencimento_aberto]').val(data.data.dias_vencimento_aberto).prop('readonly', true);;
                    $('input[name=<?=$prefix?>_dt_vencimento]').val(data.data.dt_vencimento);
                    $('input[name=<?=$prefix?>_dt_vencimento_aberto]').val(data.data.dt_vencimento_aberto);
                    $('input[name=<?=$prefix?>_dt_fabricacao]').val('<?=date('d/m/Y')?>');
                }

                //$('input[name=<?=$prefix?>_cod_barras]').prop('readonly', false);
                
            },
            beforeSend:function(){
                preloaderStart();
            },
            error:function(a,b,c){
                preloaderStop();
                gerarAlerta('Produto não localizado.', 'Aviso', 'warning');
                $('input[name=<?=$prefix?>_descricao]').val('');
                $('input[name=<?=$prefix?>_dias_vencimento]').val('').prop('readonly', false);;
                $('input[name=<?=$prefix?>_dias_vencimento_aberto]').val('').prop('readonly', false);;
                $('input[name=<?=$prefix?>_dt_vencimento]').val('');
                $('input[name=<?=$prefix?>_dt_vencimento_aberto]').val('');
                $('input[name=<?=$prefix?>_dt_fabricacao]').val('');
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

function calcula_datas() {

    const dt_fabricacao = $('input[name=<?=$prefix?>_dt_fabricacao]').val();
    const qtd_dias_vencimento = $('input[name=<?=$prefix?>_dias_vencimento]').val();
    const qtd_dias_vencimento_aberto = $('input[name=<?=$prefix?>_dias_vencimento_aberto]').val();

    if (dt_fabricacao!='' && qtd_dias_vencimento && qtd_dias_vencimento_aberto) {
        $.ajax({
            url:'/produtos-calcula-datas',
            type:'post',
            dataType:'json',
            data:{
                'dt_fabricacao':dt_fabricacao,
                'qtd_dias_vencimento':qtd_dias_vencimento,
                'qtd_dias_vencimento_aberto':qtd_dias_vencimento_aberto
            },
            success:function(data){
                console.log('data',data);
                $('input[name=<?=$prefix?>_dt_vencimento]').val(data.data.dt_vencimento);
                $('input[name=<?=$prefix?>_dt_vencimento_aberto]').val(data.data.dt_vencimento_aberto);
            },
            beforeSend:function(){
                preloaderStart();
            },
            error:function(a,b,c){
                preloaderStop();
                gerarAlerta('Produto não localizado.', 'Aviso', 'warning');
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

//COMBO DE CATEGORIAS
function comboCategorias(id_materiais_categorias=''){
    const el = $('select[name=<?=$prefix?>_id_materiais_categorias]')
    let opt = '';
    $.ajax({
        url:'/materiais-categorias-json',
        type:'post',
        dataType:'json',
        data:{status:'A'},
        success:function(data) {
            console.log('data', data);
            if (data.data.length > 0) {
                opt = '<option value="">--Selecione--</option>';
                $.each(data.data, function(i,v){
                    opt+= '<option value="'+v.id_materiais_categorias+'" '+(v.id_materiais_categorias==id_materiais_categorias?'selected':'')+' >'+v.descricao+'</option>'
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

//COMBO DE CATEGORIAS
function comboTipos(id_materiais_tipos=''){
    const el = $('select[name=<?=$prefix?>_id_materiais_tipos]')
    let opt = '';
    $.ajax({
        url:'/materiais-tipos-json',
        type:'post',
        dataType:'json',
        data:{status:'A'},
        success:function(data) {
            console.log('data', data);
            if (data.data.length > 0) {
                opt = '<option value="">--Selecione--</option>';
                $.each(data.data, function(i,v){
                    opt+= '<option value="'+v.id_materiais_tipos+'" '+(v.id_materiais_tipos==id_materiais_tipos?'selected':'')+' >'+v.descricao+'</option>'
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

//COMBO DE FORNECEDORES E FABRICANTES
function comboFornecedoresFabricantes(id='', id_tipos_pessoas=''){
    let el = '';
    if (id_tipos_pessoas==2) {
        el = $('select[name=<?=$prefix?>_id_pessoas_fabricante]')
    } else {
        el = $('select[name=<?=$prefix?>_id_pessoas_fornecedor]')
    }
    
    let opt = '';
    $.ajax({
        url:'/fornecedores-fabricantes-json',
        type:'post',
        dataType:'json',
        data:{status:'A', id_tipos_pessoas:id_tipos_pessoas},
        success:function(data) {
            console.log('data', data);
            if (data.data.length > 0) {
                opt = '<option value="">--Selecione--</option>';
                $.each(data.data, function(i,v){
                    opt+= '<option value="'+v.id_pessoas+'" '+(v.id_pessoas==id?'selected':'')+' >'+v.nm_pessoa+'</option>'
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

//COMBO DE MARCAS
function comboMarcas(id_materiais_marcas=''){
    const el = $('select[name=<?=$prefix?>_id_materiais_marcas]')
    let opt = '';
    $.ajax({
        url:'/materiais-marcas-json',
        type:'post',
        dataType:'json',
        data:{status:'A'},
        success:function(data) {
            console.log('data', data);
            if (data.data.length > 0) {
                opt = '<option value="">--Selecione--</option>';
                $.each(data.data, function(i,v){
                    opt+= '<option value="'+v.id_materiais_marcas+'" '+(v.id_materiais_marcas==id_materiais_marcas?'selected':'')+' >'+v.descricao+'</option>'
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

//COMBO DE MARCAS
function comboUnidadesMedidas(id_unidades_medidas=''){
    const el = $('select[name=<?=$prefix?>_id_unidades_medidas]')
    let opt = '';
    $.ajax({
        url:'/unidades-medidas-json',
        type:'post',
        dataType:'json',
        data:{status:'A'},
        success:function(data) {
            console.log('data', data);
            if (data.data.length > 0) {
                opt = '<option value="">--Selecione--</option>';
                $.each(data.data, function(i,v){
                    opt+= '<option value="'+v.id_unidades_medidas+'" '+(v.id_unidades_medidas==id_unidades_medidas?'selected':'')+' >'+v.descricao+'</option>'
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

    $(document).on('click', 'a[rel=btn-<?=str_replace('_','-',$prefix)?>-novo]', function(e){
        e.preventDefault();
        comboCategorias();
        comboTipos();
        comboFornecedoresFabricantes('',2);
        comboFornecedoresFabricantes('',3);
        comboMarcas();
        comboUnidadesMedidas();
        $('div#modal-<?=str_replace('_','-',$prefix)?>').modal('show');
        $('select[name=<?=$prefix?>_status]').val('A').prop('disabled', true);
    });

    $('div#modal-<?=str_replace('_','-',$prefix)?>').on('hidden.bs.modal', function (e) {
        $('form[name=form-<?=str_replace('_','-',$prefix)?>]').find('input, select').each(function(){
            $(this).val('').removeClass('is-invalid');
        });
        $('input[name=<?=$prefix?>_cod_barras]').prop('readonly', false);
        $('input[name=<?=$prefix?>_dias_vencimento]').prop('readonly', false);
        $('input[name=<?=$prefix?>_dias_vencimento_aberto]').prop('readonly', false);
        
    });

    $(document).on('click','a[rel=btn-<?=str_replace('_','-',$prefix)?>-editar]', function(e){
        e.preventDefault();
        const id = $(this).attr('id');
        if (id) {            
            $.ajax({
                url:'/materiais-edit/'+id,
                type:'get',
                dataType:'json',
                data:{},
                success:function(data){
                    if (data.data) {
                        $.each(data.data, function(i,v){
                            $('form[name=form-<?=str_replace('_','-',$prefix)?>] #<?=$prefix?>_'+i+'').val(v);
                        });

                        console.log('material', data);

                        comboCategorias(data.data.id_materiais_categorias);
                        comboTipos(data.data.id_materiais_tipos);
                        comboFornecedoresFabricantes(data.data.id_pessoas_fabricante,2);
                        comboFornecedoresFabricantes(data.data.id_pessoas_fornecedor,3);
                        comboMarcas(data.data.id_materiais_marcas);
                        comboUnidadesMedidas(data.data.id_unidades_medidas);

                        $('input[name=<?=$prefix?>_cod_barras]').prop('readonly', true);
                        $('input[name=<?=$prefix?>_dias_vencimento]').prop('readonly', true);
                        $('input[name=<?=$prefix?>_dias_vencimento_aberto]').prop('readonly', true);
                        $('select[name=<?=$prefix?>_status]').prop('disabled', false);                        

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
			url: '/materiais-save',
			resetForm:false
		}).submit();
	});

    $(document).on('keypress', 'input[name=<?=$prefix?>_codigo_barras]', function(e) {
        if (e.keyCode == 13) {
            const codigo_barras = $(this).val();
            if (codigo_barras) {
                console.log('CODIGO DE BARRAS', codigo_barras);
                ///produtos
                $.ajax({
                url:'/materiais-busca-codigo-barras/'+codigo_barras,
                type:'get',
                dataType:'json',
                data:{},
                success:function(data){
                    //gerarAlerta(data.msg, 'Aviso', data.type);
                    console.log('data', data);
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
    });

    $(document).on('focusout','input[name=<?=$prefix?>_cod_barras]', function(){
        const cod_barra = $(this).val();
        if (cod_barra) {
            busca_produto_codigo_barra(cod_barra);
        }
    });

    $(document).on('focusout','input[name=<?=$prefix?>_dt_fabricacao]', function(){
        calcula_datas();
    });

});
</script>