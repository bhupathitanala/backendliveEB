<?php include 'partials/main.php'; ?>
<?php
$page_title = "Add New Blog";
$title = "Add New Blog";
$id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));
$img_urls_str = "";
if (strlen(trim($id))>0) {
    $title = "Edit Blog";
    $page_title = "Edit Blog";

    $query = $conn->query("SELECT * FROM `blogs` WHERE id='$id'");
    $BlogDetails = $query->fetch_assoc();

    $blogtitle = $BlogDetails['title'];
    $heading = $BlogDetails['heading'];
    $subHeading = $BlogDetails['subHeading'];
    $description = $BlogDetails['description'];
    $img_urls_str = $BlogDetails['img_urls'];
}

?>
<head>
    <?php
    include 'partials/title-meta.php'; ?>

    <!-- bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Summernote CSS -->
    <link href="assets/vendor/summernote-0.8.18/summernote-bs4.css" rel="stylesheet">

    <!-- CSS overriding -->
    <style>
        a {
            text-decoration: none !important;
        }
    </style>
    <!-- CSS overriding -->

    <?php include 'partials/head-css.php'; ?>

    <style>
        .display-none {
            display: none;
        }

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
            height: 200px;
        }

        fieldset {
            border: 2px dotted hotpink;
            padding: 10px 20px;
        }
        legend {
            background-color: #e5ab5f;
            border: 1px solid #efd1aa;
            border-radius: 10px;
            padding: 5px 10px;
            text-transform: uppercase;
            float: none;
            width: auto;
            font-size: inherit;
            margin: inherit;
        }
    </style>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include 'partials/menu.php'; ?>

        <?php
        if (isset($_POST['submit'])) {
            // Sanitize and escape input data
            $blogtitle = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
            $heading = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'heading', FILTER_SANITIZE_STRING));
            $subHeading = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'subHeading', FILTER_SANITIZE_STRING));
            $description = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'description'));

            $img_urls = [];

            // Check if images are uploaded
            if (!empty($_FILES['images']['name'][0])) {
                $img_urls = upload_images($_FILES['images'], "uploads/images/blogs/", $img_urls_str);
                if (!$img_urls) {
                    echo "<script>alert('Image upload failed.');location.href='';</script>";
                    exit;
                }
                // Convert array of image URLs to a comma-separated string
                $img_urls_str = json_encode($img_urls);
            } else if (!empty(trim($id))) {
                $img_urls_str = $img_urls_str;
            } else {
                $img_urls_str = null;
            }

            // Start MySQL transaction
            $conn->begin_transaction();

            try {
                if (!empty(trim($id))) {
                    // Update the database
                    $sql = "UPDATE `blogs` SET `title`='$blogtitle', `heading`='$heading', `subHeading`='$subHeading', `description`='$description', `img_urls`='$img_urls_str' WHERE id='$id'";
                    $Alert = "Blog successfully updated";
                    $location = "blogs";
                } else {
                    // Insert data into the database
                    $sql = "INSERT INTO `blogs` (`title`, `heading`, `subHeading`, `description`, `img_urls`) VALUES ('$blogtitle', '$heading', '$subHeading', '$description', '$img_urls_str')";
                    $Alert = "Blog successfully added";
                    $location = "blogs";
                }

                // Execute the SQL query
                if ($conn->query($sql)) {
                    // Commit transaction
                    $conn->commit();
                    echo "<script>alert('$Alert');location.href='$location';</script>";
                } else {
                    // Rollback transaction on failure
                    $conn->rollback();
                    echo "<script>alert('Failed, Please try again later.');location.href='$location';</script>";
                }
            } catch (Exception $e) {
                // Rollback transaction on exception
                $conn->rollback();
                echo "<script>alert('An error occurred. Please try again later.');location.href='$location';</script>";
            }
        }

        /**
         * Uploads multiple images to the specified directory.
         * 
         * @param array $files The uploaded file information
         * @param string $path The directory path to upload the files
         * @return array The new file names or an empty array on failure
         */
        function upload_images($files, $path,$old_files) {
            $uploaded_files = [];
            $old_files = ($old_files!='') ? json_decode($old_files, true) : [];
            for ($i=0; $i <count($old_files) ; $i++) { 
                unlink($path.$old_files[$i]);
            }
            
            for ($i = 0; $i < count($files['name']); $i++) {
                $file_ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $filenewname = time() . "_$i." . $file_ext;
                $tmp_name = $files['tmp_name'][$i];
                $upload = move_uploaded_file($tmp_name, $path . $filenewname);
                if ($upload) {
                    $uploaded_files[] = $filenewname;
                } else {
                    // If any upload fails, return an empty array indicating failure
                    return [];
                }
            }
            return $uploaded_files;
        }
        ?>


<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <?php
            $sub_title = "Blogs";
            include 'partials/page-title.php'; ?>

            <div class="row justify-content-center">
                <div class="col-9">
                    <div class="card">
                        <div class="card-body">
                            <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label>Title</label><b>*</b>
                                    <input type="text" name="title" class="form-control" required value="<?php echo $blogtitle ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Heading</label><b>*</b>
                                    <input type="text" name="heading" class="form-control" required value="<?php echo $heading ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Sub Heading</label><b>*</b>
                                    <input type="text" name="subHeading" class="form-control" required value="<?php echo $subHeading ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Description</label><b>*</b>
                                    <textarea class="form-control" id="summernote" name="description" required><?php echo $description ?></textarea>
                                </div>


                                <div class="mb-3">
                                    <label class="form-label" for="images">Images</label>
                                    <input type="file" multiple class="form-control" name="images[]" accept=".png,.jpg,.jpeg,.gif.webp,.avif,.svg">
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

<!-- popper js -->
<script src="assets/js/popper.min.js"></script>

<!-- bootstrap js -->
<script src="assets/js/bootstrap.min.js"></script>

<!-- Summernote js -->
<script src="assets/vendor/summernote-0.8.18/summernote-bs4.js"></script>

<script>
    $(document).ready(function() {
        // summernote
        $('#summernote').summernote({ height: 200 });
    });
</script>

</body>
</html>