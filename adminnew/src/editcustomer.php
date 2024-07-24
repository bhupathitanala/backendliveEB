<?php include 'partials/main.php'; ?>

<?php
$title = "Add New User";
$page_title = "Add New User";


$id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));
$username = "";
$password = "";
$email = "";
$mobile = "";
$user_type = "";
$website = "";
$status = "";
if(strlen(trim($id))>0){
    $query = $conn->query("SELECT * FROM `admin_users` WHERE ID='$id'");
    $UserDetails = $query->fetch_assoc();

    $page_title = "Edit User";
    $title = "Edit User";

    $username = $UserDetails['name'];
    $email = $UserDetails['email'];
    $mobile = $UserDetails['mobile'];
    $user_type = $UserDetails['user_type'];
    $status = $UserDetails['status'];
    $website = $UserDetails['website'];
}
?>



<head>
    <?php
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

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));
            $username = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'username'));
            $password = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'password'));
            $email = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'email'));
            $mobile = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'mobile'));
            $status = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'status'));
            $user_type = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'user_type'));
            $website = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'website'));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $permissions = '["products","orders","enquiries"]';

            $query = $conn->query("SELECT * FROM `admin_users` WHERE `email`='$email' and ID!='$id'");
            $count = $query->num_rows;
            if ($count>0) {
                echo "<script>alert('Email already exist')</script>";
            }else{
                if (strlen(trim($id))>0) {
                    // Update User
                    if (!strlen(trim($password))) {
                        $add = "";
                    }else{
                        $add = ", password='$hashedPassword'";
                    }
                    $sql = "UPDATE `admin_users` SET `email`='$email', `name`='$username', `mobile`='$mobile', `user_type`='$user_type', `permissions`='$permissions', `status`='$status', `website`='$website' $add WHERE id='$id'";
                    if ($conn->query($sql)) {
                        echo "<script>alert('User successfully updated');location.href='users'</script>";
                    }else{
                        echo "<script>alert('Internal server error')</script>";
                    }

                }else{
                    if ($status=="") {
                        $status=1;
                    }
                    // Create User Account
                    $sql = "INSERT INTO `admin_users` SET `email`='$email', `password`='$hashedPassword', `name`='$username', `mobile`='$mobile', `user_type`='$user_type', `permissions`='$permissions', `status`='$status', `website`='$website'";
                    if ($conn->query($sql)) {
                        echo "<script>alert('User successfully created');location.href=''</script>";
                    }else{
                        echo "<script>alert('Internal server error')</script>";
                    }
                }

            }

        }

        function uploadImage($file, $path, $imgPreviousName){
            $filename = time().".png";
            if (strlen(trim($imgPreviousName))>0) {
                $filename = $imgPreviousName;
            }
            $tmp_name = $file['tmp_name'];
            if (move_uploaded_file($tmp_name, $path.$filename)) {
                return $filename;
            }else{
                return "";
            }
        }
        ?>

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <?php
                    $sub_title = "Users";
                    
                    include 'partials/page-title.php'; ?>

                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="mb-3 col-6">
                                                <label>Username</label><b>*</b>
                                                <input type="text" name="username" class="form-control" required value="<?php echo $username ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Password</label><b>*</b>
                                                <input type="password" name="password" class="form-control" <?php if ($id=='') { echo 'required'; } ?> >
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Email</label><b>*</b>
                                                <input type="email" name="email" class="form-control" required value="<?php echo $email ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Mobile</label><b>*</b>
                                                <input type="number" name="mobile" class="form-control" required pattern="[6789][0-9]{9}" value="<?php echo $mobile ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>User Type</label><b>*</b>
                                                <select class="form-select" name="user_type" required>
                                                    <option value="">Select</option>
                                                    <!-- <option <?php if($user_type=='CUSTOMER')echo 'selected'; ?> <?php if($status=='CUSTOMER')echo 'selected'; ?> value="CUSTOMER">Customer</option> -->
                                                    <option <?php if($user_type=='ADMIN')echo 'selected'; ?> <?php if($status=='ADMIN')echo 'selected'; ?> value="ADMIN">Administrator</option>
                                                    <option <?php if($user_type=='BLOGGER')echo 'selected'; ?> <?php if($status=='BLOGGER')echo 'selected'; ?> value="BLOGGER">Blogger</option>
                                                    <option <?php if($user_type=='NUTRITION')echo 'selected'; ?> <?php if($status=='NUTRITION')echo 'selected'; ?> value="NUTRITION">Nutrition</option>
                                                </select>
                                            </div>

                                            <div class="mb-3 col-6" <?php if(!strlen(trim($id)))echo 'hidden'; ?>>
                                                <label>Status</label>
                                                <select class="form-select" name="status">
                                                    <option <?php if($status=='1')echo 'selected'; ?> value="1">Active</option>
                                                    <option <?php if($status=='0')echo 'selected'; ?> value="0">Suspend</option>
                                                </select>
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Website</label><b>*</b>
                                                <input type="text" name="website" class="form-control" placeholder="https://google.com" value="<?php echo $website ?>">
                                            </div>

                                            <button class="btn btn-primary" type="submit" name="submit">Submit</button>
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