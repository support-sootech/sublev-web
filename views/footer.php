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
    <script src="<?=site_url()?>/layout/js/scripts.js?v=<?=date('YmdHis')?>"></script>

    <!-- Page level custom scripts -->
    <?php if(returnPage()=='/dashboard'):?>
        <script src="<?=site_url()?>/layout/js/demo/chart-area-demo.js"></script>
        <script src="<?=site_url()?>/layout/js/demo/chart-pie-demo.js"></script>
    <?php endif;?>

</body>

</html>