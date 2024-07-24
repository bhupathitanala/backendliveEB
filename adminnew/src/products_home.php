<?php include 'partials/main.php'; ?>

<head>
    <?php
    $title = "Products";
    include 'partials/title-meta.php';

    // Home Types
    $homeTypes = array();
    $homeTypes[0]['column_name'] = "top_picks";
    $homeTypes[0]['display_name'] = "Top Picks of the Day";

    $homeTypes[1]['column_name'] = "luxury_meets_sustainability";
    $homeTypes[1]['display_name'] = "Luxury Meets Sustainability";

    $homeTypes[2]['column_name'] = "sustainable_clothing";
    $homeTypes[2]['display_name'] = "Sustainable Clothing";

    $homeTypes[3]['column_name'] = "eco_friendly_gifting";
    $homeTypes[3]['display_name'] = "Eco-Friendly gifting";

    $homeTypes[4]['column_name'] = "healthy_snacks";
    $homeTypes[4]['display_name'] = "Healthy Snacks";

    $homeTypes[5]['column_name'] = "home_essentials";
    $homeTypes[5]['display_name'] = "Home Essentials";

    $homeTypes[6]['column_name'] = "bestsellers";
    $homeTypes[6]['display_name'] = "Bestsellers";
    ?>

    <!-- Datatables css -->
    <link href="assets/css/vendor/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/fixedColumns.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/fixedHeader.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />

    <?php include 'partials/head-css.php'; ?>

    <style>
        .toggle-card {
            vertical-align: middle;
        }
        .toggle-card .form-switch {
            padding-left: inherit;
        }
        .toggle-card .form-check-input {
            margin: 0 auto;
        }
        .visibility-hidden {
            visibility: hidden;
            opacity: 0;
        }
        .display-none {
            display: none;
        }
        .text-bold {
            font-weight: 600;
        }
        .form-switch {
            position: relative;
        }
        .form-switch .form-check-input {
            cursor: pointer;
        }
        .form-switch .mdi-spin {
            display: none;
            position: absolute;
            top: -3px;
            left: 14px;
            width: 30px;
            background: rgba(0, 0, 0, 0.4);
            color: #ffff;
            text-align: center;
            border-radius: 10px;
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

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <?php
                    $sub_title = "All Products";
                    $page_title = "Products";
                    include 'partials/page-title.php'; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <form method="GET">
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Type</label>
                                                <select class="form-select" name="home_type" onchange="this.form.submit();">
                                                    <option value="All" <?php if($_GET['home_type']=='All' || $_GET['home_type'] == '') echo 'selected'; ?>>All</option>
                                                    <?php
                                                        foreach ($homeTypes as $key => $homeType) { ?>
                                                            <option value="<?php echo $homeType['column_name']; ?>" <?php if($_GET['home_type']==$homeType['column_name']) echo 'selected'; ?>><?php echo $homeType['display_name']; ?></option>                                                            
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </form>
                                    </div>

                                    <table id="scroll-horizontal-datatable"
                                        class="table table-striped nowrap table-striped w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Product ID</th>
                                                <th>Product Title</th>
                                                <th>Thumbnail</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Product Type</th>
                                                <th>Status</th>
                                                <?php foreach ($homeTypes as $key => $homeType) { echo "<th>".$homeType['display_name']."</th>"; } ?>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                                $home_type = mysqli_real_escape_string($conn, @$_GET['home_type']);
                                                $queryCondition = '';
                                                $columnNames = array_column($homeTypes, 'column_name');

                                                if (strlen(trim($home_type))) {
                                                    if (in_array($home_type, $columnNames)) {
                                                        $queryCondition = " WHERE `$home_type`='1'";
                                                    }
                                                }
                                                // adding backticks to the columns using join
                                                $columnNames = "`".join("`,`", $columnNames)."`";

                                                $query = mysqli_query($conn, "SELECT `ID`,`product_id`,`product_type`,`title`,`main_img`,`quantity`,`price`,`status`, $columnNames FROM `productsnew` a $queryCondition ORDER BY `id` DESC");
                                                $sno = 0;
                                                while ($fetch = mysqli_fetch_array($query)) { ?>
                                                    <tr>
                                                        <td><?php echo ++$sno; ?></td>
                                                        <td><?php echo $fetch['product_id']; ?></td>
                                                        <td><span data-bs-toggle="tooltip"  data-bs-title="<?php echo $fetch['title']; ?>"><?php echo (strlen($fetch['title']) > 20) ? substr($fetch['title'], 0, 20)."..." : $fetch['title']; ?></span><div class="display-none"><?php echo $fetch['title']; ?></div></td>
                                                        <td><img src='./uploads/images/product_images/<?php echo $fetch['main_img']; ?>' height="64" /></td>
                                                        <td><?php echo $fetch['quantity']; ?></td>
                                                        <td><i class="bi bi-currency-rupee"></i><?php echo $fetch['price']." /-"; ?></td>
                                                        <td>
                                                            <?php
                                                                if ($fetch['product_type'] == 'simple') $textColor = "text-primary"; else $textColor = "text-success";
                                                                echo "<label class='".$textColor."'>".ucfirst($fetch['product_type'])."</label>";
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($fetch['status'] == 1) { ?>
                                                            <span class='badge bg-success'>Active</span>
                                                            <?php } else { ?>
                                                            <span class='badge bg-danger'>In Active</span>
                                                            <?php } ?>
                                                        </td>
                                                        <?php
                                                            foreach ($homeTypes as $key => $homeType) { ?>
                                                                <td class="toggle-card">
                                                                    <div class="form-check form-switch">
                                                                        <input type="checkbox" class="form-check-input" value="<?php echo $fetch['ID']."@".$homeType['column_name']; ?>" <?php if($fetch[$homeType['column_name']] == '1') echo 'checked'; ?>>
                                                                        <i class="mdi mdi-spin mdi-loading"></i>
                                                                        <div class="clearfix"></div>
                                                                        <small class="homeTypeUpdateMSG text-bold visibility-hidden">Success</small>
                                                                    </div>
                                                                </td>                                                                
                                                        <?php } ?>
                                                    </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div> <!-- end card body-->
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

    <script>
        $(document).ready(function() {
            $(document).on('click', '.form-check-input', function(e) {
                e.preventDefault();
                const $input = $(this);
                const data = $(this).val();
                const propType = $(this).prop("checked");

                // spinner on
                $(this).next(".mdi-spin").fadeIn(0);

                $.ajax({
                    url: "services/ajax.php",
                    method: "POST",
                    data: { "action": "toggleHomeTypeColumn", "data": data, "updateValue": propType },
                    success: function(res) {
                        // spinner off
                        $input.next(".mdi-spin").fadeOut(0);

                        if (res == 1) {
                            $input.prop("checked", propType);
                            classNameAdd = "text-primary";
                            classNameRemove = "text-danger";
                            msgText = "Success";
                        } else {
                            classNameAdd = "text-danger";
                            classNameRemove = "text-primary";
                            msgText = "Failed";
                        }

                        // msg to show
                        const responseMsg = $input.closest('.form-switch').find('.homeTypeUpdateMSG').first();
                        responseMsg.css('visibility', 'visible').animate({ opacity: 1 });
                        responseMsg.removeClass(classNameRemove);
                        responseMsg.addClass(classNameAdd);
                        responseMsg.text(msgText);

                        setTimeout(() => {
                            responseMsg.animate({ opacity: 0 }, 'slow', function() {
                                $(this).css('visibility', 'hidden');
                            });
                        }, 2000);
                    }
                });
            });
        });
    </script>

</body>

</html>