<?php
require_once('header.php');
$titulo = 'Home';
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
                    <h1 class="h3 mb-2 text-gray-800">Bem vindo(a), <?=$_SESSION['usuario']['nm_pessoa']?>!</h1>
                    

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                <?php
                                if (isset($_GET['debug'])==1) {
                                    verMatriz($_SESSION['usuario']);
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <div class="col text-center" id="div_btn_voltar">
                                            <h1 class="h3 mb-2 text-gray-800">Atalhos Rápidos</h1>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col" align="right"> 
                                            <div class="card text-bg-primary mb-3" style="max-width: 18rem;">
                                                <a href="/controle-materiais" class="btn btn-primary" >
                                                    <div class="card-body" style="vertical-align: middle;">
                                                            <h5 class="card-title" align="center"><br>Entrada de Materiais</h5>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col"> 
                                            <div class="card text-bg-primary mb-3" style="max-width: 18rem;">
                                                <a href="/materiais-fracionados-baixa" class="btn btn-primary" >
                                                    <div class="card-body">
                                                        <h5 class="card-title" align="center"><br>Baixar Mat. Fracionados</h5>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <div class="col text-center" id="div_btn_voltar">
                                            <h1 class="h3 mb-2 text-gray-800">Materiais Fracionados</h1>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col" align="right">
                                            <div class="card text-bg-danger mb-3" style="max-width: 18rem;">
                                                <a id="btn_vencem_hoje" rel="btn_card_fracionados" class="btn btn-danger" data-toggle="modal" data-target="#modalMatFracionados">
                                                    
                                                    <div class="card-body">
                                                        <h5 class="card-title" align="center"><i class="fas fa-fire-alt"></i></h5>
                                                        <p class="card-text" align="center" id="texto_vencem_hoje"></p>
                                                    </div>
                                                </a>
                                                
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="card text-bg-warning mb-3" style="max-width: 18rem;">
                                                <a id="btn_vencem_amanha" rel="btn_card_fracionados" class="btn btn-warning" data-toggle="modal" data-target="#modalMatFracionados">
                                                    <div class="card-body">
                                                        <h5 class="card-title" align="center"><i class="fas fa-exclamation-triangle"></i></h5>
                                                        <p class="card-text" align="center" id="texto_vencem_amanha"></p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col" align="right">    
                                            <div class="card text-bg-info mb-3" style="max-width: 18rem;">
                                                <a id="btn_vencem_semana" rel="btn_card_fracionados" class="btn btn-info" data-toggle="modal" data-target="#modalMatFracionados">
                                                    <div class="card-body">
                                                        <h5 class="card-title" align="center"><i class="far fa-calendar"></i></h5>
                                                        <p class="card-text" align="center" id="texto_vencem_semana"></p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col">  
                                            <div class="card text-bg-success mb-3" style="max-width: 18rem;">
                                                <a id="btn_vencem_mais_1_semana" rel="btn_card_fracionados" class="btn btn-success" data-toggle="modal" data-target="#modalMatFracionados">
                                                    <div class="card-body">
                                                        <h5 class="card-title" align="center"><i class="far fa-thumbs-up"></i></h5>
                                                        <p class="card-text" align="center"  id="texto_vencem_mais_1_semana"></p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal Structure -->
                                    <div class="modal fade bd-example-modal-lg" id="modalMatFracionados" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="titulo-modalMatFracionados"></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body" id="conteudo-modalMatFracionados">
                                                    
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Modal Structure -->
                                    
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

    

<?php
require_once('footer.php');
?>
<script>

function carrega_materiais_vencimento(id){
    $.ajax({
        url:'/materiais-vencimento-json',
        type:'post',
        dataType:'json',
        data:{
            'id_acao':id
        },
        success:function(data){
            console.log('carrega_materiais_vencimento',data);
            
            if (data) {
                if (id == 'btn_vencem_hoje'){
                    $("#titulo-modalMatFracionados").html('Materiais que Vencem Hoje');
                }
                if (id == 'btn_vencem_amanha'){
                    $("#titulo-modalMatFracionados").html('Materiais que Vencem Amanhã');
                }
                if (id == 'btn_vencem_semana'){
                    $("#titulo-modalMatFracionados").html('Materiais que Vencem em 1 semana');
                }
                if (id == 'btn_vencem_mais_1_semana'){
                    $("#titulo-modalMatFracionados").html('Materiais que Vencem Após 1 semana');
                }

                if (data.data) {
               
                    let body = '';
                    body += '<table class="table" id="material-detalhes"><thead><tr>';
                    body += '<th scope="col"><b>N° Etiqueta</b></th>';
                    body += '<th scope="col"><b>Material</b></th>';
                    body += '<th scope="col"><b>Marca</b></th>';
                    body += '<th scope="col"><b>Data de Validade </b></th>';
                    body += '<th scope="col"><b>Data de Fracionamento </b></th>';
                    body += '</tr></thead><tbody>';
                    
                    $.each(data.data, function(i, v){

                        body += '<tr>';
                        body += '<th scope="row">'+v.id_etiquetas+'</th>';
                        body += '<td>'+v.descricao+'</td>';
                        body += '<td>'+v.marca+'</td>';
                        body += '<td>'+v.dt_vencimento+'</td>';
                        body += '<td>'+v.dt_fracionamento+'</td>';
                        body += '</tr>';
                        
                    });

                    body += '</tbody></table>';
                    $("#conteudo-modalMatFracionados").html(body);
                }
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

    $.ajax({
        url:'/quant-materiais-vencimento-json',
        type:'post',
        dataType:'json',
        data:{
            'id_acao':'texto_vencem_hoje'
        },
        success:function(data){
            console.log('carrega_materiais_vencimento',data);
            
            if (data) {
                
                $.each(data.data, function(i, v){
                    $("#texto_vencem_hoje").html(v.quantidade+'<br>Vencem Hoje');
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

    $.ajax({
        url:'/quant-materiais-vencimento-json',
        type:'post',
        dataType:'json',
        data:{
            'id_acao':'texto_vencem_amanha'
        },
        success:function(data){
            console.log('carrega_materiais_vencimento',data);
            
            if (data) {
                
                $.each(data.data, function(i, v){
                    $("#texto_vencem_amanha").html(v.quantidade+'<br>Vencem Amanhã');
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

    $.ajax({
        url:'/quant-materiais-vencimento-json',
        type:'post',
        dataType:'json',
        data:{
            'id_acao':'texto_vencem_semana'
        },
        success:function(data){
            console.log('carrega_materiais_vencimento',data);
            
            if (data) {
                
                $.each(data.data, function(i, v){
                    $("#texto_vencem_semana").html(v.quantidade+'<br>Vencem em 1 semana');
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

    $.ajax({
        url:'/quant-materiais-vencimento-json',
        type:'post',
        dataType:'json',
        data:{
            'id_acao':'texto_vencem_mais_1_semana'
        },
        success:function(data){
            console.log('carrega_materiais_vencimento',data);
            
            if (data) {
                
                $.each(data.data, function(i, v){
                    $("#texto_vencem_mais_1_semana").html(v.quantidade+'<br>Vencem Após 7 dias');
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

    $(document).on('click','a[rel=btn_card_fracionados]', function(e){
        e.preventDefault();
        const id = $(this).attr('id');
        if (id) {
            $("#conteudo-modalMatFracionados").html("");
            carrega_materiais_vencimento(id);
        }
    });


});
</script>