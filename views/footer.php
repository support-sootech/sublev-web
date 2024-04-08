    <!-- Bootstrap core JavaScript-->
    <script src="<?=site_url()?>/layout/vendor/jquery/jquery.min.js"></script>
    <script src="<?=site_url()?>/layout/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?=site_url()?>/layout/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?=site_url()?>/layout/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="<?=site_url()?>/layout/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <?php if(returnPage()=='/dashboard'):?>
        <script src="<?=site_url()?>/layout/js/demo/chart-area-demo.js"></script>
        <script src="<?=site_url()?>/layout/js/demo/chart-pie-demo.js"></script>
    <?php endif;?>

</body>

</html>