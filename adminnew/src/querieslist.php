<?php include 'partials/main.php'; ?>

<?php
$id = $conn->real_escape_string(filter_input(INPUT_GET, 'id'));

if (strlen(trim($id))>0) {
    $sql = "UPDATE `queries` SET `status`=0 WHERE `id`='$id'";

    if ($conn->query($sql)) {
        echo "<script>alert('Query Deleted');location.href='querieslist'</script>";
    }
}
?>
<head>
    <?php
    $title = "Orders";
    include 'partials/title-meta.php'; ?>

    <!-- Datatables css -->
    <link href="assets/css/vendor/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/fixedColumns.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/fixedHeader.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />

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
                    $sub_title = "All Orders";
                    $page_title = "Orders";
                    include 'partials/page-title.php'; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <!-- <div class="card-header">
                                    <h4 class="header-title">Effortless Product Navigation</h4>
                                    <p class="text-muted mb-0">
                                        Effortlessly navigate through diverse product sections such as Baby Care, Beauty, and Food with our intuitive category system.
                                    </p>
                                </div> -->
                                <div class="card-body">

                                    <table id="scroll-horizontal-datatable"
                                        class="table table-striped nowrap table-striped w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Query</th>
                                                <th>Reply</th>
                                                <th>Query Posted on</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                            <?php
                                                $query = mysqli_query($conn, "SELECT * FROM `queries` WHERE description!='' and type='nutritionist' and status=1 ORDER BY `id` DESC");
                                                $sno = 0;
                                                while ($fetch = mysqli_fetch_array($query)) { ?>
                                                    <tr>
                                                        <td><?php echo ++$sno; ?></td>
                                                        <td><?php echo $fetch['customerName']; ?></td>
                                                        <td><?php echo $fetch['email']; ?></td>
                                                        <td><?php echo $fetch['question']; ?></td>
                                                        <td style="width: 40% !important;white-space:normal;"><?php echo $fetch['description']; ?></td>
                                                        <td><?php echo $fetch['created_on']; ?></td>
                                                        <td>
                                                            <a class="btn btn-sm btn-outline-primary rounded-pill" href="replyQuery?id=<?php echo $fetch['id'] ?>&backTo=querieslist">Edit</a>
                                                            <a class="btn btn-sm btn-outline-danger rounded-pill" href="?id=<?php echo $fetch['id'] ?>" onclick="return confirm('Are you sure to delete this Brand')">Delete</a>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            ?>
                                        <tbody>
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
            $('.changeStatus').each(function(i){
                $(this).click(function() {
                    $(".changeStatus").css("display","inline")
                    $(this).css("display","none")
                    $(".statusBadge").css("display","inline")
                    $(".statusBadge").eq(i).css("display","none")
                    $(".orderStatus").attr("hidden",true)
                    $(".orderStatus").eq(i).attr("hidden",false)

                    $(".btnSave").attr("hidden",true)
                    $(".btnSave").eq(i).attr("hidden",false)

                    $(".btnClose").attr("hidden",true)
                    $(".btnClose").eq(i).attr("hidden",false)
                })
            })

            $(".btnSave").each(function(j){
                $(this).click(function(){
                    var oid  = $(this).val()
                    var status = $(".orderStatus").eq(j).val();

                    alert(oid + ' ---- ' + status)

                    $.ajax({
                        url:"http://65.1.233.19:3001/api/emails/ordermeails/",
                        data: {"orderId":oid, "status":status},
                        method:"post",
                        success:function(response) {
                            alert(response)
                        }
                    })
                })
            })
        })
    </script>
</body>

</html>