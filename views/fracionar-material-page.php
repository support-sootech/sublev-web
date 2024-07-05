<?php
require_once('header.php');
$titulo = 'Fracionar Material';
$arr_permissoes = array();
if (isset($_SESSION['usuario']['endpoints'][returnPage()])) {
    $arr_permissoes = $_SESSION['usuario']['endpoints'][returnPage()];
}
$prefix='';
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
                    <h1 class="h3 mb-2 text-gray-800"><?=$titulo?> </h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="m-0 font-weight-bold text-primary"><?=$titulo?></h6>
                                </div>
                                <div class="col text-right">
                                    <?php if(in_array('CADASTRAR', $arr_permissoes)):?>
                                        <a href="#" rel="btn-<?=str_replace('_','-',$prefix)?>-novo" class="btn btn-primary btn-sm">
                                            <i class="fa fa-plus"></i> Novo
                                        </a>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-<?=str_replace('_','-',$prefix)?>" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th >ID</th>
                                            <th >Descrição</th>
                                            <th >Fabricante</th>
                                            <th >Fornecedor</th>
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

<?php
require_once('footer.php');
?>
