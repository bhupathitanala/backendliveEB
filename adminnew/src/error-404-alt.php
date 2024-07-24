<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Error 404 Alt";
    include 'partials/title-meta.php'; ?>

    <?php include 'partials/head-css.php'; ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'partials/menu.php'; ?>

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <?php
                    $sub_title = "Pages";
                    $page_title = "404 Alt Error";
                    include 'partials/page-title.php'; ?>

                    <div class="row justify-content-center items">
                        <div class="col-12">
                            <div class="d-flex flex-column h-100">
                                <div class="row justify-content-center">
                                    <div class="col-lg-4">
                                        <div class="text-center">
                                            <h1 class="text-error mb-4">404</h1>
                                            <h2 class="text-uppercase text-danger mt-3">Page Not Found</h2>
                                            <p class="text-muted mt-3">It's looking like you may have taken a wrong turn. Don't worry... it
                                                happens to the best of us. Here's a
                                                little tip that might help you get back on track.</p>

                                            <a class="btn btn-soft-danger mt-3" href="index.php"><i class="ri-home-4-line me-1"></i> Back to Home</a>
                                        </div> <!-- end /.text-center-->
                                    </div> <!-- end col-->
                                </div>
                            </div>
                        </div> <!-- end col -->
                    </div>

                </div> <!-- container -->

            </div> <!-- content -->

            <?php include 'partials/footer.php'; ?>

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include 'partials/right-sidebar.php'; ?>

    <?php include 'partials/footer-scripts.php'; ?>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>