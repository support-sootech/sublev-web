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
                                <form name="form-reset-password" class="">
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user"
                                            id="email" name="email" aria-describedby="email"
                                            placeholder="Informe seu e-mail">
                                    </div>
                                    <a href="#" rel="btn-send" class="btn btn-primary btn-user btn-block">
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
<script>

function register_password() {
    if($('input[name=email]').val() == ''){
        gerarAlerta('O campo e-mail deve ser preenchido!', 'Aviso', 'danger');
        return false;
    }

    preloaderStart();

    $('form[name=form-reset-password]').ajaxForm({
        data:{},
        success : function(data) {
            gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
            if (data.success) {
                setTimeout(() => {
                    window.location.href = data.page
                }, 2000);
            }
        },
        error : function(e) {
            preloaderStop();
            gerarAlerta(e.responseJSON.msg, 'Erro', 'danger');
        },
        complete:function(){
            preloaderStop();
        },
        beforeSend:function(){
            preloaderStart();
        },
        type:'post',
        dataType:'json',
        url: '/reset-password',
        resetForm:false
    }).submit();
}

$(document).ready(function(){
    $('a[rel=btn-send]').on('click', function(e){
		e.preventDefault();
        register_password();
	});
});
</script>