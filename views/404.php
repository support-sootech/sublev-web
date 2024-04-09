<?php
require_once('header.php');
?>
<div id="wrapper">
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <div class="container-fluid">
                <!-- 404 Error Text -->
                <div class="text-center">
                    <div class="error mx-auto" data-text="404">404</div>
                    <p class="lead text-gray-800 mb-5">Página não localizada.</p>
                    <a href="javascript:history.go(-1)">&larr; Voltar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once('footer.php');
?>