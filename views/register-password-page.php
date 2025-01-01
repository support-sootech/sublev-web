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
                                    <h1 class="h4 text-gray-900 mb-4">Cadastro de Senha</h1>
                                </div>
                                <form name="form-register-password" class="">
                                    <input type="hidden" name="hash" value="<?=$usuario['hash']?>" />
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="senha" name="senha" aria-describedby="senha" placeholder="Senha">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="senha_confirm" name="senha_confirm" placeholder="Confirmação de senha">
                                    </div>
                                    <a href="#" rel="btn-save" class="btn btn-primary btn-user btn-block">
                                        Salvar
                                    </a>
                                </form>
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
    if($('input[name=senha]').val() == '' || $('input[name=senha_confirm]').val() == ''){
        gerarAlerta('Os campos senha e confirmação de senha devem ser preenchidos!', 'Aviso', 'danger');
        return false;
    }

    if($('input[name=senha]').val() != $('input[name=senha_confirm]').val()){
        gerarAlerta('Os campos senha e confirmação de senha estão diferentes!', 'Aviso', 'danger');
        return false;
    }

    preloaderStart();

    $('form[name=form-register-password]').ajaxForm({
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
        url: '/register-password',
        resetForm:false
    }).submit();
}

$(document).ready(function(){
    $('a[rel=btn-save]').on('click', function(e){
		e.preventDefault();
        register_password();
	});
});
</script>