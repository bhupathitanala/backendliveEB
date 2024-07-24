<?php include 'partials/main.php'; ?>

<?php
$id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));
if (strlen(trim($id))>0) {
    $sql = "UPDATE `coupons` SET `status`=0 WHERE `ID`=$id";
    if ($conn->query($sql)) {
        echo "<script>alert('Coupon Deleted');location.href='coupons'</script>";
    }
}
?>
<head>
    <?php
    $title = "Coupons";
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
                    $sub_title = "Coupons";
                    $page_title = "List of Coupons";
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

                                    <table id="scroll-horizontal-datatable" class="table table-striped  nowrap table-striped w-100" >
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Code</th>
                                                <th>Type</th>
                                                <th>Value</th>
                                                <th>Minimum Purchase</th>
                                                <th>Brands</th>
                                                <th>Categories</th>
                                                <th>Sub Categories</th>
                                                <th>Products</th>
                                                <th>Validity</th>
                                                <th>Usage Limit</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                                $query = mysqli_query($conn, "SELECT * FROM `coupons`  ORDER BY `id` DESC");
                                                $sno = 0;
                                                while ($fetch = mysqli_fetch_array($query)) {
                                                    $BrandIds = join("', '", explode(",", $fetch['brands']));
                                                    $Brands = $conn->query("SELECT GROUP_CONCAT(' ', `brandName`) as `brands` FROM `brands` WHERE ID IN ('$BrandIds')");
                                                    $Brands = $Brands->fetch_assoc();

                                                    $CategoryIds = join("', '", explode(",", $fetch['categories']));
                                                    $Categories = $conn->query("SELECT GROUP_CONCAT(' ', `categoryName`) as `categories` FROM `categories` WHERE ID IN ('$CategoryIds')");
                                                    $Categories = $Categories->fetch_assoc();


                                                    $SubCategoryIds = join("', '", explode(",", $fetch['subCategories']));
                                                    $SubCategories = $conn->query("SELECT GROUP_CONCAT(' ', `subCategoryName`) as `subCategories` FROM `subcategory` WHERE ID IN ('$SubCategoryIds')");
                                                    $SubCategories = $SubCategories->fetch_assoc();

                                                    $ProductIds = join("', '", explode(",", $fetch['products']));
                                                    $Products = $conn->query("SELECT GROUP_CONCAT(' ', `product_id`) as `products` FROM `productsnew` WHERE ID IN ('$ProductIds')");
                                                    $Products = $Products->fetch_assoc();

                                                    ?>
                                                    <tr>
                                                        <td><?php echo ++$sno; ?></td>
                                                        <td><?php echo $fetch['code']; ?></td>
                                                        <td><?php echo $fetch['type']; ?></td>
                                                        <td><?php echo $fetch['amount']; ?></td>
                                                        <td><?php echo $fetch['purchaseAmount']==0 ? '' : $fetch['purchaseAmount']; ?></td>
                                                        <td><?php echo $Brands['brands']; ?></td>
                                                        <td><?php echo $Categories['categories']; ?></td>
                                                        <td><?php echo $SubCategories['subCategories']; ?></td>
                                                        <td><?php echo $Products['products']; ?></td>
                                                        <td><?php echo $fetch['validity']; ?></td>
                                                        <td><?php echo $fetch['useageLimit']; ?></td>
                                                        <td><?php echo $fetch['description']; ?></td>
                                                        <td>
                                                            <?php if($fetch['status']==1) { ?>
                                                                <label class="badge bg-success">Active</label>
                                                            <?php }else{ ?>
                                                                <label class="badge bg-danger">Deactive</label>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <!-- <a class="btn btn-sm btn-outline-primary rounded-pill" href="addbrand?id=<?php echo $fetch['ID'] ?>">Edit</a> -->
                                                            <a class="btn btn-sm btn-outline-danger rounded-pill" href="?id=<?php echo $fetch['ID'] ?>" onclick="return confirm('Are you sure to delete this Coupon')">Deactivate</a>
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