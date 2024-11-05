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
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="row">
                            <div class="col text-left" id="div_btn_voltar">
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col text-center">
                                <h6 class="m-0 font-weight-bold text-primary" id="div_titulo_auto_fracionamento">Passe o Material no Leitor</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" >
                        <div class="row" id="material" class="col-2">
                            <input type="text" class="form-control" aria-label="Large" id="cod_barras_material" aria-describedby="inputGroup-sizing-sm">
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
