<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Add New Color";
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
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Retrieve form data
            $color_name = $_POST['color_name'];
            $order_by = $_POST['order_by'];
            
            // Prepare INSERT query
            $query = "INSERT INTO colors (color_name, status, order_by) VALUES ('$color_name', 1, '$order_by')";

            // Execute INSERT query
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('New Color added successfully');</script>";
                // Redirect to the same page
                echo "<script>window.location.href='addcolor';</script>";
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
        ?>

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <?php
                    $sub_title = "Colors";
                    $page_title = "Add New Color";
                    include 'partials/page-title.php'; ?>

                    <div class="row justify-content-center"> <!-- Center the row -->
                        <div class="col-md-6"> <!-- Adjust the column width -->
                            <div class="card">
                                <div class="card-body">
                                <form class="needs-validation" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" novalidate>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="validationCustom01">Color Name</label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="color_name" class="form-control" id="validationCustom01" required>
                                                <div class="invalid-feedback">
                                                    Please enter Color.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="validationCustom02">Display Order Number</label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" name="order_by" class="form-control" id="validationCustom02" required>
                                                <div class="invalid-feedback">
                                                    Please enter Order number.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button class="btn btn-primary" type="submit">Add</button>
                                        </div>
                                    </form>

                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->

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

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

    <!-- CKEditor 5 -->
    <script src="assets/js/vendor/ckeditor.js"></script>

    <script>
        ClassicEditor
            .create( document.querySelector( '#editor' ), {
                // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
            } )
            .then( editor => {
                window.editor = editor;
            } )
            .catch( err => {
                console.error( err.stack );
            } );
    </script>

</body>

</html>
