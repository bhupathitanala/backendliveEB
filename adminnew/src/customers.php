<?php include 'partials/main.php'; ?>


<?php
$id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));

if (strlen(trim($id))>0) {
    $query2 = $conn->query("UPDATE `admin_users` SET `status`=0 WHERE ID='$id'");
    if ($query) {
        echo "<script>alert('Vendor Deleted');location.href=''</script>";
    }else{
        echo "<script>alert('Internal Server Error');location.href=''</script>";
    }
}
?>
<head>
    <?php
    $title = "Users";
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
                    $sub_title = "Users";
                    $page_title = "List of Users";
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
                                        class="table table-striped  nowrap table-striped w-100" >
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                                $sql = "SELECT * FROM `customer`"; // 
                                                $query = $conn->query($sql);
                                                $sno = 0;
                                                while ($fetch = $query->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?php echo ++$sno; ?></td>
                                                        <td><?php echo $fetch['fullName']; ?></td>
                                                        <td><?php echo $fetch['email']; ?></td>
                                                        <td><?php echo $fetch['phoneNumber']; ?></td>
                                                        <td>
                                                            <?php if ($fetch['status']==1) { ?>
                                                                <span class='badge bg-info'>Active</span>
                                                            <?php } else{ ?>
                                                                <span class='badge bg-danger'>Suspended</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td  style="white-space: nowrap;">
                                                            <a href="editcustomer?id=<?php echo $fetch['ID'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Edit</a>
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