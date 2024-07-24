<?php include 'partials/main.php'; ?>

<?php
$id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));
if (strlen(trim($id))>0) {
    $sql = "SELECT * FROM `blogs` WHERE `id`=$id";
    $res = $conn->query($sql);
    $Fetch = $res->fetch_assoc();
    $old_files = $Fetch['img_urls'];
    $old_files = ($old_files!='') ? json_decode($old_files, true) : [];
    for ($i=0; $i <count($old_files) ; $i++) { 
        unlink("uploads/images/blogs/".$old_files[$i]);
    }
    
    $sql = "DELETE FROM `blogs` WHERE `id`=$id";
    if ($conn->query($sql)) {
        echo "<script>alert('Blog Deleted');location.href='blogs'</script>";
    }
}
?>
<head>
    <?php
    $title = "Blogs";
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
                    $sub_title = "Blogs";
                    $page_title = "List of Blogs";
                    include 'partials/page-title.php'; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="scroll-horizontal-datatable" class="table table-striped  nowrap table-striped w-100" >
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Title</th>
                                                <th>Heading</th>
                                                <th>Sub Heading</th>
                                                <!-- <th>Description</th> -->
                                                <th>Images</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                                $query = mysqli_query($conn, "SELECT * FROM `blogs`  ORDER BY `id` ASC");
                                                $sno = 0;
                                                while ($fetch = mysqli_fetch_array($query)) { ?>
                                                    <tr>
                                                        <td><?php echo ++$sno; ?></td>
                                                        <td><?php echo $fetch['title']; ?></td>
                                                        <td><?php echo $fetch['heading']; ?></td>
                                                        <td><?php echo $fetch['subHeading']; ?></td>
                                                        <!-- <td><?php //echo $fetch['description']; ?></td> -->
                                                        <td>
                                                            <?php
                                                            $images = $fetch['img_urls'] ? json_decode($fetch['img_urls'], true) : [];
                                                            for ($i=0; $i < count($images); $i++) { ?>
                                                                <img src="uploads/images/blogs/<?php echo $images[$i] ?>" style="max-height: 50px;max-width: 50px;">
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-sm btn-outline-primary rounded-pill" href="addblog?id=<?php echo $fetch['id'] ?>">Edit</a>
                                                            <a class="btn btn-sm btn-outline-danger rounded-pill" href="?id=<?php echo $fetch['id'] ?>" onclick="return confirm('Are you sure to delete this Brand')">Delete</a>
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