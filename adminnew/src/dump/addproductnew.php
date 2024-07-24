<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Add New Product";
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
                                    <form class="needs-validation" novalidate>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom01">SKU</label>
                                                    <input type="text" class="form-control" id="validationCustom01" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a valid SKU.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom02">Product Title</label>
                                                    <input type="text" class="form-control" id="validationCustom02" required>
                                                    <div class="invalid-feedback">
                                                        Please provide the Product Title.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom03">Stock Levels (Quantity)</label>
                                                    <input type="number" min="0" class="form-control" id="validationCustom03" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Quantity.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom04">Regular Price</label>
                                                    <input type="number" min="0" class="form-control" id="validationCustom04" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Price.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom05">Sales Price (Discounted Price)</label>
                                                    <input type="number" min="0" class="form-control" id="validationCustom05" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Discount / Sales price.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom06">Tax Classes</label>
                                                    <input type="number" min="0" class="form-control" id="validationCustom06" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Tax classes.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom07">Dimensions of the Product (Length, width, height)</label>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control" id="validationCustom0701" placeholder="Length" required>
                                                            <div class="invalid-feedback">
                                                                Please provide a valid Product Length.
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control" id="validationCustom0702" placeholder="Width" required>
                                                            <div class="invalid-feedback">
                                                                Please provide a valid Product Width.
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control" id="validationCustom0703" placeholder="Height" required>
                                                            <div class="invalid-feedback">
                                                                Please provide a valid Product Height.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom08">Pincodes</label>
                                                    <input type="text" class="form-control" id="validationCustom08" placeholder="533001,533002" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Pincodes.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom09">Product Image<sup>x1</sup></label>
                                                    <input type="file" class="form-control" id="validationCustom09" required accept=".png,.jpg,.jpeg,.gif">
                                                    <div class="invalid-feedback">
                                                        Please choose a Product Main Image.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom10">Hover Image<sup>x1</sup></label>
                                                    <input type="file" class="form-control" id="validationCustom10" accept=".png,.jpg,.jpeg,.gif">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom11">Image Gallery<sup>xMultiple</sup> (Please select display order wise)</label>
                                                    <input type="file" multiple="true" class="form-control" id="validationCustom11" required accept=".png,.jpg,.jpeg,.gif">
                                                    <div class="invalid-feedback">
                                                        Please choose Product Images.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom12">Categories</label>
                                                    <input type="text" class="form-control" id="validationCustom12" required>
                                                    <div class="invalid-feedback">
                                                        Please choose a Category.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom13">Tags</label>
                                                    <input type="text" class="form-control" id="validationCustom13" required>
                                                    <div class="invalid-feedback">
                                                        Please provide Product tags.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom14">Brand</label>
                                                    <input type="text" class="form-control" id="validationCustom14" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a Brand.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom15">Vendor</label>
                                                    <input type="text" class="form-control" id="validationCustom15" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a Vendor.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom16">COD Fee</label>
                                                    <input type="text" class="form-control" id="validationCustom16" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a COD Fee.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom17">Shipping Charges</label>
                                                    <input type="text" class="form-control" id="validationCustom17" required>
                                                    <div class="invalid-feedback">
                                                        Please provide Shipping Charges.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom18">Short Description (for displaying on the Quick View)</label>
                                                    <input type="text" class="form-control" id="validationCustom18" required>
                                                    <div class="invalid-feedback">
                                                        Please provide valid Short Description.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom19">Description</label>
                                                    <div id="editor"></div>
                                                    <!-- <input type="text" class="form-control" id="validationCustom19" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Description.
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom20">Rating</label>
                                                    <input type="text" class="form-control" id="validationCustom20" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Rating.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom21">Reviews</label>
                                                    <input type="text" class="form-control" id="validationCustom21" required>
                                                    <div class="invalid-feedback">
                                                        Please provide a valid Review.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom22">COD Availability</label>
                                                    <select class="form-control" id="validationCustom22" required>
                                                        <option value="">Select</option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no">No</option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Please choose COD Availability.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="validationCustom23">PAN India Develivery Availability</label>
                                                    <select class="form-control" id="validationCustom23" required>
                                                        <option value="">Select</option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no">No</option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Please choose PAN India Availability.
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-12">  
                                            <!-- Button to add new row -->
                                            <button type="button" id="addRowBtn" class="btn btn-sm btn-success mb-3 right">+</button>

                                            <!-- Template row for cloning -->
                                            <div class="row mb-3 template-row" style="display: none;">
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" name="mrp[]" placeholder="MRP">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" name="price[]" placeholder="Price">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" name="qty[]" placeholder="Qty">
                                                </div>
                                                <div class="col-md-2">
                                                    <?php
                                                        // Fetch data from the sizes table
                                                        $sql = "SELECT * FROM sizes where status=1";
                                                        $result = mysqli_query($conn, $sql);

                                                        // Check if there are any rows returned
                                                        if (mysqli_num_rows($result) > 0) {
                                                            // Start select dropdown
                                                            echo '<select class="form-control" name="size[]" id="validationCustom24" required>';
                                                            echo '<option value="">Select Size</option>';

                                                            // Loop through each row of data
                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                                // Output an option for each size
                                                                echo '<option value="' . $row['id'] . '">' . $row['size'] . '</option>';
                                                            }

                                                            // End select dropdown
                                                            echo '</select>';
                                                        }
                                                    ?>
                                                    <!-- <input type="text" class="form-control" name="size[]" placeholder="Size"> -->
                                                </div>
                                                <div class="col-md-2">
                                                    <?php
                                                        // Fetch data from the sizes table
                                                        $sql = "SELECT * FROM colors where status=1";
                                                        $result = mysqli_query($conn, $sql);

                                                        // Check if there are any rows returned
                                                        if (mysqli_num_rows($result) > 0) {
                                                            // Start select dropdown
                                                            echo '<select class="form-control" name="color_name[]" id="validationCustom25" required>';
                                                            echo '<option value="">Select Color</option>';

                                                            // Loop through each row of data
                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                                // Output an option for each size
                                                                echo '<option value="' . $row['id'] . '">' . $row['color_name'] . '</option>';
                                                            }

                                                            // End select dropdown
                                                            echo '</select>';
                                                        }
                                                    ?>
                                                    <!-- <input type="text" class="form-control" name="size[]" placeholder="Size"> -->
                                                </div>
                                                <!-- <div class="col-md-2">
                                                    <input type="text" class="form-control" name="color[]" placeholder="Color">
                                                </div> -->
                                                <div class="col-md-2">
                                                    <input type="file" class="form-control" name="image[]" accept=".png,.jpg,.jpeg,.gif">
                                                </div>
                                            </div>

                                            <div id="dynamicRows"></div>

                                            </div>



                                        </div>
                                        <button class="btn btn-primary" type="submit">Submit</button>
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
                ckfinder: {
                    uploadUrl: 'http://admin.earthbased.in/api/CKEditor-upload'
                }
            })
            .then( editor => {
                window.editor = editor;
            })
            .catch( err => {
                console.error( err.stack );
            });
    </script>
    <script>
    document.getElementById('addRowBtn').addEventListener('click', function() {
        var template = document.querySelector('.template-row');
        var clone = template.cloneNode(true);
        clone.classList.remove('template-row');
        clone.style.display = 'flex'; // Show the cloned row
        document.getElementById('dynamicRows').appendChild(clone);
    });
</script>

</body>

</html>