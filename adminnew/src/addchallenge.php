<?php include 'partials/main.php'; ?>
<?php
$page_title = "Add New Challenge";
$title = "Add New Challenge";
$id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));
$img_urls_str = "";
if (strlen(trim($id))>0) {
    $title = "Edit Challenge";
    $page_title = "Edit Challenge";
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
        $title = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
        $subTitle = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'subTitle', FILTER_SANITIZE_STRING));
        $smallDescription = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'smallDescription', FILTER_SANITIZE_STRING));
        $price = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING));
        $poster = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'poster', FILTER_SANITIZE_STRING));
        $startDate = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'startDate', FILTER_SANITIZE_STRING));
        $endDate = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'endDate', FILTER_SANITIZE_STRING));
        $timeSlots = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'timeSlots'));
        $description = mysqli_real_escape_string($conn, filter_input(INPUT_POST, 'description'));
        if (isset($_POST['submit'])) {
            // Sanitize and escape input data
            $added_date = date("Y-m-d H:i:s");
            $added_by = $_SESSION['userId'];
            $timeSlots = json_decode($timeSlots);

            echo $query = "INSERT INTO `challenges` SET `title`='$title',`subTitle`='$subTitle',`smallDescription`='$smallDescription',`price`='$price',`poster`='$poster',`startDate`='$startDate',`endDate`='$endDate',`timeSlots`='$timeSlots',`description`='$description',`added_date`='$added_date',`added_by`='$added_by'";

            // INSERT INTO `challenges` SET `title`='Heal Yourself Challenge',`subTitle`='',`smallDescription`='Learn 9 steps to heal your chronic lifestyle diseases following the Satvic Healing Plan.',`price`='990',`poster`='',`startDate`='2024-06-23T09:00',`endDate`='2024-06-29T16:00',`timeSlots`='',`description`=''

        }
        ?>


<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <?php
            $sub_title = "Challenges";
            include 'partials/page-title.php'; ?>

            <div class="row justify-content-center">
                <div class="col-9">
                    <div class="card">
                        <div class="card-body">
                            <form class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label>Challenge Title</label><b>*</b>
                                    <input type="text" name="title" class="form-control" required value="<?php echo $title ?>">
                                </div>


                                <div class="mb-3">
                                    <label>Sub Title</label><b>*</b>
                                    <input type="text" name="subTitle" class="form-control" required value="<?php echo $subTitle ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Small Description <small>(2 Lines)</small></label><b>*</b>
                                    <textarea class="form-control" rows="2" name="smallDescription"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label>Price</label><b>*</b>
                                    <input type="number" name="price" class="form-control" required value="<?php echo $price ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Poster</label><b>*</b>
                                    <input type="file" name="poster" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label>Starts From</label><b>*</b>
                                    <input type="datetime-local" name="startDate" class="form-control" value="<?php echo $startDate ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Ends On</label><b>*</b>
                                    <input type="datetime-local" name="endDate" class="form-control" value="<?php echo $endDate ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Time Slots</label>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label>Slot-1</label>
                                            <div class="row ">
                                                <div class="col-12 col-md-6">
                                                    <label>From</label>
                                                    <input type="time" name="timeSlots[]" class="form-control" value="<?php echo $time ?>">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>To</label>
                                                    <input type="time" name="timeSlots[]" class="form-control" value="<?php echo $time ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 mb-3">
                                            <label>Slot-2</label>
                                            <div class="row ">
                                                <div class="col-12 col-md-6">
                                                    <label>From</label>
                                                    <input type="time" name="timeSlots[]" class="form-control" value="<?php echo $time ?>">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>To</label>
                                                    <input type="time" name="timeSlots[]" class="form-control" value="<?php echo $time ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 mb-3">
                                            <label>Slot-3</label>
                                            <div class="row ">
                                                <div class="col-12 col-md-6">
                                                    <label>From</label>
                                                    <input type="time" name="timeSlots[]" class="form-control" value="<?php echo $time ?>">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>To</label>
                                                    <input type="time" name="timeSlots[]" class="form-control" value="<?php echo $time ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 mb-3">
                                            <label>Slot-4</label>
                                            <div class="row ">
                                                <div class="col-12 col-md-6">
                                                    <label>From</label>
                                                    <input type="time" name="timeSlots[]" class="form-control" value="<?php echo $time ?>">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label>To</label>
                                                    <input type="time" name="timeSlots[]" class="form-control" value="<?php echo $time ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label>Description</label><b>*</b>
                                    <textarea class="form-control" id="summernote" name="description" required><?php echo $description ?></textarea>
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