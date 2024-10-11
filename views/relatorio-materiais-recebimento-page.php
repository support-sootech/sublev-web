<?php
require_once('header.php');
$titulo = 'Relatório de Recebimento de Materiais';
$prefix = 'material';
$arr_permissoes = array();
if (isset($_SESSION['usuario']['endpoints'][returnPage()])) {
    $arr_permissoes = $_SESSION['usuario']['endpoints'][returnPage()];
}
?>
<style>
    table tr td {
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
                    <h1 class="h3 mb-2 text-gray-800">Controle de <?=$titulo?> </h1>

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
                            <form action="/relatorio-materiais-recebimento" method="POST" target="_blank" name="form-relatorio-materiais-recebimentos-filtros">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                        <div class="form-group">
                                            <label for="fil_dt_ini">Data ínicio</label>
                                            <input type="text" class="form-control mask-data" id="fil_dt_ini" name="fil_dt_ini" value="<?=date('d/m/Y')?>" placeholder="99/99/9999">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
                                        <div class="form-group">
                                        <label for="fil_dt_fim">Data final</label>
                                            <input type="text" class="form-control mask-data" id="fil_dt_fim" name="fil_dt_fim" value="<?=date('d/m/Y')?>" placeholder="99/99/9999">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3" style="padding-top:30px;">
                                        <a href="#" rel="btn-filtrar" class="btn btn-primary"><i class="fas fa-search"></i> Filtrar</a>
                                        <a href="#" rel="btn-exportar" class="btn btn-dark"><i class="fas fa-file-pdf"></i> PDF</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

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
                                <table class="table table-bordered" id="table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th >Data / Hora</th>
                                            <th >Material</th>
                                            <th >Fornecedor</th>
                                            <th >Validade</th>
                                            <th >Qtde</th>
                                            <th >Temperatura</th>
                                            <th >SIF</th>
                                            <th >Lote</th>
                                            <th >Nº Nota</th>
                                            <th >Condições Emb.</th>
                                            <th >Responsável</th>
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

<?php
require_once('footer.php');
?>
<script>
function carrega_lista(){
    const fil_dt_ini = $('input[name=fil_dt_ini]').val();
    const fil_dt_fim = $('input[name=fil_dt_fim]').val();

    $('#table').DataTable({
        "ajax": {
            "url": '/relatorio-materiais-recebimento',
            "type": "post",
            "data":{
                tipo:'json',
                fil_dt_ini:fil_dt_ini,
                fil_dt_fim:fil_dt_fim
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
                                    return data.dh_cadastro;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.descricao;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.nm_fornecedor;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.dt_vencimento;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.quantidade;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.temperatura;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.sif;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.lote;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.nro_nota;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.ds_embalagem_condicoes;
                                }
                    },
                    { "data": function ( data, type, row ) {
                                    return data.nm_responsavel;
                                }
                    }
                ]

    });
}


$(document).ready(function(){

    formFieldsRequered();

    carrega_lista();

    $(document).on('click', 'a[rel=btn-filtrar]', function(e){
        e.preventDefault();
        carrega_lista();
    });

    $(document).on('click', 'a[rel=btn-exportar]', function(e){
        e.preventDefault();
        const form = $('form[name=form-relatorio-materiais-recebimentos-filtros]');
        form.append('<input type="hidden" name="tipo" value="pdf" />').submit();
    });

});
</script>