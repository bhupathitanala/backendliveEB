<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Add New Product";
    include 'partials/title-meta.php'; ?>

    <!-- Quill css -->
    <link href="assets/css/vendor/quill.core.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/quill.snow.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/quill.bubble.css" rel="stylesheet" type="text/css" />

    <?php include 'partials/head-css.php'; ?>

    <style>
        .form-check {
            display: inline-block;
        }
        .form-check, .form-check label, .form-check input {
            cursor: pointer;
        }

        .checkbox-dropdown .btn-group {
            width: 100%;
        }
        .checkbox-dropdown .btn-group .btn {
            color: var(--tz-body-color);
            background-color: var(--tz-secondary-bg);
            background-clip: padding-box;
            border: var(--tz-border-width) solid var(--tz-border-color);
            text-align: left;
        }

        .checkbox-dropdown .btn-group .dropdown-toggle::after {
            float: right;
            margin-top: .6em;
        }

        .checkbox-dropdown .btn-group .dropdown-menu {
            width: 100%;
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
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
            $type = $conn->real_escape_string(filter_input(INPUT_POST, 'type'));
            
            if(isset($_FILES["attachment"]) && $_FILES["attachment"]["error"] == UPLOAD_ERR_OK) {
                // Get file details
                $fileName = $_FILES["attachment"]["name"];
                $fileTmpName = $_FILES["attachment"]["tmp_name"];
                
                // Read CSV file
                $csvData = file_get_contents($fileTmpName);

                // Parse CSV data
                $rows = explode("\n", $csvData);
                $x=1;
                try {
                    // Start a transaction
                    $conn->begin_transaction();

                    foreach($rows as $row) {
                        if ($x>1) {
                            if (!empty($row)) {
                                $rowData = str_getcsv($row);
                                if (!strlen(trim($rowData[4]))) { $rowData[4] = 0; }
                                if (!strlen(trim($rowData[5]))) { $rowData[5] = 0; }
                                $rowData[2] =  str_replace("\x83", "", $conn->real_escape_string($rowData[2]));
                                $rowData[0] =  $conn->real_escape_string($rowData[0]);
                                $sql = "INSERT INTO `ratingsnew` SET `rating`='$rowData[1]', `product_id`='$rowData[3]', `customer_id`='$_SESSION[userId]',`customer_name`='$rowData[0]', `review`='$rowData[2]', `likes`='$rowData[4]', `dislikes`='$rowData[5]', `date`='$rowData[6]', `added_by`='Admin'";
                                // exit;
                                try {
                                    $conn->query($sql);
                                } catch (Exception $e) {
                                    // echo $e;
                                    
                                }
                            }

                        }
                        $x++;
                    }

                    $conn->commit();

                    // Database insertion complete
                    echo "<script>alert('CSV data inserted into database successfully.')</script>";
                }catch (Exception $e) {
                    // Rollback the transaction if an exception occurs
                    $conn->rollback();
                    
                    // Display error message
                    echo "<script>alert('Error inserting data:')</script>";
                }
            } else {
                echo "<script>alert('Error uploading file.')</script>";
            }
        }
        ?>

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <?php
                    $sub_title = "Reviews";
                    $page_title = "Upload Reviews";
                    include 'partials/page-title.php'; ?>

                    <div class="row justify-content-center">
                        <div class="col-8">
                            <div class="card">
                                <div class="card-body">
                                    <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label" for="attachment">CSV File</label>
                                                    <input type="file" class="form-control" id="attachment" name="attachment" required accept=".csv">
                                                    <div class="invalid-feedback">
                                                        Please choose a Product Main Image.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary" type="submit" name="submit">Submit</button>
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

    <!-- Quill Editor js -->
    <script src="assets/js/vendor/quill.min.js"></script>

    <!-- Quill Demo js -->
    <script src="assets/js/pages/quilljs.init.js"></script>

    <!-- CKEditor 5 -->
    <!-- <script src="assets/js/vendor/ckeditor.js"></script> -->

    <script>

    </script>

</body>

</html>