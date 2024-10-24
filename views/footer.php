    <div class="modal fade" id="modal-usuarios-config" tabindex="-1" role="dialog" aria-labelledby="modalUsuariosConfig" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUsuariosConfig">Configurações</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="form-usuarios-config" class="formValidate">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
                            <div class="form-group">
                            <label for="usuarios_config_senha">Senha</label>
                                <input type="password" class="form-control requered" id="usuarios_config_senha" name="usuarios_config_senha" placeholder="Senha">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fa fa-xmark"></i> Fechar
                    </button>
                    <a class="btn btn-primary" href="#" rel="btn-usuarios-config-salvar">
                        <i class="fa fa-save"></i> Salvar
                    </a>
                </div>
            </div>
        </div>
    </div>    
    
    <!-- Bootstrap core JavaScript-->
    <script src="<?=site_url()?>/layout/vendor/jquery/jquery.min.js"></script>
    <script src="<?=site_url()?>/layout/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?=site_url()?>/layout/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?=site_url()?>/layout/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="<?=site_url()?>/layout/vendor/chart.js/Chart.min.js"></script>

    <!-- EXTRAS -->
    <script src="<?=site_url()?>/layout/js/jquery.form.js?v=<?=date('YmdHis')?>"></script>
    <script src="<?=site_url()?>/layout/js/jquery.maskMoney.js?v=<?=date('YmdHis')?>"></script>
    <script src="<?=site_url()?>/layout/js/jquery.preloaders.js?v=<?=date('YmdHis')?>"></script>
    <script src="<?=site_url()?>/layout/js/pnotify.min.js?v=<?=date('YmdHis')?>"></script>
    <script src="<?=site_url()?>/layout/js/jgrowl.min.js?v=<?=date('YmdHis')?>"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.4.8/jquery.jgrowl.min.js"></script>
    <!-- <script src="<?=site_url()?>/layout/js/sweetalert2.min.js?v=<?=date('YmdHis')?>"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
    <script type='text/javascript' src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>

    <script src="<?=site_url()?>/layout/js/scripts.js?v=<?=date('YmdHis')?>"></script>

    <!-- Page level custom scripts -->
    <?php if(returnPage()=='/dashboard'):?>
        <script src="<?=site_url()?>/layout/js/demo/chart-area-demo.js"></script>
        <script src="<?=site_url()?>/layout/js/demo/chart-pie-demo.js"></script>
    <?php endif;?>

    <script>
    $(document).ready(function(){
        $(document).on('click', 'a[rel=btn-modal-usuarios-perfil-config]', function(e){
            e.preventDefault();
            $('div#modal-usuarios-config').modal('show');
        });

        $(document).on('click','a[rel=btn-usuarios-config-salvar]', function(e){
            e.preventDefault();
            
            if(!isFormValidate($('form[name=form-usuarios-config]'))) {
                gerarAlerta('<?=messagesDefault('fields_requered')?>', 'Aviso', 'danger');
                return false;
            }

            $('form[name=form-usuarios-config]').ajaxForm({
                data:{},
                success : function(data) {
                    console.log('data', data);
                    gerarAlerta(data.msg, (data.success?'Sucesso':'Erro'), data.type);
                    if (data.success) {
                        $('div#modal-usuarios-config').modal('hide');
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
                url: '/usuarios-configuracoes-save',
                resetForm:false
            }).submit();
        });
    });
    </script>

</body>

</html>