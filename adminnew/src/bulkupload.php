<?php include 'partials/main.php'; ?>


<?php

// Function to update or insert student record using mysqli_query format
function updateOrInsertStudent($conn, $data) {
    // print_r($data);
    // exit();
    // Sanitize inputs (example: using mysqli_real_escape_string)
    $product_id = mysqli_real_escape_string($conn, $data[0]);
    $title = mysqli_real_escape_string($conn, $data[1]);
    $tags = mysqli_real_escape_string($conn, $data[2]);

    // Check if the roll number already exists
    $checkQuery = "SELECT product_id FROM productsnew WHERE product_id='$product_id'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($checkResult) {
        $currentDateTime = date("Y-m-d H:i:s");
        if (mysqli_num_rows($checkResult) > 0) {
            // echo "existed";
            // echo "<br>";
            // product_id exists, update the record
            $updateQuery = "UPDATE productsnew SET 
                tags = '$tags'
                WHERE product_id = '$product_id'";

            $updateResult = mysqli_query($conn, $updateQuery);
            if (!$updateResult) {
                echo "Error updating record: " . mysqli_error($conn);
            }else{
                echo "updated";
                echo "<br>";
            }
        } else {
            // product_id does not exist, insert a new record
             

        }

        mysqli_free_result($checkResult);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Check if form is submitted
if (isset($_POST["submit"])) {
    // Check if file upload was successful
    if ($_FILES["file"]["error"] == 0) {
        $filename = $_FILES["file"]["tmp_name"];
        $file = fopen($filename, "r");

        // Skip the header line
        fgetcsv($file);

        // Read and process each line of the CSV file
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            // Ensure data array has exactly 15 elements (adjust if necessary)
            
            // if (count($data) == 15) {
            //     // Update or insert student record
            //     updateOrInsertStudent($conn, $data);
            // }
            // Update or insert student record
            updateOrInsertStudent($conn, $data);
        }

        fclose($file);
        echo "CSV file successfully imported!";
    } else {
        echo "Error uploading file: " . $_FILES["file"]["error"];
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
                            <h2>Upload CSV File to Update or Insert Students</h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    Select CSV file to upload:
    <input type="file" name="file" id="file" accept=".csv" required>
    <br><br>
    <input type="submit" value="Upload CSV" name="submit">
</form>
                               
                                
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