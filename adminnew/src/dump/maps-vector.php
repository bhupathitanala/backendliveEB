<?php include 'partials/main.php'; ?>

    <head>
        <?php
    $title = "Vector Maps";
    include 'partials/title-meta.php'; ?>
        
        <!-- Vector Maps css -->
        <link href="assets/css/vendor/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />

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
                    $sub_title = "Maps";
                    $page_title = "Vector Maps";
    include 'partials/page-title.php'; ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">World Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="world-map-markers" style="height: 360px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                        </div>
                        <!-- end row-->

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">USA Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="usa-vectormap" style="height: 300px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">India Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="india-vectormap"  style="height: 300px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                        </div>
                        <!-- end row-->

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">Australia Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="australia-vectormap" style="height: 300px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">Chicago Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="chicago-vectormap"  style="height: 300px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                        </div>
                        <!-- end row-->

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">UK Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="uk-vectormap" style="height: 300px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">Canada Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="canada-vectormap"  style="height: 300px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                        </div>
                        <!-- end row-->

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">Europe Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="europe-vectormap" style="height: 300px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">France Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="france-vectormap"  style="height: 300px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                        </div>
                        <!-- end row-->

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">Spain Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="spain-vectormap" style="height: 300px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="header-title mb-0">Spain Vector Map</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="spain2-vectormap"  style="height: 300px"></div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                        </div>
                        <!-- end row-->
                        
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

        <!-- Vector Maps js -->
        <script src="assets/js/vendor/jquery-jvectormap-1.2.2.min.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-world-mill-en.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-us-merc-en.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-in-mill-en.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-au-mill-en.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-us-il-chicago-mill-en.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-uk-mill-en.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-ca-lcc-en.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-europe-mill-en.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-fr-merc-en.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-es-merc.js"></script>
        <script src="assets/js/vendor/jquery-jvectormap-es-mill.js"></script>

        <!-- Vector Maps Demo js -->
        <script src="assets/js/pages/vector-maps.init.js"></script>

        <!-- App js -->
        <script src="assets/js/app.min.js"></script>

    </body>
</html>
