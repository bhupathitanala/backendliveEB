<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Sparkline";
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
                    $sub_title = "Charts";
                    $page_title = "Sparkline";
                    include 'partials/page-title.php'; ?>

                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Line Charts</h4>
                                    <div class="mt-4">
                                        <div id="sparkline1" data-colors="#3bc0c3,#4489e4"></div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col -->

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Bar Chart</h4>
                                    <div class="mt-4">
                                        <div id="sparkline2" data-colors="#3bc0c3" class="text-center"></div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col -->

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Pie Chart</h4>
                                    <div class="mt-4">
                                        <div id="sparkline3" data-colors="#3bc0c3,#4489e4,#d03f3f,#716cb0" class="text-center"></div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col -->

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Custom Line Chart</h4>
                                    <div class="mt-4">
                                        <div id="sparkline4" data-colors="#3bc0c3,#716cb0" class="text-center"></div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col -->

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Mouse Speed Chart</h4>
                                    <div class="mt-4">
                                        <div id="sparkline5" data-colors="#716cb0" class="text-center"></div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col -->

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Composite bar Chart</h4>
                                    <div class="text-center mt-4">
                                        <div id="sparkline6" data-colors="#f2f2f7,#3bc0c3" class="text-center"></div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col -->

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Discrete Chart</h4>
                                    <div class="text-center mt-4">
                                        <div id="sparkline7" data-colors="#33b0e0" class="text-center"></div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col -->

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Bullet Chart</h4>
                                    <div class="text-center mt-4" style="min-height: 164px;">
                                        <div id="sparkline8" data-colors="#f24f7c,#4489e4" class="text-center"></div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col -->

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Box Plot Chart</h4>
                                    <div class="text-center mt-4" style="min-height: 164px;">
                                        <div id="sparkline9" data-colors="#3bc0c3,#1a2942,#d1d7d973" class="text-center"></div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col -->

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Tristate Charts</h4>
                                    <div class="text-center mt-4" style="min-height: 164px;">
                                        <div id="sparkline10" data-colors="#d1d7d973,#1a2942,#3bc0c3" class="text-center">
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card -->
                        </div> <!-- end col -->

                    </div>
                    <!-- end row -->

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

    <!-- Sparkline charts -->
    <script src="assets/js/vendor/jquery.sparkline.min.js"></script>

    <!-- init js -->
    <script src="assets/js/pages/sparkline.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>