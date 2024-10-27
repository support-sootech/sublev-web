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
                                    <h1 class="h4 text-gray-900 mb-4">Acesso ao Sistema</h1>
                                </div>
                                <form name="form-login" class="">
                                    <div class="form-group">
                                        <input type="cpf" class="form-control form-control-user mask-cpf-cnpj" autoComplete="off" id="cpf" name="cpf" aria-describedby="cpf" placeholder="Digite seu CPF">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user" id="senha" name="senha" placeholder="Senha">
                                    </div>
                                    <a href="#" rel="btn-login" class="btn btn-primary btn-user btn-block">
                                        Acessar
                                    </a>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="/forgot-password">Esqueceu sua senha?</a>
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

function login() {
    if($('input[name=cpf]').val() == '' || $('input[name=senha]').val() == ''){
        gerarAlerta('É necessário informar CPF e senha.', 'Aviso', 'danger');
        return false;
    }

    preloaderStart();

    $('form[name=form-login]').ajaxForm({
        data:{},
        success : function(data) {
            gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
            if (data.success) {
                window.location.href = data.page
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
        url: '/login',
        resetForm:false
    }).submit();
}

$(document).ready(function(){
    $('a[rel=btn-login]').on('click', function(e){
		e.preventDefault();
        login();
	});

    $(document).keypress(function (e) {
        if (e.which == 13) {
            login();
            return false;
        }
    });

});
</script>