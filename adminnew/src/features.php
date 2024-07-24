<?php include 'partials/main.php'; ?>

<?php

$id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));

if (strlen(trim($id))>0) {
    $data = explode("-", $id);
    $id = $data[0];
    $img = $data[1];

    $sql = "DELETE FROM `features` WHERE id=$id";
    if ($conn->query($sql)) {
        unlink("uploads/images/features/".$img);

        echo "<script>alert('Feature Deleted');location.href='features'</script>";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // fields
    $title = mysqli_real_escape_string($conn, $_POST['title']);

    // Insert data into the database
    $sql = "INSERT INTO `features` SET `title`='$title', `status`='1'";
    $result = $conn->query($sql);
    $insert_id = $conn->insert_id;

    if ($result) {
        // upload image
        $file = $_FILES['icon'];
        if ($file['name'] != '') {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = time().".".$ext;
            $upload_path = "uploads/images/features/";
            $move = move_uploaded_file($file['tmp_name'], $upload_path.$filename);

            if ($move) {
                // update icon name in the table
                $update = $conn->query("UPDATE `features` SET `icon`='$filename' WHERE `id`='$insert_id'");
            }
        }

        echo "<script>alert('New Feature Added Successfully.');location.href='';</script>";
    }else{
        echo "<script>alert('Failed, Please try again later.');</script>";
    }
}
?>

<head>
    <?php
    $title = "Features";
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
        .card-body {
            position: relative;
        }
        .add-feature {
            position: absolute;
            right: 10px;
            top: -15px;
        }
        .text-right {
            text-align: right;
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
                    $sub_title = "Features";
                    $page_title = "Features";
                    include 'partials/page-title.php'; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <button type="button" class="btn btn-primary btn-sm add-feature" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add Feature</button>
                                    <table id="fixed-header-datatable"
                                        class="table table-striped dt-responsive nowrap table-striped w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Feature</th>
                                                <th>Icon / Image</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                                $query = mysqli_query($conn, "SELECT * FROM `features` ORDER BY `id` ASC");
                                                $sno = 0;
                                                while ($fetch = mysqli_fetch_array($query)) { ?>
                                                    <tr>
                                                        <td><?php echo ++$sno; ?></td>
                                                        <td><?php echo $fetch['title']; ?></td>
                                                        <td><img src='uploads/images/features/<?php echo $fetch['icon']; ?>' height="64" /></td>
                                                        <td>
                                                            <?php if ($fetch['status']==1) { ?>
                                                                <span class='badge bg-success'>Active</span>
                                                            <?php } else{ ?>
                                                                <span class='badge bg-danger'>Inactive</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <!-- <a class="btn btn-sm btn-outline-primary rounded-pill" href="features?id=<?php //echo $fetch['id'] ?>">Edit</a> -->
                                                            <a class="btn btn-sm btn-outline-danger rounded-pill" href="?id=<?php echo $fetch['id'].'-'.$fetch['icon'] ?>" onclick="return confirm('Are you sure to delete this Feature')">Delete</a>
                                                        </td>
                                                    </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>

                                    <!-- Modal -->
                                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="staticBackdropLabel">Add Feature</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div> <!-- end modal header -->
                                                <div class="modal-body">
                                                    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                                        <div class="mb-3">
                                                            <label for="title" class="form-label">Feature Name</label>
                                                            <input class="form-control" type="text" id="title" name="title" required>
                                                            <div class="invalid-feedback">
                                                                Please provide a valid Feature Name.
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="icon" class="form-label">Icon</label>
                                                            <input class="form-control" type="file" id="icon" name="icon" required accept=".png,.jpg,.jpeg,.gif,.webp">
                                                            <div class="invalid-feedback">
                                                                Please choose a Feature Icon.
                                                            </div>
                                                        </div>

                                                        <div class="mb-3 text-right">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Submit</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div> <!-- end modal content-->
                                        </div> <!-- end modal dialog-->
                                    </div> <!-- end modal-->
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