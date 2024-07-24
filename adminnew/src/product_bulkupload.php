<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Product Bulk Upload";
    include 'partials/title-meta.php'; ?>

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
            height: 200px;
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
            // form fields
            $sku = mysqli_real_escape_string($conn, $_POST['sku']);
            $heading = 'Heading';
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
            $price = mysqli_real_escape_string($conn, $_POST['price']);
            $discount = mysqli_real_escape_string($conn, $_POST['discount']);
            $tax = mysqli_real_escape_string($conn, $_POST['tax']);
            $length = mysqli_real_escape_string($conn, $_POST['length']);
            $width = mysqli_real_escape_string($conn, $_POST['width']);
            $height = mysqli_real_escape_string($conn, $_POST['height']);
            $dimensions = "[$length, $width, $height]";
            $pincodes = mysqli_real_escape_string($conn, $_POST['pincodes']);
            $category = join(",", $_POST['category']);
            $subcategory = join(",", $_POST['subcategory']);
            $tags = mysqli_real_escape_string($conn, $_POST['tags']);
            $brand_id = mysqli_real_escape_string($conn, $_POST['brand_id']);
            $vendor_id = mysqli_real_escape_string($conn, $_POST['vendor_id']);
            $cod_charges = mysqli_real_escape_string($conn, $_POST['cod_charges']);
            $shipping_charges = mysqli_real_escape_string($conn, $_POST['shipping_charges']);
            // $description_desc = base64_encode(mysqli_real_escape_string($conn, $_POST['description_desc']));
            $description_desc = 'description';
            $description_short = mysqli_real_escape_string($conn, $_POST['description_short']);
            $is_cod_available = mysqli_real_escape_string($conn, $_POST['is_cod_available']);
            $is_pan_india_available = mysqli_real_escape_string($conn, $_POST['is_pan_india_available']);
            $rating = mysqli_real_escape_string($conn, $_POST['rating']);
            $review = mysqli_real_escape_string($conn, $_POST['review']);

            $variable_i = 0;
            $variables = array();
            $variables_index = 0;
            $variables_length = COUNT($_POST['v_size']);
            while ( $variables_length > 0 ) {
                // check all the variables found for the single row
                if ($_POST['v_mrp'][$variable_i]!='' && $_POST['v_price'][$variable_i]!='' && $_POST['v_qty'][$variable_i]!='' && $_POST['v_size'][$variable_i]!='' && $_POST['v_color_name'][$variable_i]!='') {
                    $variables[$variables_index]['mrp'] = $_POST['v_mrp'][$variable_i];
                    $variables[$variables_index]['price'] = $_POST['v_price'][$variable_i];
                    $variables[$variables_index]['qty'] = $_POST['v_qty'][$variable_i];
                    $variables[$variables_index]['size'] = $_POST['v_size'][$variable_i];
                    $variables[$variables_index]['color_name'] = $_POST['v_color_name'][$variable_i];
                    $variables[$variables_index]['image'] = '';
                    $variables[$variables_index]['dimensions']['length'] = 0;
                    $variables[$variables_index]['dimensions']['width'] = 0;
                    $variables[$variables_index]['dimensions']['height'] = 0;
                    $variables[$variables_index]['dimensions']['weight'] = 0;

                    $variables_index++;
                }

                $variable_i++;
                $variables_length--;
            }
            $variables = json_encode($variables);

            $queryString = "INSERT INTO `productsnew` SET `sku` = '$sku', `heading` = '$heading', `title` = '$title', `quantity` = '$quantity', `price` = '$price', `discount` = '$discount', `tax` = '$tax', `dimensions` = '$dimensions', `pincodes` = '$pincodes', `category` = '$category', `subcategory` = '$subcategory', `tags` = '$tags', `brand_id` = '$brand_id', `vendor_id` = '$vendor_id', `cod_charges` = '$cod_charges', `shipping_charges` = '$shipping_charges', `description_desc` = '$description_desc', `description_short` = '$description_short', `is_cod_available` = '$is_cod_available', `is_pan_india_available` = '$is_pan_india_available', `rating` = '$rating', `review` = '$review', `variables`='$variables', `user_id`='1'";
            $query = mysqli_query($conn, $queryString);
            $insert_id = mysqli_insert_id($conn);

            if ($query) {
                // Generating EBIN ID
                $product_id = 'PR-'; // prefix
                $product_id .= str_pad($insert_id, (10-strlen($insert_id)), '0', STR_PAD_LEFT); // Adding leading zeros to the id
                $product_id .= '-EBIN'; // suffix

                // update product id
                $update = mysqli_query($conn, "UPDATE `productsnew` SET `product_id` = '$product_id' WHERE `id`='$insert_id'");

                // Image uploadings
                if ($_FILES['main_img']['name']!='') {
                    $main_img = upload_image($_FILES['main_img'], "main_img/");
                }
                if ($_FILES['hover_img']['name']!='') {
                    $hover_img = upload_image($_FILES['hover_img'], "hover_img/");
                }
                if ($_FILES['product_images']['name'][0]!='') {
                    // multiple upload
                    $product_images_length = count($_FILES['product_images']['name']);
                    $product_image_i = 0;
                    while ($product_images_length > 0) {
                        $product_image = array('name'=>$_FILES['product_images']['name'][$product_image_i], 'full_path'=>$_FILES['product_images']['full_path'][$product_image_i], 'type'=>$_FILES['product_images']['type'][$product_image_i], 'tmp_name'=>$_FILES['product_images']['tmp_name'][$product_image_i], 'error'=>$_FILES['product_images']['error'][$product_image_i], 'size'=>$_FILES['product_images']['size'][$product_image_i]);

                        $product_images .= upload_image($product_image, "product_images/");
                        $product_images .= ", ";

                        $product_image_i++;
                        $product_images_length--;
                    }
                    rtrim($product_images, ', ');
                }

                // update image names
                $update = mysqli_query($conn, "UPDATE `productsnew` SET `main_img` = '$main_img', `hover_img` = '$hover_img', `product_images` = '$product_images' WHERE `id`='$insert_id'");
                
                echo "<script>alert('Product added successfully.');location.href='';</script>";
            } else {
                echo "<script>alert('Failed to insert, Please try again later.');location.href='';</script>";                
            }
        }

        function upload_image($file, $destination) {
            $fullpath = "uploads/images/".$destination;
            $filename = $file['name'];
            $isupload = move_uploaded_file($file['tmp_name'], $fullpath.$filename);
            if (!$isupload) {
                $filename = '';
            }
            return $filename;
        }
        ?>

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <?php
                    $sub_title = "Products";
                    $page_title = "Bulk Upload";
                    include 'partials/page-title.php'; ?>

                    <div class="row justify-content-center">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3 checkbox-dropdown">
                                                    <label class="form-label" for="validationCustom12">Categories</label><br />
                                                    <div class="btn-group">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                                            Select
                                                        </button>
                                                        <ul class="dropdown-menu" data-simplebar>
                                                            <?php
                                                            $category_query = mysqli_query($conn, "SELECT * FROM `categories` ORDER BY `categoryName` ASC");
                                                            while ($category_fetch = mysqli_fetch_array($category_query)) {
                                                                $category_id = $category_fetch['ID'];
                                                                ?>
                                                                <li class="dropdown-item">
                                                                    <div class="form-check">
                                                                        <input type="checkbox" class="form-check-input" id="categoryCheck<?php echo $category_id; ?>" name="category[]" value="<?php echo $category_id; ?>">
                                                                        <label class="form-check-label" for="categoryCheck<?php echo $category_id; ?>"><?php echo $category_fetch['categoryName']; ?></label>
                                                                    </div>
                                                                    <?php
                                                                    $subcategory_query = mysqli_query($conn, "SELECT * FROM `subcategory` WHERE `mcID`='$category_id' ORDER BY `subCategoryName` ASC");
                                                                    $subcategory_count = mysqli_num_rows($subcategory_query);
                                                                    if ($subcategory_count) echo "<ul>";
                                                                    while ($subcategory_fetch = mysqli_fetch_array($subcategory_query)) {
                                                                        $subcategory_id = $subcategory_fetch['ID'];
                                                                        ?>
                                                                        <li class="dropdown-item">
                                                                            <div class="form-check">
                                                                                <input type="checkbox" class="form-check-input subcategoryCheck<?php echo $category_id; ?>" id="subcategoryCheck<?php echo $subcategory_id; ?>" name="subcategory[]" value="<?php echo $subcategory_id; ?>">
                                                                                <label class="form-check-label" for="subcategoryCheck<?php echo $subcategory_id; ?>"><?php echo $subcategory_fetch['subCategoryName']; ?></label>
                                                                            </div>
                                                                        </li>
                                                                    <?php } if ($subcategory_count) echo "</ul>"; ?>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                    <!-- <input type="text" class="form-control" id="validationCustom12" name="category" required> -->
                                                    <div class="invalid-feedback" id="subcategory-invalid">
                                                        Please choose atleast one Sub Category.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom09">Select CSV</label>
                                                    <input type="file" class="form-control" id="validationCustom09" name="csv" required accept=".csv">
                                                    <div class="invalid-feedback">
                                                        Please select a CSV file.
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

    <script>
        $(document).ready(function() {
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

            $('form[novalidate]').submit(function(event) {

                var subcategoryChecked = $("input[name='subcategory[]']:checked").length;

                if (subcategoryChecked == 0) {
                    $("#subcategory-invalid").css("display", "block");
                    return false;
                }
                $("#subcategory-invalid").css("display", "none");
            });
        });
    </script>

</body>

</html>