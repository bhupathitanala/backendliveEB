<?php include 'partials/main.php'; ?>

<?php
$title = "Add New Vendor";
$page_title = "Add New Vendor";


$id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));
$name = "";
$username = "";
$password = "";
$email = "";
$mobile = "";
$website = "";
$address = "";
$gstin = "";
$state = "";
$pincode = "";
$accountName = "";
$accountType = "";
$bankName = "";
$bankAddress = "";
$ifscCode = "";
$status = "";
if(strlen(trim($id))>0){
    $query = $conn->query("SELECT a.*,b.email,b.mobile,b.name as `vendorName` FROM `vendors` a LEFT JOIN `admin_users` b ON a.user_id=b.id WHERE a.vendorID='$id'");

    $vendorDetails = $query->fetch_assoc();
    $page_title = "Edit Vendor";
    $title = "Edit Vendor";

    $name = $vendorDetails['name'];
    $username = $vendorDetails['vendorName'];
    $email = $vendorDetails['email'];
    $mobile = $vendorDetails['mobile'];
    $website = $vendorDetails['website'];
    $address = $vendorDetails['address'];
    $gstin = $vendorDetails['gstin'];
    $state = $vendorDetails['state'];
    $pincode = $vendorDetails['pincode'];
    $accountName = $vendorDetails['accountName'];
    $accountType = $vendorDetails['accountType'];
    $bankName = $vendorDetails['bankName'];
    $bankAddress = $vendorDetails['bankAddress'];
    $ifscCode = $vendorDetails['ifscCode'];
    $status = $vendorDetails['status'];
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
            $name = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'name'));
            $username = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'username'));
            $password = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'password'));
            $email = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'email'));
            $mobile = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'mobile'));
            $website = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'website'));
            $address = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'address'));
            $state = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'state'));
            $gstin = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'gstin'));
            $pincode = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'pincode'));
            $accountName = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'accountName'));
            $accountType = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'accountType'));
            $bankName = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'bankName'));
            $bankAddress = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'bankAddress'));
            $ifscCode = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'ifscCode'));
            $status = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'status'));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $permissions = '["products","orders","enquiries"]';
            if (strlen(trim($id))>0) {
                // Update
                $vendorImage = $vendorDetails['vendorImage'];
                if ($_FILES['image']) {
                    $vendorImage = uploadImage($_FILES['image'], "uploads/images/vendor_pictures/", $vendorImage);
                }

                $banner = $vendorDetails['banner'];
                if ($_FILES['image']) {
                    $banner = uploadImage($_FILES['banner'], "uploads/images/vendor_banners/", $banner);
                }

                $sql = "UPDATE `admin_users` SET `email`='$email', `name`='$username', `mobile`='$mobile', `user_type`='SUBADMIN', `permissions`='$permissions' WHERE id='$vendorDetails[user_id]'";
                if($conn->query($sql)){
                    $vendorId = $conn->insert_id;
                    $sql = "UPDATE `vendors` SET `name`='$name', `website`='$website', `address`='$address', `state`='$state', `gstin`='$gstin', `pincode`='$pincode', `accountName`='$accountName', `accountType`='$accountType', `bankName`='$bankName', `bankAddress`='$bankAddress', `ifscCode`='$ifscCode', `status`='$status' WHERE vendorID='$id'";
                    if ($conn->query($sql)) {
                        echo "<script>alert('Vendor successfully updated');location.href='vendors'</script>";
                    }else{
                        echo "<script>alert('Something went wrong, try-again later');location.href='vendors'</script>";
                    }
                }else{
                    echo "<script>alert('Internal server error')</script>";
                }

            }else{
                // Create User Account
                $query = $conn->query("SELECT * FROM `admin_users` WHERE `email`='$email'");
                $count = $query->num_rows;
                if ($count>0) {
                    echo "<script>alert('Email already exist')</script>";
                }else{
                    $vendorImage = "";
                    if ($_FILES['image']) {
                        $vendorImage = uploadImage($_FILES['image'], "uploads/images/vendor_pictures/", $vendorImage);
                    }

                    $banner = "";
                    if ($_FILES['image']) {
                        $banner = uploadImage($_FILES['banner'], "uploads/images/vendor_banners/", $banner);
                    }
                    

                    $sql = "INSERT INTO `admin_users` SET `email`='$email', `password`='$hashedPassword', `name`='$username', `mobile`='$mobile', `user_type`='VENDOR', `permissions`='$permissions',`status`=1";
                    if($conn->query($sql)){
                        $vendorId = $conn->insert_id;
                        $sql = "INSERT INTO `vendors` SET `name`='$name', `website`='$website', `address`='$address', `state`='$state', `gstin`='$gstin', `pincode`='$pincode', `accountName`='$accountName', `accountType`='$accountType', `bankName`='$bankName', `bankAddress`='$bankAddress', `ifscCode`='$ifscCode', `vendorImage`='$vendorImage', `banner`='$banner', `user_id`='$vendorId', `addedBy`='$_SESSION[userId]', `status`='$status'";
                        if ($conn->query($sql)) {
                            echo "<script>alert('Vendor successfully created');location.href=''</script>";
                        }else{
                            echo "<script>alert('Something went wrong, try-again later');location.href=''</script>";
                        }
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
                    $sub_title = "Vendors";
                    
                    include 'partials/page-title.php'; ?>

                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="image">Vendor Picture</label>
                                                <input type="file" class="form-control" name="image" accept=".png,.jpg,.jpeg,.gif.webp">
                                                <div class="invalid-feedback">
                                                    Please choose Product Images.
                                                </div>
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="banner">Banner</label>
                                                <input type="file" class="form-control" name="banner" accept=".png,.jpg,.jpeg,.gif.webp">
                                                <div class="invalid-feedback">
                                                    Please choose Product Images.
                                                </div>
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Name</label><b>*</b>
                                                <input type="text" name="name" class="form-control" required value="<?php echo $name ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Username</label><b>*</b>
                                                <input type="text" name="username" class="form-control" required value="<?php echo $username ?>">
                                            </div>

                                            <?php if(!strlen(trim($id))) { ?>
                                                <div class="mb-3 col-6">
                                                    <label>Password</label><b>*</b>
                                                    <input type="password" name="password" class="form-control" required value="<?php echo $password ?>">
                                                </div>
                                            <?php } ?>

                                            <div class="mb-3 col-6">
                                                <label>Email</label><b>*</b>
                                                <input type="email" name="email" class="form-control" required value="<?php echo $email ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Mobile</label><b>*</b>
                                                <input type="number" name="mobile" class="form-control" required pattern="[6789][0-9]{9}" value="<?php echo $mobile ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Website</label>
                                                <input type="url" name="website" class="form-control" value="<?php echo $website ?>">
                                            </div>



                                            <!-- <div class="mb-3 col-6">
                                                <label>Company Name</label>
                                                <input type="text" name="company" class="form-control" required value="<?php echo $company ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Company GST Number</label>
                                                <input type="text" name="gst_number" class="form-control" required  value="<?php echo $gst_number ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Company Certificate Number</label>
                                                <input type="text" name="certificate" class="form-control" required  value="<?php echo $certificate ?>">
                                            </div> -->

                                            <!-- <div class="mb-3 col-6">
                                                <label>City</label>
                                                <input type="text" name="city" class="form-control" required value="<?php echo $city ?>">
                                            </div> -->

                                            <!-- <div class="mb-3 col-6">
                                                <label>State</label>
                                                <input type="text" name="state" class="form-control" required value="<?php echo $state ?>">
                                            </div> -->

                                            <div class="mb-3 col-6">
                                                <label>GSTIN</label>
                                                <input type="text" name="gstin" class="form-control" value="<?php echo $gstin ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>State</label><b>*</b>
                                                <select class="form-select" name="state" required>
                                                    <option value="">Select</option>
                                                    <option <?php if($state=='Andhra Pradesh')echo 'selected' ?> value="Andhra Pradesh">Andhra Pradesh</option>
                                                    <option <?php if($state=='Arunachal Pradesh')echo 'selected' ?> value="Arunachal Pradesh">Arunachal Pradesh</option>
                                                    <option <?php if($state=='Assam')echo 'selected' ?> value="Assam">Assam</option>
                                                    <option <?php if($state=='Bihar')echo 'selected' ?> value="Bihar">Bihar</option>
                                                    <option <?php if($state=='Chandigarh')echo 'selected' ?> value="Chandigarh">Chandigarh</option>
                                                    <option <?php if($state=='Chhattisgarh')echo 'selected' ?> value="Chhattisgarh">Chhattisgarh</option>
                                                    <option <?php if($state=='Dadra & Nagar Haveli and Daman & Diu')echo 'selected' ?> value="Dadra & Nagar Haveli and Daman & Diu">Dadra & Nagar Haveli and Daman & Diu</option>
                                                    <option <?php if($state=='Goa')echo 'selected' ?> value="Goa">Goa</option>
                                                    <option <?php if($state=='Gujarat')echo 'selected' ?> value="Gujarat">Gujarat</option>
                                                    <option <?php if($state=='Haryana')echo 'selected' ?> value="Haryana">Haryana</option>
                                                    <option <?php if($state=='Himachal Pradesh')echo 'selected' ?> value="Himachal Pradesh">Himachal Pradesh</option>
                                                    <option <?php if($state=='Jharkhand')echo 'selected' ?> value="Jharkhand">Jharkhand</option>
                                                    <option <?php if($state=='Karnataka')echo 'selected' ?> value="Karnataka">Karnataka</option>
                                                    <option <?php if($state=='Kerala')echo 'selected' ?> value="Kerala">Kerala</option>
                                                    <option <?php if($state=='Madhya Pradesh')echo 'selected' ?> value="Madhya Pradesh">Madhya Pradesh</option>
                                                    <option <?php if($state=='Maharashtra')echo 'selected' ?> value="Maharashtra">Maharashtra</option>
                                                    <option <?php if($state=='Manipur')echo 'selected' ?> value="Manipur">Manipur</option>
                                                    <option <?php if($state=='Meghalaya')echo 'selected' ?> value="Meghalaya">Meghalaya</option>
                                                    <option <?php if($state=='Mizoram')echo 'selected' ?> value="Mizoram">Mizoram</option>
                                                    <option <?php if($state=='Nagaland')echo 'selected' ?> value="Nagaland">Nagaland</option>
                                                    <option <?php if($state=='National Capital Territory of Delhi')echo 'selected' ?> value="National Capital Territory of Delhi">National Capital Territory of Delhi</option>
                                                    <option <?php if($state=='Odisha')echo 'selected' ?> value="Odisha">Odisha</option>
                                                    <option <?php if($state=='Punjab')echo 'selected' ?> value="Punjab">Punjab</option>
                                                    <option <?php if($state=='Rajasthan')echo 'selected' ?> value="Rajasthan">Rajasthan</option>
                                                    <option <?php if($state=='Sikkim')echo 'selected' ?> value="Sikkim">Sikkim</option>
                                                    <option <?php if($state=='Tamil Nadu')echo 'selected' ?> value="Tamil Nadu">Tamil Nadu</option>
                                                    <option <?php if($state=='Telangana')echo 'selected' ?> value="Telangana">Telangana</option>
                                                    <option <?php if($state=='Uttarakhand')echo 'selected' ?> value="Uttarakhand">Uttarakhand</option>
                                                    <option <?php if($state=='Uttar Pradesh')echo 'selected' ?> value="Uttar Pradesh">Uttar Pradesh</option>
                                                    <option <?php if($state=='Tripura')echo 'selected' ?> value="Tripura">Tripura</option>
                                                    <option <?php if($state=='West Bengal')echo 'selected' ?> value="West Bengal">West Bengal</option>
                                                    <option <?php if($state=='Puducherry')echo 'selected' ?> value="Puducherry">Puducherry</option>

                                                </select>
                                            </div>

                                            <div class="mb-3 col-<?php echo !strlen(trim($id)) ? 12 : 6; ?>">
                                                <label>Address</label>
                                                <textarea name="address" class="form-control"><?php echo $address ?></textarea>
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Pincode</label>
                                                <input type="text" name="pincode" class="form-control" value="<?php echo $pincode ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Account Name</label>
                                                <input type="text" name="accountName" class="form-control" value="<?php echo $accountName ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Account Type</label>
                                                <select name="accountType" class="form-select">
                                                    <option value="">Select</option>
                                                    <option <?php if($accountType=='Personal')echo 'selected'; ?> value="Personal">Personal</option>
                                                    <option <?php if($accountType=='Business')echo 'selected'; ?> value="Business">Business</option>
                                                </select>
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Bank Name</label>
                                                <input type="text" name="bankName" class="form-control" value="<?php echo $bankName ?>">
                                            </div>

                                            <div class="mb-3 col-12">
                                                <label>Bank Address</label>
                                                <textarea name="bankAddress" class="form-control"><?php echo $bankAddress ?></textarea>
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>IFSC Code</label>
                                                <input type="text" name="ifscCode" class="form-control" value="<?php echo $ifscCode ?>">
                                            </div>

                                            <div class="mb-3 col-6">
                                                <label>Status</label>
                                                <select class="form-select" name="status">
                                                    <option <?php if($status=='1')echo 'selected'; ?> value="1">Pending</option>
                                                    <option <?php if($status=='2')echo 'selected'; ?> value="2">Accepted</option>
                                                    <option <?php if($status=='3')echo 'selected'; ?> value="3">Rejected</option>
                                                </select>
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