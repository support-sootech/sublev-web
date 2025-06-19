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
                                        <div class="col text-left" id="div_btn_voltar">
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col" align="right">
                                            <div class="card text-bg-danger mb-3" style="max-width: 18rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title" align="center"><i class="fas fa-fire-alt"></i></h5>
                                                    <p class="card-text" align="center">5<br>Vencem Hoje</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="card text-bg-warning mb-3" style="max-width: 18rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title" align="center"><i class="fas fa-exclamation-triangle"></i></h5>
                                                    <p class="card-text" align="center">3<br>Vencem Amanhã</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col" align="right">    
                                            <div class="card text-bg-info mb-3" style="max-width: 18rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title" align="center"><i class="far fa-calendar"></i></h5>
                                                    <p class="card-text" align="center">9<br>Vencem Em 1 semana</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">  
                                            <div class="card text-bg-success mb-3" style="max-width: 18rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title" align="center"><i class="far fa-thumbs-up"></i></h5>
                                                    <p class="card-text" align="center">15<br>Vencem Após 7 dias</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col" align="right"> 
                                            <div class="card text-bg-primary mb-3" style="max-width: 18rem; width: 288px; height: 126px;">
                                                <div class="card-body" style="vertical-align: middle;">
                                                        <h5 class="card-title" align="center"><br>Entrada de Materiais</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col"> 
                                            <div class="card text-bg-primary mb-3" style="max-width: 18rem; width: 288px; height: 126px;">
                                                <div class="card-body">
                                                    <h5 class="card-title" align="center"><br>Baixar Materiais Fracionados</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    /*
                                    $fg = array_search('ROOT', array_column($_SESSION['usuario']['perfil'], 'ds_perfil'));
                                    if (isset($fg)) {
                                        verMatriz($_SESSION['usuario']);
                                    }
                                    */
                                    ?>
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
