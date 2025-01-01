<?php
require_once('header.php');
?>
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-6 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">                        
                        <div class="col">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-2">Esqueceu sua senha?</h1>
                                    <p class="mb-4">Será enviado um e-mail para resetar sua senha.</p>
                                </div>
                                <form class="">
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user"
                                            id="exampleInputEmail" aria-describedby="emailHelp"
                                            placeholder="Enter Email Address...">
                                    </div>
                                    <a href="/" class="btn btn-primary btn-user btn-block">
                                        Enviar
                                    </a>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="/">Acessar o sistema.</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
<?php
require_once('footer.php');
?>