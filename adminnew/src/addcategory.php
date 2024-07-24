<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Add New Category";
    include 'partials/title-meta.php'; ?>

    <!-- Quill css -->
    <link href="assets/css/vendor/quill.core.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/quill.snow.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/quill.bubble.css" rel="stylesheet" type="text/css" />

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

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <?php

        $id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));
        $category = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'category'));
        $img = @$_FILES['image']['name'];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // Start a transaction
            $conn->begin_transaction();

            if (strlen(trim($id))>0) {
                $sql = "UPDATE `categories` SET `categoryName`='$category', `status`='1' WHERE id='$id'";
                $Res = $conn->query($sql);
                $lastInsertedId = $id;
                $Alert = "Main Category successfully updated.";
                $location = "categories";
                if (strlen(trim($_FILES['image']['name']))) {
                    $img = upload_image($_FILES['image'], "main_categories/", $lastInsertedId, $conn);
                }else{
                    $img = true;
                }
            }else{
                $sql = "INSERT INTO `categories` SET `categoryName`='$category', `status`='1'";
                $Res = $conn->query($sql);
                $lastInsertedId = $conn->insert_id;
                $Alert = "Main Category successfully added.";
                $location = '';
                $img = upload_image($_FILES['image'], "main_categories/", $lastInsertedId, $conn);
            }

            if ($img) {
                $sql = "UPDATE `categories` set img_url='$img' WHERE id='$lastInsertedId'";
                $Res = $conn->query($sql);
                $conn->commit();
                echo "<script>alert('".$Alert."');location.href='".$location."';</script>";
            }else{
                $conn->rollback();
                echo "<script>alert('Image upload failed. Data not saved.');location.href='';</script>";
            }
        }

        function upload_image($file, $destination, $id, $conn) {
            $fullpath = "uploads/images/" . $destination;
            $filename = $file['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $newFilename = $id . "." . $ext;
            $isupload = move_uploaded_file($file['tmp_name'], $fullpath . $newFilename);
            if ($isupload) {
                return $newFilename; // Return the filename if upload successful
            } else {
                return false; // Return false if upload failed
            }
        }
        
        $page_title = "Add New Category";
        if (strlen(trim($id))>0) {
            $sql = "SELECT * FROM `categories` WHERE ID=$id";
            $result = $conn->query($sql);
            $CatDetails = $result->fetch_assoc();
            $category = $CatDetails['categoryName'];
            $img = $CatDetails['img_url'];
            $page_title = "Edit Category";
        }



        ?>

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <?php
                    $sub_title = "Main Category";
                    include 'partials/page-title.php'; ?>

                    <div class="row justify-content-center">
                        <div class="col-9">
                            <div class="card">
                                <div class="card-body">
                                    <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label>Category</label>
                                            <input type="text" name="category" class="form-control" required value="<?php echo $category ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="image">Image</label>
                                            <input type="file" class="form-control" name="image" <?php if(strlen(trim($id))==0){ echo 'required'; } ?> accept=".png,.jpg,.jpeg,.gif">
                                            <div class="invalid-feedback">
                                                Please choose Product Images.
                                            </div>
                                        </div>
                                        <?php if ($id!="") { ?>
                                            <img src="uploads/images/main_categories/<?php echo $img ?>" style="max-height: 50px;max-width: 50px;">
                                        <?php } ?>

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
        $(document).ready(function() {
            $("#product_type").change(function() {
                $("#simple_product_data").slideToggle("slow");
                if ( $(this).val() == 'variable' ) {
                    $("#simple_product_data input,select,textarea").prop("required", false);
                    $("#addRowBtn").css("display", "block");
                } else {
                    $("#simple_product_data input,select,textarea").prop("required", true);
                    $("#addRowBtn").css("display", "none");
                }                
            });
            // ClassicEditor
            //     .create( document.querySelector( '#editor' ), {
            //         // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
            //     })
            //     .then( editor => {
            //         window.editor = editor;
            //     })
            //     .catch( err => {
            //         console.error( err.stack );
            //     });
            document.getElementById('addRowBtn').addEventListener('click', function() {
                var template = document.querySelector('.template-row');
                var clone = template.cloneNode(true);
                clone.classList.remove('template-row');
                clone.style.display = 'flex'; // Show the cloned row
                document.getElementById('dynamicRows').appendChild(clone);

                // Add event listener to the newly added 'Add Attr.' button
                clone.querySelector('.addAttributeBtn').addEventListener('click', function() {
                    var templateAttribute = document.querySelector('.attribute');
                    var cloneAttribute = templateAttribute.cloneNode(true);
                    cloneAttribute.style.display = 'block'; // Show the cloned attribute
                    clone.querySelector('.dynamicAttributes > .row').appendChild(cloneAttribute);
                });
            });

            // category | subcategory select
            $("input[name='subcategory[]']").click(function(){
                var data = $(this).attr('class');
                var arr = data.split('subcategoryCheck');
                var id = arr[1];
                // checkbox's check
                var check = 0;

                $(".subcategoryCheck"+id).each(function() {
                    if ($(this).prop('checked')==true){ 
                        check = 1;
                    }
                });

                if(check == 1){
                    $("#categoryCheck"+id).prop('checked', true);
                }
                else {
                    $("#categoryCheck"+id).prop('checked', false);
                }
            });

            $("input[name='category[]']").click(function(){
                var id = $(this).val();
                var checkStatus = '';
                if ($(this).prop('checked')==false){
                    checkStatus = false;
                } else {
                    checkStatus = true;
                }
                $(".subcategoryCheck"+id).each(function() {
                    $(this).prop('checked', checkStatus);
                });
            });
        });
    </script>

</body>

</html>