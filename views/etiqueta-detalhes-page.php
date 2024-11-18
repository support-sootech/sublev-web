<?php
require_once('header.php');

$titulo = 'Informações da Etiqueta';
$prefix='etiqueta-detalhes';
$arr_permissoes = array();
if (isset($_SESSION['usuario']['endpoints'][returnPage()])) {
    $arr_permissoes = $_SESSION['usuario']['endpoints'][returnPage()];
}
?>
<script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js" ></script>	
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
                <div class="card text-center" >
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary" id="div_titulo_auto_fracionamento">Informações da Etiqueta</h6>
                    </div>
                    <div class="card-body" id="btn_leitor_etiqueta">
                        <table align="center">
                            <tr>
                                <td><a class="btn btn-success" rel="btn-ler-material">Ler QR Code</a></td>
                                <td><a class="btn btn-danger" rel="btn-fechar">Fechar Câmera</a></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="card-body" id="div_scan_material">
                        <video id="preview"></video>
                    </div>

                    <div class="card-body" id="div_detalhes_etiqueta">
                        
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

<?php
require_once('footer.php');
?>
<script>
    $(document).ready(function(){
        var scanner;
        $("#div_scan_material").hide();
        $("#div_detalhes_etiqueta").hide();
        $(document).on('click','a[rel=btn-fechar]', function(e){
            document.getElementById('preview').style.width = "1px";
            document.getElementById('preview').style.height = "1px";
            $("#div_scan_material").hide();
            scanner.stop();
            
        });

        $(document).on('click','a[rel=btn-ler-material]', function(e){
            $("#div_detalhes_etiqueta").hide();
            $("#div_scan_material").show();
            document.getElementById('preview').style.width = "320px";
            document.getElementById('preview').style.height = "240px";
            scanner = new Instascan.Scanner(
                {
                    video: document.getElementById('preview'),
                    mirror: false
                }
            );
            scanner.addListener('scan', function(content) {
                scanner.stop();
                //alert('Escaneou o conteudo: ' + content);
                $.ajax({
                        url:'/detalhes-etiqueta-json',
                        type:'post',
                        dataType:'json',
                        data:{
                            'id_etiqueta':content
                        },
                        success:function(data){
                            $("#div_scan_material").hide();
                            $("#div_detalhes_etiqueta").show();
                            if (data) {
                                
                                let body = '';
                                body += '<div class="container" id="etiqueta-detalhes"><div class="row col-sm text-center">';

                                if (data) {
                                    body += '<div class="col-sm"><b>Etiqueta</b></div>';
                                    body += '<div  class="col-sm"><b>Descrição</b></div>';
                                    body += '<div  class="col-sm"><b>Material Fracionado</b></div>';
                                    body += '<div  class="col-sm"><b>Lote do Material</b></div>';
                                    body += '<div  class="col-sm"><b>Quantidade Fracionada</b></div>';
                                    body += '<div  class="col-sm"><b>Data do Fracionamento</b></div>';
                                    body += '<div  class="col-sm"><b>Data de Vencimento</b></div>';
                                    body += '</div><div class="row col-sm text-center">';
                                    
                                    body += '<div class="col-sm">'+data.id_etiquetas+'</div>';
                                    body += '<div class="col-sm">'+data.descricao+'</div>';
                                    body += '<div class="col-sm">'+data.desc_material+'</div>';
                                    body += '<div class="col-sm">'+data.lote+'</div>';
                                    body += '<div class="col-sm">'+data.qtd_fracionada+'</div>';
                                    body += '<div class="col-sm">'+data.dt_fracionamento+'</div>';
                                    body += '<div class="col-sm">'+data.dt_vencimento+'</div>';
                                    
                                }
                                body += '</div>';
                                
                                $("#div_detalhes_etiqueta").html(body);
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
            });
            Instascan.Camera.getCameras().then(cameras => 
            {
                console.log(cameras);
                if(cameras.length > 0){
                    scanner.start(cameras[1]);
                } else {
                    console.error("Não existe câmera no dispositivo!");
                }
            });
        });
    });
</script>
