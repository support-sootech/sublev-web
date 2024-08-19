<?php
require_once('header.php');
$titulo = 'Fracionar Material';
$prefix='fracionamento';
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
                <h1 class="h3 mb-2 text-gray-800"><?=$titulo?></h1>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="row">
                            <div class="col text-left" id="div_btn_voltar">
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col text-center">
                                <h6 class="m-0 font-weight-bold text-primary" id="div_titulo_fracionamento"></h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="fracionamento">
                        
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
    <div class="modal fade" id="modal-fracionamento" tabindex="-1" role="dialog" aria-labelledby="modalFracionamento" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFracionamento"></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-fracionamento" class="formValidate">
                        <input type="hidden" class="" id="id_materiais" name="id_materiais" value="">
                        <input type="hidden" class="" id="peso" name="peso" value="">
                        <div id="fracao-campos"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fa fa-xmark"></i> Fechar
                    </button>
                    <a class="btn btn-primary" href="#" rel="btn-fracao-salvar">
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
function voltar_categorias_materiais(){
    
        $("#div_titulo_fracionamento").html('Selecione a Categoria do Material');
        $("#materiais").hide();
        $("#div_btn_voltar").hide();
        carrega_categorias_materiais();
  
}
function voltar_materiais(id){
    
    $("#div_titulo_fracionamento").html('Selecione o Material');
    $("#materiais-detalhes").hide();
    carrega_materiais(id);

}
function carrega_categorias_materiais(){
    $.ajax({
        url:'/materiais-categorias-json',
        type:'post',
        dataType:'json',
        data:{
            status:'A'
        },
        success:function(data){
            console.log(data);

            if (data.data) {
                
                let body = '';
                body += '<div class="row" id="categorias">';

                if (data.data) {
                    
                    $.each(data.data, function(i, v){
                        body += '<div class="p-4 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 d-grid">';
                        body += '<a id="'+v.id_materiais_categorias+'" rel="btn-cat-material-<?=str_replace('_','-',$prefix)?>-selecionar" class="btn btn-outline-primary p-4" style="height: 80px;">'+v.descricao+'</a>';
                        body += '</div>';
                    });
                    
                }
                body += '</div>';

                $("#fracionamento").html(body);
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
};

function carrega_materiais(id){
    $.ajax({
        url:'/materiais-da-categoria-json',
        type:'post',
        dataType:'json',
        data:{
            status:'A',
            'id_materiais_categorias':id
        },
        success:function(data){
            console.log(data);
            
            $("#div_titulo_fracionamento").html('Selecione o Material');
            $("#div_btn_voltar").show();
            $("#div_btn_voltar").html('<a id='+id+' rel="btn-material-<?=str_replace('_','-',$prefix)?>-voltar" class="btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>');
            $("#categorias").hide();

            if (data.data) {
                
                let body = '';
                body += '<div class="row" id="materiais">';

                if (data.data) {
                    
                    $.each(data.data, function(i, v){

                        if (v.quantidade > 0) {
                            body += '<div class="p-4 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 d-grid gap-2mx-auto">';
                            body += '<a id="'+v.id_materiais+'" rel="btn-material-<?=str_replace('_','-',$prefix)?>-selecionar" class="btn btn-outline-primary p-4" style="height: 80px;">'+v.descricao+' <small>('+v.quantidade+')</small></a>';
                            body += '</div>';
                        }

                    });
                    
                }
                body += '</div>';
                $("#fracionamento").html(body);
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

function carrega_detalhes_material(id){
    $.ajax({
        url:'/detalhes-materiais-json',
        type:'post',
        dataType:'json',
        data:{
            status:'A',
            'id_materiais':id
        },
        success:function(data){
            console.log('carrega_detalhes_material',data);
            
            $("#materiais").hide();
            $("#div_titulo_fracionamento").html('Fracionamento do Material');
            $("#div_btn_voltar").html('<a rel="btn-detalhes-material-<?=str_replace('_','-',$prefix)?>-voltar" class="btn btn-primary btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>');
            if (data) {
                
                let body = '';
                body += '<div class="container" id="material-detalhes"><div class="row col-sm text-center">';

                if (data) {
                    body += '<div class="col-sm"><b>Material</b></div>';
                    body += '<div  class="col-sm"><b>Marca</b></div>';
                    body += '<div  class="col-sm"><b>Data de Validade (Fechado)</b></div>';
                    body += '<div  class="col-sm"><b>Data de Validade (Após Aberto)</b></div>';
                    body += '<div  class="col-sm"><b>Peso</b></div>';
                    body += '<div  class="col-sm"><b>Fracionar em</b></div>';
                    body += '</div><div class="row col-sm text-center">';
                    
                    data_vencimento = new Date(data.dt_vencimento);
                    dataVencimentoFormatada = data_vencimento.toLocaleDateString('pt-BR', {timeZone: 'UTC'});
                    data_vencimento_aberto = new Date(data.dt_vencimento_aberto);
                    dataVencimentoAbertoFormatada = data_vencimento_aberto.toLocaleDateString('pt-BR', {timeZone: 'UTC'});
                    body += '<div class="col-sm">'+data.descricao+'</div>';
                    body += '<div class="col-sm">'+data.marca+'</div>';
                    body += '<div class="col-sm">'+dataVencimentoFormatada+'</div>';
                    body += '<div class="col-sm">'+dataVencimentoAbertoFormatada+'</div>';
                    body += '<div class="col-sm">'+data.peso+' '+data.ds_unidade_medida+'</div>';
                    body += '<div class="col-sm"><input type="text" aria-label="Default" aria-describedby="inputGroup-sizing-default" id="qtde_fracionar" class="form-control" /></div>';
                    body += '<div class="col-sm" id="id_material_categoria" style="display:none;">'+data.id_materiais_categorias+'</div>';
                    
                }
                body += '</div><div class="row col-sm text-center">&nbsp;</div><div class="row col-sm text-center">&nbsp;</div><div class="row col-sm text-center">';
                body += '<div class="col-sm"><a id="btn_fracionar_'+id+'" data-peso="'+data.peso+'" data-descricao="'+data.descricao+'" data-id="'+data.id_materiais+'" rel="btn-material-<?=str_replace('_','-',$prefix)?>-fracionar" class="btn btn-success p-4" style="height: 70px; width:200px;">Fracionar</a></div>';
                body += '<div class="col-sm"><a id="btn_utilizar_'+id+'" data-id="'+data.id_materiais+'" rel="btn-material-<?=str_replace('_','-',$prefix)?>-utilizar" class="btn btn-primary p-4" style="height: 70px; width:200px;">Utilizar Unidade</a></div>';
                body += '<div class="col-sm"><a id="btn_cancelar_'+id+'" data-id="'+data.id_materiais+'" rel="btn-material-<?=str_replace('_','-',$prefix)?>-cancelar" class="btn btn-warning p-4" style="height: 70px; width:200px;">Cancelar</a></div>';
                body += '</div></div>';
                $("#fracionamento").html(body);
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
    $("#div_titulo_fracionamento").html('Selecione a Categoria do Material');
    carrega_categorias_materiais();

    $(document).on('click','a[rel=btn-material-<?=str_replace('_','-',$prefix)?>-voltar]', function(e){
        voltar_categorias_materiais();
    }); 

    $(document).on('click','a[rel=btn-detalhes-material-<?=str_replace('_','-',$prefix)?>-voltar]', function(e){
        voltar_materiais($("#id_material_categoria").html());
    }); 

    $(document).on('click','a[rel=btn-cat-material-<?=str_replace('_','-',$prefix)?>-selecionar]', function(e){
        e.preventDefault();
        const id = $(this).attr('id');
        if (id) {
            carrega_materiais(id);
        }
    });   
    
    $(document).on('click','a[rel=btn-material-<?=str_replace('_','-',$prefix)?>-selecionar]', function(e){
        e.preventDefault();
        const id = $(this).attr('id');
        if (id) {
            carrega_detalhes_material(id);
        }
    });

    $(document).on('click','a[rel=btn-material-<?=str_replace('_','-',$prefix)?>-utilizar]', function(e){
        e.preventDefault();
        const id = $(this).attr('data-id');
        if (id) {
            $.ajax({
                url:'/fracionar-materiais',
                type:'post',
                dataType:'json',
                data:{'id_materiais':id},
                success:function(data){
                    console.log(data);
                    gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                    
                    if (data.success) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
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

    $(document).on('click','a[rel=btn-material-<?=str_replace('_','-',$prefix)?>-fracionar]', function(e){
        const id = $(this).attr('data-id');
        const qtd = $('input[id=qtde_fracionar]').val();
        const peso_material = $(this).attr('data-peso').replace('.','').replace(',','.');
        const peso = (parseFloat(peso_material) / parseInt(qtd)).toFixed(2).replace('.',',');

        if (!qtd || qtd < 1) {
            gerarAlerta('É necessário informar a quantidade de fracionamento para a unidade!', 'Aviso', 'danger');
            $('input[id=qtde_fracionar]').focus()
            return false;
        }

        if (id) {
            $('h5[id=modalFracionamento][class=modal-title]').html('Material: '+$(this).attr('data-descricao'));

            let campos = '';
            for (let index = 1; index <= qtd; index++) {
                campos+= '<div class="row mb-2">'+
                            '<label for="fracao'+index+'" class="col-4 col-form-label">Fração '+index+'</label>'+
                            '<div class="col">'+
                                '<input type="text" class="form-control valor-decimal" maxlength="10" id="fracao'+index+'" name="fracao[]" value="'+peso+'">'+
                            '</div>'+
                        '</div>';
            }

            $('div[id=fracao-campos]').html(campos);
            $(".valor-decimal").maskMoney({thousands:'.', decimal:',', allowZero: true, allowEmpty: true});
            $('form[name=form-fracionamento] input[name=id_materiais]').val(id);
            $('form[name=form-fracionamento] input[name=peso]').val(peso_material);

            $('div[id=modal-fracionamento]').modal('show');
        }


    });

    $(document).on('click','a[rel=btn-fracao-salvar]', function(e){
        e.preventDefault();

        const id_materiais = $('form[name=form-fracionamento] input[name=id_materiais]').val();
        const peso_material = $('form[name=form-fracionamento] input[name=peso]').val();
        let arr_quantidades = [];
        let total_peso_fracao = 0;
        $('form[name=form-fracionamento]').find('input[name^=fracao]').each(function(i, v){
            if($(this).val() && $(this).val()!='') {
                arr_quantidades.push($(this).val());
                total_peso_fracao+= parseFloat($(this).val().replace(',','.'));
            }
        });

        if(arr_quantidades.length < 1) {
            gerarAlerta('Deve ser informado os valores das frações','Aviso', 'danger');
            return false;
        }

        if(parseFloat(peso_material) < parseFloat(total_peso_fracao)) {
            gerarAlerta('Os valores das frações passa o pesso do material ('+peso_material+')','Aviso', 'danger');
            return false;
        }

        
        if (id_materiais && arr_quantidades.length >= 1) {
            
            $.ajax({
                url:'/fracionar-materiais',
                type:'post',
                dataType:'json',
                data:{'id_materiais':id_materiais, arr_quantidades: arr_quantidades},
                success:function(data){
                    
                    gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                    
                    if (data.success) {
                        $('div[id=modal-fracionamento]').modal('hide');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
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

});
</script>