<?php
require_once('header.php');

$titulo = 'Fracionar Material';
$prefix='auto-fracionamento';
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
                <div class="card text-center" >
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary" id="div_titulo_auto_fracionamento">Passe o Material no Leitor</h6>
                    </div>
                    <div class="card-body">
                        
                        <h5 class="card-title">Código de Barras</h5> 
                        <p class="card-text"><input type="text" class="form-control" aria-label="Large" id="cod_barras_material" aria-describedby="inputGroup-sizing-sm"></p>
                        
                    </div>
                    <div class="card-body" id="div_dt_vencimento">
                        
                        <h5 class="card-title">Data de Vencimento</h5> 
                        <p class="card-text"><input type="text" class="form-control mask-data requered" maxlength="10" placeholder="Ex.: 99/99/9999" cursor-center class="form-control" aria-label="Large" id="dt_vencimento_material" aria-describedby="inputGroup-sizing-sm"></p>

                        <a class="btn btn-success" rel="btn-fracionar-material">Fracionar</a>
                        
                    </div>
                    <div class="card-body" id="div_material">

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
    function validaData (valor) {
        // Verifica se a entrada é uma string
        if (typeof valor !== 'string') {
            return false
        }

        // Verifica formado da data
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(valor)) {
            return false
        }

        // Divide a data para o objeto "data"
        const partesData = valor.split('/')
        const data = { 
            dia: partesData[0], 
            mes: partesData[1], 
            ano: partesData[2] 
        }
        
        // Converte strings em número
        const dia = parseInt(data.dia)
        const mes = parseInt(data.mes)
        const ano = parseInt(data.ano)
        
        // Dias de cada mês, incluindo ajuste para ano bissexto
        const diasNoMes = [ 0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ]

        // Atualiza os dias do mês de fevereiro para ano bisexto
        if (ano % 400 === 0 || ano % 4 === 0 && ano % 100 !== 0) {
            diasNoMes[2] = 29
        }
        
        // Regras de validação:
        // Mês deve estar entre 1 e 12, e o dia deve ser maior que zero
        if (mes < 1 || mes > 12 || dia < 1) {
            return false
        }
        // Valida número de dias do mês
        else if (dia > diasNoMes[mes]) {
            return false
        }
        
        // Passou nas validações
        return true
    }

    $(document).ready(function(){
        $("#div_dt_vencimento").hide();
        
        $( "#cod_barras_material" ).on( "blur", function() {
            //e.preventDefault();
            const cod_barras = $('input[id=cod_barras_material]').val();
            id_material = '';
            if (cod_barras) {
                $.ajax({
                    url:'/buscar-material-cod-barras',
                    type:'post',
                    dataType:'json',
                    data:{'cod_barras':cod_barras},
                    success:function(data){
                        console.log(data);
                        
                        if (data.id_materiais) {
                            gerarAlerta('Informe a Data de Vencimento do Material', 'Alerta', 'alert');
                            $("#div_dt_vencimento").show();
                            $("#div_material").html("<input type='hidden' id='id_material' value='"+data.id_materiais+"'/>");
                           
                        }else{
                            $("#div_dt_vencimento").hide();
                            gerarAlerta('Material não encontrado!', 'Alerta', 'danger');
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
                
            }else{
                gerarAlerta('O código de barras deve ser informado','Aviso', 'danger');
                return false;
            }
        }); 

        $(document).on('click','a[rel=btn-fracionar-material]', function(e){
            if (($("#dt_vencimento_material").val() != '') && (validaData($("#dt_vencimento_material").val()))){
                window.open('https://ootech.com.br/fracionar-imprimir-material?id='+$("#id_material").val()+'&dt_venc='+$("#dt_vencimento_material").val());
                setTimeout(() => {
                    window.location.reload();
                }, 4000);
            }else{
                gerarAlerta('Data de Vencimento Inválida!', 'Alerta', 'danger');
            }
        });
    });
</script>