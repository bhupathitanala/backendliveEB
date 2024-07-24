<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Products";
    include 'partials/title-meta.php'; ?>

    <!-- Datatables css -->
    <link href="assets/css/vendor/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/fixedColumns.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/fixedHeader.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />

    <?php include 'partials/head-css.php'; ?>

    <style>
        .display-none {
            display: none;
        }
    </style>
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
                    $sub_title = "All Products";
                    $page_title = "Products";
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
                                                <!-- <th>Category</th>
                                                <th>Sub Category</th> -->
                                                <th>Product ID</th>
                                                <th>Product Title</th>
                                                <th>Thumbnail</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Product Type</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            if(in_array($_SESSION['userType'], array('VENDOR', 'SUBADMIN'))){
                                                $query = mysqli_query($conn, "SELECT `ID`,`product_id`,`product_type`,`title`,`main_img`,`quantity`,`price`,`status` FROM `productsnew` a WHERE `is_deleted`='0' and vendor_id='".$_SESSION['vendorId']."' ORDER BY `id` DESC");                                                

                                            }else{
                                                $query = mysqli_query($conn, "SELECT `ID`,`product_id`,`product_type`,`title`,`main_img`,`quantity`,`price`,`status` FROM `productsnew` a WHERE `is_deleted`='0' ORDER BY `id` DESC");                                                
                                            }
                                                $sno = 0;
                                                while ($fetch = mysqli_fetch_array($query)) { ?>
                                                    <tr>
                                                        <td><?php echo ++$sno; ?></td>
                                                        <!-- <td><?php //echo $fetch['categoryName']; ?></td>
                                                        <td><?php //echo $fetch['subCategoryName']; ?></td> -->
                                                        <td><?php echo $fetch['product_id']; ?></td>
                                                        <td><span data-bs-toggle="tooltip"  data-bs-title="<?php echo $fetch['title']; ?>"><?php echo (strlen($fetch['title']) > 20) ? substr($fetch['title'], 0, 20)."..." : $fetch['title']; ?></span><div class="display-none"><?php echo $fetch['title']; ?></div></td>
                                                        <td><img src='./uploads/images/product_images/<?php echo $fetch['main_img']; ?>' height="64" /></td>
                                                        <td><?php echo $fetch['quantity']; ?></td>
                                                        <td><i class="bi bi-currency-rupee"></i><?php echo $fetch['price']." /-"; ?></td>
                                                        <td>
                                                            <?php
                                                                if ($fetch['product_type'] == 'simple') $textColor = "text-primary"; else $textColor = "text-success";
                                                                echo "<label class='".$textColor."'>".ucfirst($fetch['product_type'])."</label>";
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($fetch['status'] == 1) { ?>
                                                            <span class='badge bg-success'>Active</span>
                                                            <?php } else { ?>
                                                            <span class='badge bg-danger'>In Active</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <a href="product?v=<?php echo $fetch['ID']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">Edit</a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill">Delete</button>
                                                        </td>
                                                    </tr>
                                            <?php } ?>
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