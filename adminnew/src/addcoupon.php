<?php include 'partials/main.php'; ?>
<?php
$page_title = "Add New Coupon";
$title = "Add New Coupon";
$id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));
$img_urls_str = "";
if (strlen(trim($id))>0) {
    $title = "Edit Coupon";
    $page_title = "Edit Coupon";
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
            $code = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING));
            $type = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING));
            $note = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'note', FILTER_SANITIZE_STRING));
            $amount = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_STRING));
            $purchaseAmount = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'purchaseAmount', FILTER_SANITIZE_STRING));
            $validity_from = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'validity_from', FILTER_SANITIZE_STRING));
            $validity_to = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'validity_to', FILTER_SANITIZE_STRING));
            $usageLimit = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'usageLimit', FILTER_SANITIZE_STRING));
            $description = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'description'));

            $category = $_POST['category'] ? join(",", $_POST['category']) : '';
            $subcategory = $_POST['subcategory'] ? join(",", $_POST['subcategory']) : '';
            $brands = $_POST['brands'] ? join(",", $_POST['brands']) : '';
            $products = $_POST['products'] ? join(",", $_POST['products']) : '';
            $purchaseAmount = strlen(trim($purchaseAmount))>0 ? $purchaseAmount : 0; 
            $usageLimit = strlen(trim($usageLimit))>0 ? $usageLimit : 0; 
            $validity_from = strlen(trim($validity_from))>0 ? $validity_from : "NULL";
            $validity_to = strlen(trim($validity_to))>0 ? $validity_to : "NULL";
            $added_date = date("Y-m-d H:i:s");
            $Applied_type = "All";

            if ($category || $subcategory || $brands || $products) {
                $Applied_type = "Specific";
            }


            $query = "INSERT INTO `coupons` SET `code`='$code', `type`='$type', `note`='$note', `brands`='$brands', `categories`='$category', `subCategories`='$subcategory', `products`='$products', `amount`='$amount', `purchaseAmount`='$purchaseAmount', `validity_from`='$validity_from', `validity_to`='$validity_to', `usageLimit`='$usageLimit', `description`='$description', `applied_for`='$Applied_type', added_by='$_SESSION[userId]', added_date='$added_date'";

            if ($conn->query($query)) {
                echo "<script>alert('Coupon Added');location.href=''</script>";
            }else{
                echo "<script>alert('failed to add')</script>";
            }
        }
        ?>


<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <?php
            $sub_title = "Coupons";
            include 'partials/page-title.php'; ?>

            <div class="row justify-content-center">
                <div class="col-9">
                    <div class="card">
                        <div class="card-body">
                            <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label>Coupon Code</label><b>*</b>
                                    <input type="text" name="code" class="form-control" required value="<?php echo $code ?>">
                                </div>


                                <div class="mb-3">
                                    <label>Title</label><b>*</b>
                                    <input type="text" name="note" class="form-control" required value="<?php echo $note ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Coupon Type</label><b>*</b>
                                    <select class="form-select" name="type" required>
                                        <option value="Flat">Flat</option>
                                        <option value="Percentage">Percentage</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label>Value</label><b>*</b>
                                    <input type="number" name="amount" class="form-control" >
                                </div>

                                <div class="mb-3">
                                    <label>Minimum Purchase</label>
                                    <input type="number" name="purchaseAmount" class="form-control" >
                                </div>

                                
                                <div class="mb-3 checkbox-dropdown">
                                    <label class="form-label" for="brands">Brands</label><br />

                                    <div class="btn-group">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                            Select
                                        </button>
                                        <ul class="dropdown-menu" data-simplebar>
                                            <?php
                                            $category_query = mysqli_query($conn, "SELECT * FROM `brands` ORDER BY `brandName` ASC");
                                            while ($brand_fetch = mysqli_fetch_array($category_query)) {
                                                $brand_id = $brand_fetch['ID'];
                                                ?>
                                                <li class="dropdown-item">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="brandCheck<?php echo $brand_id; ?>" name="brands[]" value="<?php echo $brand_id; ?>" >
                                                        <label class="form-check-label" for="brandCheck<?php echo $brand_id; ?>"><?php echo $brand_fetch['brandName']; ?></label>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>

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
                                                        <input type="checkbox" class="form-check-input" id="categoryCheck<?php echo $category_id; ?>" name="category[]" value="<?php echo $category_id; ?>" >
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
                                                                <input type="checkbox" class="form-check-input subcategoryCheck<?php echo $category_id; ?>" id="subcategoryCheck<?php echo $subcategory_id; ?>" name="subcategory[]" value="<?php echo $subcategory_id; ?>" >
                                                                <label class="form-check-label" for="subcategoryCheck<?php echo $subcategory_id; ?>"><?php echo $subcategory_fetch['subCategoryName']; ?></label>
                                                            </div>
                                                        </li>
                                                    <?php } if ($subcategory_count) echo "</ul>"; ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>


                                <div class="mb-3 checkbox-dropdown">
                                    <label class="form-label" for="products">Products</label><br />

                                    <div class="btn-group">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                            Select
                                        </button>
                                        <ul class="dropdown-menu" data-simplebar>
                                            <?php
                                            $products_query = mysqli_query($conn, "SELECT `ID`, `product_id`, `title` FROM `productsnew` ORDER BY `category`,subcategory,title ASC;");
                                            while ($product_fetch = mysqli_fetch_array($products_query)) {
                                                $product_id = $product_fetch['ID'];
                                                ?>
                                                <li class="dropdown-item">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="productCheck<?php echo $product_id; ?>" name="products[]" value="<?php echo $product_id; ?>" >
                                                        <label class="form-check-label" for="productCheck<?php echo $product_id; ?>"><?php echo $product_fetch['title']; ?> (<small><?php echo $product_fetch['product_id']; ?></small>)</label>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Validity From</label>
                                    <input type="date" name="validity_from" class="form-control" value="<?php echo $validity_from ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Validity To</label>
                                    <input type="date" name="validity_to" class="form-control" value="<?php echo $validity_to ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Usage Limit</label>
                                    <input type="text" name="usageLimit" class="form-control" value="<?php echo $usageLimit ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Description</label><b>*</b>
                                    <textarea class="form-control" id="summernote" name="description" required><?php echo $description ?></textarea>
                                </div>


                                <!-- <div class="mb-3">
                                    <label class="form-label" for="images">Images</label>
                                    <input type="file" multiple class="form-control" name="images[]" accept=".png,.jpg,.jpeg,.gif.webp,.avif,.svg">
                                </div> -->
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
        });
    </script>

</body>
</html>