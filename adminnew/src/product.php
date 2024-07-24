<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Add New Product";
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
            max-height: 200px;
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
            // form fields
            $product_type = mysqli_real_escape_string($conn, $_POST['product_type']);
            // $heading = mysqli_real_escape_string($conn, $_POST['heading']);
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $sku = mysqli_real_escape_string($conn, $_POST['sku']);
            $quantity = $_POST['quantity'] ? mysqli_real_escape_string($conn, $_POST['quantity']) : 0;
            $price = mysqli_real_escape_string($conn, $_POST['price']);
            $sale_price = $_POST['sale_price'] ? mysqli_real_escape_string($conn, $_POST['sale_price']) : $price;
            $length = mysqli_real_escape_string($conn, $_POST['length']);
            $width = mysqli_real_escape_string($conn, $_POST['width']);
            $height = mysqli_real_escape_string($conn, $_POST['height']);
            $weight = mysqli_real_escape_string($conn, $_POST['weight']);
            $dimensions = json_encode(array('0'=>$length, '1'=>$width, '2'=>$height));
            $tax_status = mysqli_real_escape_string($conn, $_POST['tax_status']);
            $tax = mysqli_real_escape_string($conn, $_POST['tax']);
            $pincodes = mysqli_real_escape_string($conn, $_POST['pincodes']);
            $category = $_POST['category'] ? join(",", $_POST['category']) : 0;
            $subcategory = $_POST['subcategory'] ? join(",", $_POST['subcategory']) : 0;
            $tags = mysqli_real_escape_string($conn, $_POST['tags']);
            $brand_id = $_POST['brand_id'] ? mysqli_real_escape_string($conn, $_POST['brand_id']) : 0;
            $vendor_id = $_POST['vendor_id'] ? mysqli_real_escape_string($conn, $_POST['vendor_id']) : 0;
            $cod_charges = $_POST['cod_charges'] ? mysqli_real_escape_string($conn, $_POST['cod_charges']) : 0;
            $shipping_charges = $_POST['shipping_charges'] ? mysqli_real_escape_string($conn, $_POST['shipping_charges']) : 0;
            $description_desc = mysqli_real_escape_string($conn, $_POST['description_desc']);
            $description_short = mysqli_real_escape_string($conn, $_POST['description_short']);
            $is_cod_available = $_POST['is_cod_available'] ? mysqli_real_escape_string($conn, $_POST['is_cod_available']) : 0;
            $is_pan_india_available = $_POST['is_pan_india_available'] ? mysqli_real_escape_string($conn, $_POST['is_pan_india_available']) : 0;
            $rating = mysqli_real_escape_string($conn, $_POST['rating']);
            $review = mysqli_real_escape_string($conn, $_POST['review']);
            $status = $_POST['status'] ? mysqli_real_escape_string($conn, $_POST['status']) : 0;
            $user_id = $_SESSION['userId'];
            $datetime = date('Y-m-d H:i:s');

            // edit id
            $id = mysqli_real_escape_string($conn, $_GET['v']);

            $variables = array();
            $variables_quantity = array();
            // if ($product_type == 'variable') {
                $variable_i = 0;
                $variable_prices = array();
                $variables_index = 0;
                $variables_length = COUNT($_POST['v_price']); // get number of variables
                while ( $variables_length > 0 ) {
                    // check all the variables found for the single row
                    if ($_POST['v_price'][$variable_i]!='' && $_POST['v_quantity'][$variable_i]!='') {
                        $v_attributes = $_POST['v_attributes'][$variable_i] ? json_decode($_POST['v_attributes'][$variable_i], true) : [];
                        $variables[$variables_index]['attributes'] = !is_null($v_attributes) ? $v_attributes : [];
                        $variables[$variables_index]['sku'] = $_POST['v_sku'][$variable_i];
                        $variables[$variables_index]['price'] = $_POST['v_price'][$variable_i];
                        $variables[$variables_index]['sale_price'] = $_POST['v_sale_price'][$variable_i];
                        $variables[$variables_index]['quantity'] = $_POST['v_quantity'][$variable_i];
                        $variables[$variables_index]['weight'] = $_POST['v_weight'][$variable_i];
                        // image uploading
                        if ($_FILES['v_image']['name'][$variable_i][0] != '') {
                            // multiple upload
                            $variable_images = '';
                            $variable_images_length = count($_FILES['v_image']['name'][$variable_i]);
                            $variable_image_i = 0;
                            while ($variable_images_length > 0) {
                                $variable_image = array('name'=>$_FILES['v_image']['name'][$variable_i][$variable_image_i], 'full_path'=>$_FILES['v_image']['full_path'][$variable_i][$variable_image_i], 'type'=>$_FILES['v_image']['type'][$variable_i][$variable_image_i], 'tmp_name'=>$_FILES['v_image']['tmp_name'][$variable_i][$variable_image_i], 'error'=>$_FILES['v_image']['error'][$variable_i][$variable_image_i], 'size'=>$_FILES['v_image']['size'][$variable_i][$variable_image_i]);

                                $variable_images .= upload_image($variable_image, "vproduct".$variable_image_i);
                                // $variable_images .= ",";

                                $variable_image_i++;
                                $variable_images_length--;
                            }
                            // $variable_images = array_values(array_filter(explode(",", $variable_images)));
                            $variables[$variables_index]['image'] = $variable_images;
                        } else {
                            if (strlen(trim($id)) == 0) {
                                // inserting
                                // $variables[$variables_index]['image'] = [];
                                $variables[$variables_index]['image'] = "";
                            } else {
                                // updating the row
                                // $variables[$variables_index]['image'] = json_decode($_POST['v_preimage'][$variable_i], true);
                                $variables[$variables_index]['image'] = $_POST['v_preimage'][$variable_i];
                            }
                        }
                        $variables[$variables_index]['dimensions']['length'] = $_POST['v_length'][$variable_i];
                        $variables[$variables_index]['dimensions']['width'] = $_POST['v_width'][$variable_i];
                        $variables[$variables_index]['dimensions']['height'] = $_POST['v_height'][$variable_i];
                        $variables[$variables_index]['tax_status'] = $_POST['v_tax_status'][$variable_i];
                        $variables[$variables_index]['tax'] = $_POST['v_tax'][$variable_i];
                        $variables[$variables_index]['cod_charges'] = $_POST['v_cod_charges'][$variable_i];
                        $variables[$variables_index]['shipping_charges'] = $_POST['v_shipping_charges'][$variable_i];

                        // Add into variables price array
                        $variable_prices[] = $_POST['v_price'][$variable_i];
                        if ($_POST['v_sale_price'][$variable_i] != '') {
                            $variable_prices[] = $_POST['v_sale_price'][$variable_i];
                        }

                        // This is for storing variable combination quantity
                        foreach ($variables[$variables_index]['attributes'] as $attrkey => $variable_attribute) {
                            if ($variable_attribute['name'] != '') {
                                $variables_quantity[$variables_index][$variable_attribute['name']] = $variable_attribute['value'];
                            }
                        }
                        $variables_quantity[$variables_index]['quantity'] = $_POST['v_quantity'][$variable_i];

                        // increment array index
                        $variables_index++;
                    }

                    $variable_i++;
                    $variables_length--;
                }
                $variables = json_encode($variables);
                $variables_quantity = json_encode($variables_quantity);
            // }

            // query addons
            $query_addon = "";
            if ($product_type == 'simple') {
                // These values only come for the simple product type
                $query_addon = "`sku` = '$sku', `quantity` = '$quantity', `price` = '$price', `sale_price` = '$sale_price', `weight` = '$weight', `dimensions` = '$dimensions', `cod_charges` = '$cod_charges', `shipping_charges` = '$shipping_charges', `tax_status` = '$tax_status', `tax` = '$tax',";
            } else {
                // min and max prices
                $price = $variable_prices ? min($variable_prices) : 0;
                $max_price = $variable_prices ? max($variable_prices) : 0;
                // These values only come for the variable product type
                $query_addon = "`price` = '$price', `max_price` = '$max_price',";
            }
            
            if (strlen(trim($id)) == 0) {
                $queryString = "INSERT INTO `productsnew` SET `product_type` = '$product_type', `title` = '$title', $query_addon `pincodes` = '$pincodes', `category` = '$category', `subcategory` = '$subcategory', `tags` = '$tags', `brand_id` = '$brand_id', `vendor_id` = '$vendor_id', `description_desc` = '$description_desc', `description_short` = '$description_short', `is_cod_available` = '$is_cod_available', `is_pan_india_available` = '$is_pan_india_available', `rating` = '$rating', `review` = '$review', `variables`='$variables', `variables_quantity`='$variables_quantity', `manual_created_on`='$datetime', `user_id`='$user_id', `status`='$status'";
                $query = mysqli_query($conn, $queryString);
                $insert_id = mysqli_insert_id($conn);
                $type = "insert";
            } else {
                $queryString = "UPDATE `productsnew` SET `product_type` = '$product_type', `title` = '$title', $query_addon `pincodes` = '$pincodes', `category` = '$category', `subcategory` = '$subcategory', `tags` = '$tags', `brand_id` = '$brand_id', `vendor_id` = '$vendor_id', `description_desc` = '$description_desc', `description_short` = '$description_short', `is_cod_available` = '$is_cod_available', `is_pan_india_available` = '$is_pan_india_available', `rating` = '$rating', `review` = '$review', `variables`='$variables', `variables_quantity`='$variables_quantity', `manual_updated_on`='$datetime', `lastedited_user_id`='$user_id', `status`='$status' WHERE `id`='$id'";
                $query = mysqli_query($conn, $queryString);
                $insert_id = $id;
                $type = "edit";
            }

            if ($query) {
                if ($type == 'insert') {
                    // Generating EBIN ID
                    $product_id = 'PR-'; // prefix
                    $product_id .= str_pad($insert_id, (10-strlen($insert_id)), '0', STR_PAD_LEFT); // Adding leading zeros to the id
                    $product_id .= '-EBIN'; // suffix

                    // update product id
                    $update = mysqli_query($conn, "UPDATE `productsnew` SET `product_id` = '$product_id' WHERE `id`='$insert_id'");
                }

                // Image uploadings
                // variable declarations
                $main_img = '';
                $hover_img = '';
                $product_images = '';

                if ($_FILES['main_img']['name']!='') {
                    $main_img = upload_image($_FILES['main_img'], "main");
                } else if($type != 'edit') {
                    $main_img = "image-not-available.png";
                }
                if ($_FILES['hover_img']['name']!='') {
                    $hover_img = upload_image($_FILES['hover_img'], "hover");
                }
                if ($_FILES['product_images']['name'][0]!='') {
                    $product_images = array();

                    // multiple upload
                    $product_images_length = count($_FILES['product_images']['name']);
                    $product_image_i = 0;
                    while ($product_images_length > 0) {
                        $product_image = array('name'=>$_FILES['product_images']['name'][$product_image_i], 'full_path'=>$_FILES['product_images']['full_path'][$product_image_i], 'type'=>$_FILES['product_images']['type'][$product_image_i], 'tmp_name'=>$_FILES['product_images']['tmp_name'][$product_image_i], 'error'=>$_FILES['product_images']['error'][$product_image_i], 'size'=>$_FILES['product_images']['size'][$product_image_i]);

                        $product_images[] = upload_image($product_image, "product".$product_image_i);
                        // $product_images .= ",";

                        $product_image_i++;
                        $product_images_length--;
                    }
                    // $product_images = json_encode(array_values(array_filter(explode(",", $product_images))));
                    $product_images = json_encode(array_values(array_filter($product_images)));
                }

                // update image names
                if ($type == 'insert') {
                    $update = mysqli_query($conn, "UPDATE `productsnew` SET `main_img` = '$main_img', `hover_img` = '$hover_img', `product_images` = '$product_images' WHERE `id`='$insert_id'");
                } else {
                    $update_fields = array();
                    $update_fields[0] = $main_img ? "`main_img` = '$main_img'" : '';
                    $update_fields[1] = $hover_img ? "`hover_img` = '$hover_img'" : '';
                    $update_fields[2] = $product_images ? "`product_images` = '$product_images'" : '';
                    // remove null values
                    $update_fields = array_filter($update_fields);
                    if (count($update_fields)) {
                        $update_fields = join(",", $update_fields);
                        $update = mysqli_query($conn, "UPDATE `productsnew` SET $update_fields WHERE `id`='$insert_id'");
                    }
                }
                
                if ($type == 'insert') {
                    $msg = "Product added successfully.";
                    $location = '';
                } else {
                    $msg = "Product updated successfully.";
                    $location = 'products';
                }
                echo "<script>alert('".$msg."');location.href='".$location."';</script>";
            } else {
                echo "<script>alert('Failed to insert, Please try again later.');location.href='';</script>";                
            }
        } else {
            $id = mysqli_real_escape_string($conn, $_GET['v']);
            if (isset($id) && (int)$id) {
                // get product row
                $sql = "SELECT * FROM `productsnew` WHERE `id`='$id'";
                $result = mysqli_query($conn, $sql);
                $rowcount = mysqli_num_rows($result);

                if ($rowcount) {
                    $row = mysqli_fetch_array($result);
                    $_POST = $row;

                    // json decodings
                    $_POST['dimensions'] = json_decode($_POST['dimensions'], true);
                    $_POST['category'] = explode(",", $_POST['category']);
                    $_POST['subcategory'] = explode(",", $_POST['subcategory']);
                    $_POST['variables'] = json_decode($_POST['variables'], true);
                }
            } else {
                $_POST = array();
                $_POST['category'] = array();
                $_POST['subcategory'] = array();
                $_POST['variables'] = array();
            }
        }

        function upload_image($file, $img_type) {
            // $fullpath = "uploads/images/".$destination;
            $fullpath = "uploads/images/product_images/";
            $filename = time()."-".$img_type."-".$file['name'];
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
                    $page_title = "Add New Product";
                    include 'partials/page-title.php'; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product_type">Product Type</label>
                                                    <select class="form-select" id="product_type" name="product_type" required>
                                                        <option value="simple" <?php if($_POST['product_type'] == 'simple') echo "selected"; ?>>Simple product</option>
                                                        <option value="variable" <?php if($_POST['product_type'] == 'variable') echo "selected"; ?>>Variable product</option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Please choose a Product Type.
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="heading">Heading</label>
                                                    <input type="text" class="form-control" id="heading" name="heading" required>
                                                    <div class="invalid-feedback">
                                                        Please provide the Product Heading.
                                                    </div>
                                                </div>
                                            </div> -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product_title">Product Title*</label>
                                                    <input type="text" class="form-control" id="product_title" name="title" value="<?php echo $_POST['title']; ?>" required>
                                                    <div class="invalid-feedback">
                                                        Please provide the Product Title.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="simple_product_data">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="sku">SKU</label>
                                                        <input type="text" class="form-control" id="sku" name="sku" value="<?php echo $_POST['sku']; ?>">
                                                        <div class="invalid-feedback">
                                                            Please provide a valid SKU.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="regular_price">Regular Price* (<i class="bi bi-currency-rupee"></i>)</label>
                                                        <input type="number" min="0" step="0.01" class="form-control" id="regular_price" name="price" value="<?php echo $_POST['price']; ?>" required>
                                                        <div class="invalid-feedback">
                                                            Please provide a valid Price.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="sale_price">Sale Price (<i class="bi bi-currency-rupee"></i>)</label>
                                                        <input type="number" min="0" step="0.01" class="form-control" id="sale_price" name="sale_price" value="<?php echo $_POST['sale_price']; ?>">
                                                        <div class="invalid-feedback">
                                                            Please provide a valid Sale price.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="stock_levels">Stock Levels (Quantity)</label>
                                                        <input type="number" min="0" class="form-control" id="stock_levels" name="quantity" value="<?php echo $_POST['quantity']; ?>">
                                                        <div class="invalid-feedback">
                                                            Please provide a valid Quantity.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="weight">Weight (g)</label>
                                                        <input type="number" min="0" step="0.01" class="form-control" id="weight" name="weight" value="<?php echo $_POST['weight']; ?>">
                                                        <div class="invalid-feedback">
                                                            Please provide a valid Weight.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="dimensions">Dimensions (cm)</label>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <input type="number" min="0" step="0.01" class="form-control" id="length" name="length" placeholder="Length" value="<?php echo $_POST['dimensions'][0]; ?>">
                                                                <div class="invalid-feedback">
                                                                    Please provide a valid Length.
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="number" min="0" step="0.01" class="form-control" id="width" name="width" placeholder="Width" value="<?php echo $_POST['dimensions'][1]; ?>">
                                                                <div class="invalid-feedback">
                                                                    Please provide a valid Width.
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="number" min="0" step="0.01" class="form-control" id="height" name="height" placeholder="Height" value="<?php echo $_POST['dimensions'][2]; ?>">
                                                                <div class="invalid-feedback">
                                                                    Please provide a valid Height.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="tax_status">Tax Status</label>
                                                        <select class="form-select" id="tax_status" name="tax_status">
                                                            <option value="Taxable" <?php if($_POST['tax_status'] == 'Taxable') echo "selected"; ?>>Taxable</option>
                                                            <option value="Non Taxable" <?php if($_POST['tax_status'] == 'Non Taxable') echo "selected"; ?>>Non Taxable</option>
                                                        </select>
                                                        <div class="invalid-feedback">
                                                            Please choose a Tax Status.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="tax_class">Tax Class</label>
                                                        <input type="number" min="0" step="0.01" class="form-control" id="tax_class" name="tax" value="<?php echo $_POST['tax']; ?>">
                                                        <div class="invalid-feedback">
                                                            Please provide a valid Tax class.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="cod_charges">COD Charges (<i class="bi bi-currency-rupee"></i>)</label>
                                                        <input type="number" min="0" step="0.01" class="form-control" id="cod_charges" name="cod_charges" value="<?php echo $_POST['cod_charges']; ?>">
                                                        <div class="invalid-feedback">
                                                            Please provide a COD Fee.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="shipping_charges">Shipping Charges (<i class="bi bi-currency-rupee"></i>)</label>
                                                        <input type="number" min="0" step="0.01" class="form-control" id="shipping_charges" name="shipping_charges" value="<?php echo $_POST['shipping_charges']; ?>">
                                                        <div class="invalid-feedback">
                                                            Please provide Shipping Charges.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3 checkbox-dropdown">
                                                    <label class="form-label" for="categories">Categories</label><br />
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
                                                                        <input type="checkbox" class="form-check-input" id="categoryCheck<?php echo $category_id; ?>" name="category[]" value="<?php echo $category_id; ?>" <?php if(in_array($category_id, $_POST['category'])) echo "checked"; ?>>
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
                                                                                <input type="checkbox" class="form-check-input subcategoryCheck<?php echo $category_id; ?>" id="subcategoryCheck<?php echo $subcategory_id; ?>" name="subcategory[]" value="<?php echo $subcategory_id; ?>" <?php if(in_array($subcategory_id, $_POST['subcategory'])) echo "checked"; ?>>
                                                                                <label class="form-check-label" for="subcategoryCheck<?php echo $subcategory_id; ?>"><?php echo $subcategory_fetch['subCategoryName']; ?></label>
                                                                            </div>
                                                                        </li>
                                                                    <?php } if ($subcategory_count) echo "</ul>"; ?>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                    <div class="invalid-feedback">
                                                        Please choose atleast a Sub Category.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="brand">Brand</label>
                                                    <select class="form-select" id="brand" name="brand_id">
                                                        <option value="">Select</option>
                                                        <?php
                                                            $brandsquery = mysqli_query($conn, "SELECT * FROM `brands` WHERE `status` = '1' ORDER BY `brandName` ASC");
                                                            while ($brandfetch = mysqli_fetch_array($brandsquery)) {
                                                                if($_POST['brand_id'] == $brandfetch['ID']) $select = "selected"; else $select = '';
                                                                echo "<option value='".$brandfetch['ID']."' ".$select.">".$brandfetch['brandName']."</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Please choose a Brand.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="vendor">Vendor&nbsp;<?php echo $_SESSION['userType']; ?></label>
                                                    <!-- <select class="form-select" id="vendor" name="vendor_id">
                                                        <?php
                                                            if(in_array($_SESSION['userType'], array('VENDOR', 'SUBADMIN'))){                  
                                                                $vendorId = $_SESSION['vendorId'];                                              
                                                                $vendorquery = mysqli_query($conn, "SELECT * FROM `vendors` WHERE vendorID='$vendor_id' and status=2 ORDER BY `name` ASC");                                                
                
                                                            }else{
                                                                $vendorquery = mysqli_query($conn, "SELECT * FROM `vendors` WHERE status=2 ORDER BY `name` ASC");  
                                                                ?>
                                                                 <option value="">Select</option>                                              
                                                           <?php }
                                                            
                                                            while ($vendorfetch = mysqli_fetch_array($vendorquery)) {
                                                                if($_POST['vendor_id'] == $vendorfetch['vendorID']  ) $select = "selected"; else $select = '';
                                                                echo "<option value='".$vendorfetch['vendorID']."' ".$select.">".$vendorfetch['name']."</option>";
                                                            }
                                                        ?>
                                                    </select> -->
                                                     <select class="form-select" id="vendor" name="vendor_id">
                                                        <option value="">Select</option>
                                                        <?php
                                                            if(in_array($_SESSION['userType'], array('VENDOR', 'SUBADMIN'))){ 
                                                                $vendorId = $_SESSION['vendorId'];
                                                                $userId = $_SESSION['userId'];                            
                                                                $vendorquery = mysqli_query($conn, "SELECT * FROM `vendors` WHERE user_id='$userId' and status=2 ORDER BY `name` ASC");                                                
                                                            }else{
                                                                $vendorquery = mysqli_query($conn, "SELECT * FROM `vendors` WHERE status=2 ORDER BY `name` ASC");  
                                                                ?>
                                                                                                               
                                                           <?php }
                                                            
                                                           while ($vendorfetch = mysqli_fetch_assoc($vendorquery)) {
                                                            ?>
                                                            <option value="<?php echo $vendorfetch['vendorID'];?>" <?php echo $_POST['vendor_id'] == $vendorfetch['vendorID'] ? "selected" : ''?>><?php echo $vendorfetch['name'];?></option>
                                                            <?php
                                                            }
                                                        ?>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Please provide a Vendor.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="pincodes">Pincodes</label>
                                                    <input type="text" class="form-control" id="pincodes" name="pincodes" placeholder="533001,533002" value="<?php echo $_POST['pincodes']; ?>">
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Pincodes.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product_image">Product Image<sup>x1</sup></label>
                                                    <input type="file" class="form-control" id="product_image" name="main_img" accept=".png,.jpg,.jpeg,.gif,.webp">
                                                    <div class="invalid-feedback">
                                                        Please choose a Product Main Image.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="hover_image">Hover Image<sup>x1</sup></label>
                                                    <input type="file" class="form-control" id="hover_image" name="hover_img" accept=".png,.jpg,.jpeg,.gif,.webp">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="image_gallery">Image Gallery<sup>xMultiple</sup> (Please select display order wise)</label>
                                                    <input type="file" multiple="true" class="form-control" id="image_gallery" name="product_images[]" accept=".png,.jpg,.jpeg,.gif,.webp">
                                                    <div class="invalid-feedback">
                                                        Please choose Product Images.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="tags">Tags</label>
                                                    <input type="text" class="form-control" id="tags" name="tags" value="<?php echo $_POST['tags']; ?>">
                                                    <div class="invalid-feedback">
                                                        Please provide Product tags.
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label" for="short_description">Short Description (for displaying on the Quick View)</label>
                                                    <textarea class="form-control" rows="3" id="short_description" name="description_short"><?php echo $_POST['description_short']; ?></textarea>
                                                    <div class="invalid-feedback">
                                                        Please provide valid Short Description.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label" for="snow-editor">Description</label>
                                                    <textarea id="summernote" name="description_desc"><?php echo $_POST['description_desc']; ?></textarea>
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Description.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="rating">Rating</label>
                                                    <input type="number" min="1" step="0.01" class="form-control" id="rating" name="rating" value="<?php echo $_POST['rating']; ?>">
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Rating.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="review">Review</label>
                                                    <input type="text" class="form-control" id="review" name="review" value="<?php echo $_POST['review']; ?>">
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Review.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="cod_availability">COD Availability</label><br />
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" id="cod_yes" name="is_cod_available" value="1" <?php if($_POST['is_cod_available'] == 1) echo "checked"; ?>>
                                                        <label class="form-check-label" for="cod_yes">Yes</label>
                                                    </div>&nbsp;&nbsp;
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" id="cod_no" name="is_cod_available" value="0" <?php if($_POST['is_cod_available'] == 0) echo "checked"; ?>>
                                                        <label class="form-check-label" for="cod_no">No</label>
                                                    </div>
                                                    <div class="invalid-feedback">
                                                        Please choose COD Availability.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="pan_delivery">PAN India Develivery Availability</label><br />
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" id="pan_yes" name="is_pan_india_available" value="1" <?php if($_POST['is_pan_india_available'] == 1) echo "checked"; ?>>
                                                        <label class="form-check-label" for="pan_yes">Yes</label>
                                                    </div>&nbsp;&nbsp;
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" id="pan_no" name="is_pan_india_available" value="0" <?php if($_POST['is_pan_india_available'] == 0) echo "checked"; ?>>
                                                        <label class="form-check-label" for="pan_no">No</label>
                                                    </div>
                                                    <div class="invalid-feedback">
                                                        Please choose PAN India Availability.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if (strlen(trim($id)) && (int)$id && count($_POST) > 0) { ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label text-danger" for="product_publish">Publish</label><br />
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" id="publish_yes" name="status" value="1" <?php if($_POST['status'] == 1) echo "checked"; ?>>
                                                        <label class="form-check-label" for="publish_yes">Yes</label>
                                                    </div>&nbsp;&nbsp;
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" id="publish_no" name="status" value="0" <?php if($_POST['status'] == 0) echo "checked"; ?>>
                                                        <label class="form-check-label" for="publish_no">No</label>
                                                    </div>
                                                    <div class="invalid-feedback">
                                                        Please choose COD Availability.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="row display-none" id="variable_product_data">
                                            <div class="col-md-12">

                                                <!-- Template row for cloning -->
                                                <div class="mb-3 template-row display-none">
                                                    <fieldset>
                                                        <legend>Variable</legend>
                                                        <div class="row">
                                                            <div class="col-md-12 mb-1">
                                                                <button type="button" class="btn btn-sm btn-info right addAttributeBtn">Add Attr.</button>
                                                            </div>

                                                            <div class="dynamicAttributes">
                                                                <div class="row">
                                                                    <div class="col-md-4 attribute display-none">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="mb-3">
                                                                                    <label class="form-label">Name</label>
                                                                                    <input type="text" class="form-control attribute-name" placeholder="Attribute Name">
                                                                                    <div class="invalid-feedback">
                                                                                        Please provide a valid Attr. Name.
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="mb-3">
                                                                                    <label class="form-label">Value</label>
                                                                                    <input type="text" class="form-control attribute-value" placeholder="Attribute Value">
                                                                                    <div class="invalid-feedback">
                                                                                        Please provide a valid Attr. Value.
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="clearfix"></div>

                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">SKU</label>
                                                                <input type="text" class="form-control" name="v_sku[]">
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">Regular Price* (<i class="bi bi-currency-rupee"></i>)</label>
                                                                <input type="number" min="0" step="0.01" class="form-control" name="v_price[]">
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">Sale Price (<i class="bi bi-currency-rupee"></i>)</label>
                                                                <input type="number" min="0" step="0.01" class="form-control" name="v_sale_price[]">
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">Quantity*</label>
                                                                <input type="number" min="0" class="form-control" name="v_quantity[]">
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">Weight (g)</label>
                                                                <input type="number" min="0" step="0.01" class="form-control" name="v_weight[]">
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label class="form-label">Dimensions (cm)</label>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <input type="number" min="0" step="0.01" class="form-control" name="v_length[]" placeholder="Length">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <input type="number" min="0" step="0.01" class="form-control" name="v_width[]" placeholder="Width">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <input type="number" min="0" step="0.01" class="form-control" name="v_height[]" placeholder="Height">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">Image</label>
                                                                <input type="file" class="form-control" name="v_image[0][]" accept=".png,.jpg,.jpeg,.gif,.webp">
                                                                <textarea class="display-none" name="v_preimage[]"></textarea>
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">Tax Status</label>
                                                                <select class="form-select" name="v_tax_status[]">
                                                                    <option value="Taxable">Taxable</option>
                                                                    <option value="Non Taxable">Non Taxable</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">Tax Class</label>
                                                                <input type="number" min="0" step="0.01" class="form-control" name="v_tax[]">
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">COD Charges (<i class="bi bi-currency-rupee"></i>)</label>
                                                                <input type="number" min="0" step="0.01" class="form-control" name="v_cod_charges[]">
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">Shipping Charges (<i class="bi bi-currency-rupee"></i>)</label>
                                                                <input type="number" min="0" step="0.01" class="form-control" name="v_shipping_charges[]">
                                                            </div>

                                                            <!-- Textarea for attributes of this row -->
                                                            <div class="col-md-12 display-none">
                                                                <textarea class="form-control attributesTextarea" rows="5" name="v_attributes[]" readonly></textarea>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>

                                                <div id="dynamicRows">
                                                    <?php
                                                    if (count($_POST['variables']) > 0) {
                                                        foreach ($_POST['variables'] as $key => $variable) { ?>
                                                        <div class="mb-3 variable-main">
                                                            <fieldset>
                                                                <legend>Variable</legend>
                                                                <div class="row variable-row">
                                                                    <div class="col-md-12 mb-1">
                                                                        <button type="button" class="btn btn-sm btn-info right addAttributeBtn">Add Attr.</button>
                                                                    </div>

                                                                    <div class="dynamicAttributes">
                                                                        <div class="row">
                                                                            <?php
                                                                                foreach ($variable['attributes'] as $attrkey => $attribute) { ?>
                                                                                <div class="col-md-4 attribute">
                                                                                    <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <div class="mb-3">
                                                                                                <label class="form-label">Name</label>
                                                                                                <input type="text" class="form-control attribute-name" placeholder="Attribute Name" value="<?php echo $attribute['name']; ?>">
                                                                                                <div class="invalid-feedback">
                                                                                                    Please provide a valid Attr. Name.
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <div class="mb-3">
                                                                                                <label class="form-label">Value</label>
                                                                                                <input type="text" class="form-control attribute-value" placeholder="Attribute Value" value="<?php echo $attribute['value']; ?>">
                                                                                                <div class="invalid-feedback">
                                                                                                    Please provide a valid Attr. Value.
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <?php
                                                                                }
                                                                            ?>
                                                                        </div>
                                                                    </div>

                                                                    <div class="clearfix"></div>

                                                                    <div class="col-md-3 mb-2">
                                                                        <label class="form-label">SKU</label>
                                                                        <input type="text" class="form-control" name="v_sku[]" value="<?php echo $variable['sku']; ?>">
                                                                    </div>
                                                                    <div class="col-md-3 mb-2">
                                                                        <label class="form-label">Regular Price* (<i class="bi bi-currency-rupee"></i>)</label>
                                                                        <input type="number" min="0" step="0.01" class="form-control" name="v_price[]" value="<?php echo $variable['price']; ?>">
                                                                    </div>
                                                                    <div class="col-md-3 mb-2">
                                                                        <label class="form-label">Sale Price (<i class="bi bi-currency-rupee"></i>)</label>
                                                                        <input type="number" min="0" step="0.01" class="form-control" name="v_sale_price[]" value="<?php echo $variable['sale_price']; ?>">
                                                                    </div>
                                                                    <div class="col-md-3 mb-2">
                                                                        <label class="form-label">Quantity*</label>
                                                                        <input type="number" min="0" class="form-control" name="v_quantity[]" value="<?php echo $variable['quantity']; ?>">
                                                                    </div>
                                                                    <div class="col-md-3 mb-2">
                                                                        <label class="form-label">Weight (g)</label>
                                                                        <input type="number" min="0" step="0.01" class="form-control" name="v_weight[]" value="<?php echo $variable['weight']; ?>">
                                                                    </div>
                                                                    <div class="col-md-6 mb-2">
                                                                        <label class="form-label">Dimensions (cm)</label>
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <input type="number" min="0" step="0.01" class="form-control" name="v_length[]" placeholder="Length" value="<?php echo $variable['dimensions']['length']; ?>">
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <input type="number" min="0" step="0.01" class="form-control" name="v_width[]" placeholder="Width" value="<?php echo $variable['dimensions']['width']; ?>">
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <input type="number" min="0" step="0.01" class="form-control" name="v_height[]" placeholder="Height" value="<?php echo $variable['dimensions']['height']; ?>">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 mb-2">
                                                                        <label class="form-label">Image</label>
                                                                        <input type="file" class="form-control" name="v_image[<?php echo $key+1; ?>][]" accept=".png,.jpg,.jpeg,.gif,.webp">
                                                                        <textarea class="display-none" name="v_preimage[]"><?php /*echo htmlspecialchars(json_encode($variable['image'], JSON_PRETTY_PRINT));*/ echo $variable['image']; ?></textarea>
                                                                    </div>
                                                                    <div class="col-md-3 mb-2">
                                                                        <label class="form-label">Tax Status</label>
                                                                        <select class="form-select" name="v_tax_status[]">
                                                                            <option value="Taxable" <?php if($variable['tax_status'] == 'Taxable') echo "selected"; ?>>Taxable</option>
                                                                            <option value="Non Taxable" <?php if($variable['tax_status'] == 'Non Taxable') echo "selected"; ?>>Non Taxable</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3 mb-2">
                                                                        <label class="form-label">Tax Class</label>
                                                                        <input type="number" min="0" step="0.01" class="form-control" name="v_tax[]" value="<?php echo $variable['tax']; ?>">
                                                                    </div>
                                                                    <div class="col-md-3 mb-2">
                                                                        <label class="form-label">COD Charges (<i class="bi bi-currency-rupee"></i>)</label>
                                                                        <input type="number" min="0" step="0.01" class="form-control" name="v_cod_charges[]" value="<?php echo $variable['cod_charges']; ?>">
                                                                    </div>
                                                                    <div class="col-md-3 mb-2">
                                                                        <label class="form-label">Shipping Charges (<i class="bi bi-currency-rupee"></i>)</label>
                                                                        <input type="number" min="0" step="0.01" class="form-control" name="v_shipping_charges[]" value="<?php echo $variable['shipping_charges']; ?>">
                                                                    </div>

                                                                    <!-- Textarea for attributes of this row -->
                                                                    <div class="col-md-12 display-none">
                                                                        <textarea class="form-control attributesTextarea" rows="5" name="v_attributes[]" readonly><?php echo htmlspecialchars(json_encode($variable['attributes'], JSON_PRETTY_PRINT)); ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <?php
                                                        }
                                                    } ?>
                                                </div>

                                                <!-- Button to add new row -->
                                                <button type="button" id="addRowBtn" class="btn btn-sm btn-success mb-3 right">Add Variable</button>
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

    <!-- popper js -->
    <script src="assets/js/popper.min.js"></script>

    <!-- bootstrap js -->
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Summernote js -->
    <script src="assets/vendor/summernote-0.8.18/summernote-bs4.js"></script>

    <!-- variables js -->
    <script src="assets/js/variables.js"></script>

    <script>
        $(document).ready(function() {
            // summernote
            $('#summernote').summernote({ height: 200 });

            // product type handling
            $(function () {
                handleProductType(); //this calls it on load
                $("#product_type").change(handleProductType);
            });

            function handleProductType() {
                var product_type = $("#product_type").val()
                if ( product_type == 'variable' ) {
                    $("#simple_product_data").slideUp("slow");
                    // $("#simple_product_data input,#simple_product_data select,#simple_product_data textarea").prop("required", false);
                    $("#simple_product_data #regular_price").prop("required", false);
                    $("#variable_product_data").slideDown("slow");
                } else {
                    $("#simple_product_data").slideDown("slow");
                    // $("#simple_product_data input,#simple_product_data select,#simple_product_data textarea").prop("required", true);
                    $("#simple_product_data #regular_price").prop("required", true);
                    $("#variable_product_data").slideUp("slow");
                }
            }

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