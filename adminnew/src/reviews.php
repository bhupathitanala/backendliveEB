<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Categories";
    include 'partials/title-meta.php'; ?>

    <!-- Datatables css -->
    <link href="assets/css/vendor/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/fixedColumns.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/fixedHeader.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />

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
                    $sub_title = "Reviews";
                    $page_title = "Product Reviews";
                    include 'partials/page-title.php'; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <!-- <div class="card-header">
                                    <h4 class="header-title">Effortless Product Navigation</h4>
                                    <p class="text-muted mb-0">
                                        Effortlessly navigate through diverse product sections such as Baby Care, Beauty, and Food with our intuitive category system.
                                    </p>
                                </div> -->
                                <div class="card-body">

                                    <table id="scroll-horizontal-datatable"
                                        class="table table-striped nowrap table-striped w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Product Id</th>
                                                <th>Product Name</th>
                                                <th>Customer Name</th>
                                                <th>Rating</th>
                                                <th>Review</th>
                                                <th>Likes</th>
                                                <th>Dislikes</th>
                                                <th>Date</th>
                                                <!-- <th>Action</th> -->
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $x=1;
                                            $sql = "SELECT `product_id`,`customer_name`,`rating`,`review`,`likes`,`dislikes`,`date`,(SELECT `title` FROM `productsnew` WHERE product_id=a.product_id) as `title` FROM `ratingsnew` a WHERE status=1";
                                            $result = $conn->query($sql);
                                            while ($reviews = $result->fetch_assoc()) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $x++ ?></td>
                                                    <td><?php echo $reviews['product_id'] ?></td>
                                                    <td><?php echo $reviews['title'] ?></td>
                                                    <td><?php echo $reviews['customer_name'] ?></td>
                                                    <td><?php echo $reviews['rating'] ?></td>
                                                    <td><?php echo $reviews['review'] ?></td>
                                                    <td><?php echo $reviews['likes'] ?></td>
                                                    <td><?php echo $reviews['dislikes'] ?></td>
                                                    <td><?php echo $reviews['date'] ?></td>
                                                    <!-- <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill">Edit</button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill">Delete</button>
                                                    </td> -->
                                                </tr>
                                            <?php  } ?>
                                        </tbody>
                                    </table>
                                </div> <!-- end card body-->
                            </div> <!-- end card -->
                        </div><!-- end col-->
                    </div> <!-- end row-->
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

    <!-- Datatables js -->
    <script src="assets/js/vendor/jquery.dataTables.min.js"></script>
    <script src="assets/js/vendor/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/vendor/dataTables.responsive.min.js"></script>
    <script src="assets/js/vendor/responsive.bootstrap5.min.js"></script>
    <script src="assets/js/vendor/fixedColumns.bootstrap5.min.js"></script>
    <script src="assets/js/vendor/dataTables.fixedHeader.min.js"></script>
    <script src="assets/js/vendor/dataTables.buttons.min.js"></script>
    <script src="assets/js/vendor/buttons.bootstrap5.min.js"></script>
    <script src="assets/js/vendor/buttons.php5.min.js"></script>
    <script src="assets/js/vendor/buttons.flash.min.js"></script>
    <script src="assets/js/vendor/buttons.print.min.js"></script>
    <script src="assets/js/vendor/dataTables.keyTable.min.js"></script>
    <script src="assets/js/vendor/dataTables.select.min.js"></script>

    <!-- Datatable Demo Aapp js -->
    <script src="assets/js/pages/datatable.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>