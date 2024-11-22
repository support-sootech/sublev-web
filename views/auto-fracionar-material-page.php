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

                        <div class="row">
                            
                            <?php if($arr_computadores && isset($arr_computadores) && count($arr_computadores) > 0):?>
                                
                                <?php if(count($arr_computadores) > 1):?>
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                        <h5 class="card-title">Impressoras</h5> 
                                        <select class="form-select" id="arr_computadores" name="arr_computadores">
                                            <?php foreach ($arr_computadores as $key => $value):?>
                                                <option value="<?=$value['chave']?>"><?=$value['descricao']?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                <?php else:?>
                                    <div class="col-lg-4 col-xl-4 col-xxl-4">
                                        <input type="hidden" class="form-control" id="arr_computadores" name="arr_computadores" value="<?=$arr_computadores[0]['chave']?>">
                                    </div>                                        
                                <?php endif;?>
                                
                            <?php else:?>
                                <div class="col-lg-4 col-xl-4 col-xxl-4"></div>
                            <?php endif;?>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                <h5 class="card-title">Código de Barras</h5> 
                                <input type="text" class="form-control text-center" aria-label="Large" id="cod_barras_material" aria-describedby="inputGroup-sizing-sm">
                            </div>
                            <div class="col-lg-4 col-xl-4 col-xxl-4"></div>
                        </div>
                        <br>
                        <hr>
                        <div class="row" id="div_material"></div>
                        
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

    function card_materiais(material){
        let html = '<div class="col-xl-3 col-md-6 mb-4">';
                html+= '<a href="#" data-material="'+material.id_materiais+'" data-vencimento="'+material.dt_vencimento+'" rel="btn-fracionar-material" style="text-decoration: none;">';
                    html+= '<div class="card border-left-'+material.color_dt_vencimento+' shadow h-100 py-2">';
                        html+= '<div class="card-body">';
                            html+= '<div class="row no-gutters align-items-center">';
                                html+= '<div class="col mr-2">';
                                    html+= '<div class="h6 font-weight-bold text-uppercase mb-1">'+material.descricao+'</div>';
                                    html+= '<div class="text-xs font-weight-bold text-uppercase mb-1">Vencimento: '+material.dt_vencimento+'</div>';
                                    if (material.marca != '') {
                                        html+= '<div class="text-xs font-weight-bold text-uppercase mb-1">'+material.marca+'</div>';
                                    }
                                html+= '</div>';
                            html+= '</div>';
                        html+= '</div>';
                    html+= '</div>';
                html+= '</a>';
            html+= '</div>';
        return html;
    }

    $(document).ready(function(){        
        
        $( "#cod_barras_material" ).on( "blur", function() {
            //e.preventDefault();
            const cod_barras = $('input[id=cod_barras_material]').val();
            id_material = '';
            if (cod_barras) {
                $("#div_material").html('');
                $.ajax({
                    url:'/buscar-material-cod-barras',
                    type:'post',
                    dataType:'json',
                    data:{'cod_barras':cod_barras},
                    success:function(data){
                        if (data.length > 0) {
                            let arr_materiais = '';
                            $.each(data, function (i, v) { 
                                arr_materiais+= card_materiais(v);
                            });

                            $("#div_material").html(arr_materiais);
                        } else {
                            gerarAlerta('Nenhum material não encontrado!', 'Alerta', 'danger');
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

        function imprimir(etiqueta, computador) {
            $.ajax({
                url:'http://localhost:3000/imprimir',
                type:'post',
                dataType:'json',
                processData: false,
                contentType: 'application/json',
                data: JSON.stringify({
                    computador:computador,
                    etiqueta:etiqueta
                }),
                success:function(data){
                    console.log('IMPRIMIR', data);
                    if (data.success) {
                        gerarAlerta('Etiqueta impressa com sucesso', 'Aviso', 'success');
                    } else {
                        gerarAlerta(data.msg, 'Aviso', data.type);
                    }
                },
                beforeSend: () => {
                    preloaderStart();
                },
                complete:()=>{
                    preloaderStop();
                },
                error:(a,b,c)=>{
                    console.log('ERROR A', a);
                    console.log('ERROR B', b);
                    console.log('ERROR C', c);
                }
            });
        }

        $(document).on('click','a[rel=btn-fracionar-material]', function(e){
            e.preventDefault();
            const id_materiais = $(this).attr('data-material');
            const dt_vencimento = $(this).attr('data-vencimento');
            const computador = $('#arr_computadores').val();
            console.log('computador', computador);
            const etiqueta = 'https://ootech.com.br/fracionar-imprimir-material?id='+id_materiais+'&dt_venc='+dt_vencimento+'';

            if (computador) {
                console.log('imprimir');
                imprimir(etiqueta, computador);
            } else {
                window.open(etiqueta);
                setTimeout(() => {
                    window.location.reload();
                }, 4000);
            }
        });
    });
</script>