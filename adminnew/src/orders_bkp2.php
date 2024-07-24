<?php include 'partials/main.php'; ?>

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
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.4.0/css/all.min.css" integrity="sha512-eBNnVs5xPOVglLWDGXyZnZZ2K2ixXhR/3aECgCpFnW2dGCd/yiqXZ6fcB3BubeA91kM6NX234b6Wrah8RiYAPA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php include 'partials/head-css.php'; ?>
    <style>
        /* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
.modal-content {
    background-color: #fefefe;
    margin-left: 320px;
    padding: 18px;
    border: 1px solid #888;
    width: 60%;
    margin-top: 77px;
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    text-align: right;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
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
                                                <th>Order ID</th>
                                                <th>Invoice No</th>
                                                <th>Order Date</th>
                                                <th><span style="color: grey;">Payment</span><br/>Status</th>
                                                <th>VIew Details</th>
                                                <th>Mode</th>
                                                <th>Amount</th>
                                                <th><span>Customer</span><br/>Name</th>
                                                <th>Customer Email</th>
                                                <th>Vendor Email</th>
                                                <th>Delivery Status</th>
                                            </tr>
                                        </thead>
                                            <?php
                                                $query = mysqli_query($conn, "SELECT *,(SELECT `email` FROM `admin_users` WHERE id=(SELECT user_id from vendors where vendorID=(SELECT `vendor_id` FROM `productsnew` WHERE id=a.`product_id`))) AS `vendor_email` FROM `order_details` a WHERE 1 ORDER BY `id` DESC;");
                                                // $query = mysqli_query($conn, "SELECT *,(SELECT `email` FROM `admin_users` WHERE id=(SELECT user_id from vendors where vendorID=(SELECT `vendor_id` FROM `productsnew` WHERE id=a.`product_id`))) AS `vendor_email` FROM `order_details` a WHERE a.order_id='EBOR000413' ORDER BY `id` DESC;");
                                                $sno = 0;
                                                while ($fetch = mysqli_fetch_array($query)) { ?>
                                                    <tr>
                                                        <td><?php echo ++$sno; ?></td>
                                                        <td><?php echo $fetch['order_id']; ?></td>
                                                        <td class="invoice_number"><?php echo $fetch['invoice_number'] ?></td>
                                                        <td><?php echo $fetch['order_placed_date']; ?></td>
                                                        <td><?php echo $fetch['payment_status']; ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary view-btn" data-row-data="<?php echo htmlentities(json_encode($fetch)); ?>"><i class="fa fa-eye"></i></button>
                                                        </td>
                                                        <td><?php echo $fetch['payment_mode']; ?></td>
                                                        <td><i class="bi bi-currency-rupee"></i><?php echo $fetch['amount']." /-"; ?></td>
                                                        <td><?php echo $fetch['customer_name']; ?></td>
                                                        <td class="userEmailId">
                                                            <?php 
                                                                echo $fetch['customer_email'];
                                                                $Address = json_decode($fetch['customer_address'], true);

                                                                if ($Address['email'] != $fetch['customer_email']) {
                                                                    echo ", ".$Address['email'];
                                                                }
                                                            ?>
                                                        </td>
                                                        <td class="vendor_email"><?php echo $fetch['vendor_email'] ?></td>
                                                        <td>
                                                            <?php if ($fetch['delivered_status']==1) { ?>
                                                                <label class="badge" style="background: green;">Delivered</label>
                                                            <?php } else if($fetch['shipped_status']==1) { ?>
                                                                <label class="statusBadge badge" style="background:#004444">Shipped</label>
                                                                <button class="btn btn-info btn-sm changeStatus"><i class="ri-edit-2-line"></i></button>
                                                                
                                                                <select class="form-select orderStatus" hidden>
                                                                    <option value="">Select</option>
                                                                    <option value="Delivered">Delivered</option>
                                                                </select>
                                                                <div class="d-flex jusify-content-between pt-2" style="display: flex;justify-content: space-between;">
                                                                    <button class="btn btn-success btn-sm btnSave" value="<?php echo $fetch['order_id'] ?>" hidden><i class="ri-check-line"></i></button>
                                                                    <button class="btn btn-danger btn-sm btnClose" hidden><i class="ri-close-line"></i></button>
                                                                </div>
                                                            <?php } else if($fetch['confirmed_status']==1) { ?>
                                                                <label class="statusBadge badge" style="background:#0D6EFD">Confirmed</label>
                                                                <button class="btn btn-info btn-sm changeStatus"><i class="ri-edit-2-line"></i></button>
                                                                
                                                                <select class="form-select orderStatus" hidden>
                                                                    <option value="">Select</option>
                                                                    <option value="Shipped">Shipped</option>
                                                                </select>
                                                                <div class="d-flex jusify-content-between pt-2" style="display: flex;justify-content: space-between;">
                                                                    <button class="btn btn-success btn-sm btnSave" value="<?php echo $fetch['order_id'] ?>" hidden><i class="ri-check-line"></i></button>
                                                                    <button class="btn btn-danger btn-sm btnClose" hidden><i class="ri-close-line"></i></button>
                                                                </div>
                                                            <?php } else{ ?>
                                                                <label class="statusBadge badge bg-info">Ordered</label>
                                                                <button class="btn btn-info btn-sm changeStatus"><i class="ri-edit-2-line"></i></button>

                                                                <select class="form-select orderStatus" hidden>
                                                                    <option value="">Select</option>
                                                                    <option value="Confirmed">Confirmed</option>
                                                                </select>
                                                                <div class="d-flex jusify-content-between pt-2" style="display: flex;justify-content: space-between;">
                                                                    <button class="btn btn-success btn-sm btnSave" value="<?php echo $fetch['order_id'] ?>" hidden><i class="ri-check-line"></i></button>
                                                                    <button class="btn btn-danger btn-sm btnClose" hidden><i class="ri-close-line"></i></button>
                                                                </div>
                                                            <?php } ?>
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

        <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modalContent"></div>
    </div>
</div>


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
            
            $(document).on('keydown', function(event) {
                if (event.key === "Escape" || event.keyCode === 27) {
                    $(".changeStatus").css("display","inline")        
                    $(".statusBadge").css("display","inline")
                    $(".orderStatus").attr("hidden",true)
                    $(".btnSave").attr("hidden",true)
                    $(".btnClose").attr("hidden",true)
                }
            });

        


            $(document).on('click', '.changeStatus', function(e) {
                var thisBtn = $(this);
                $(".changeStatus").css("display","inline")
                $(this).css("display","none")

                var badge = thisBtn.closest('tr').find('.statusBadge').first();
                $('.statusBadge').css("display","inline")
                badge.css("display","none")

                var orderStatus = thisBtn.closest('tr').find('.orderStatus').first();
                $('.orderStatus').attr("hidden",true)
                orderStatus.attr("hidden",false)

                var btnSave = thisBtn.closest('tr').find('.btnSave').first();
                $('.btnSave').attr("hidden",true)
                btnSave.attr("hidden",false)

                var btnClose = thisBtn.closest('tr').find('.btnClose').first();
                $('.btnClose').attr("hidden",true)
                btnClose.attr("hidden",false)
            })


            $(document).on('click', '.btnClose', function(e) {
                $(".changeStatus").css("display","inline")        
                $(".statusBadge").css("display","inline")
                $(".orderStatus").attr("hidden",true)
                $(".btnSave").attr("hidden",true)
                $(".btnClose").attr("hidden",true)
            })

            $(document).on('click', '.btnSave', function(e) {
                var thisBtn = $(this);
                var oid = thisBtn.val();

                var selectInput = thisBtn.closest('tr').find('.orderStatus').first();
                var status = $.trim(selectInput.val());
                var adminEmail = 'hello@earthbased.store';
                var vendorEmail = $.trim(thisBtn.closest('tr').find('.vendor_email').first().text());
                var invoice_number = $.trim(thisBtn.closest('tr').find('.invoice_number').first().text());//invoice_number
                var userType = '<?php echo $_SESSION['userType']; ?>';
                var userEmail = $.trim(thisBtn.closest('tr').find('.userEmailId').first().text());
                var statusBadge = thisBtn.closest('tr').find('.statusBadge').first();

                if (status!="") {
                    selectInput.css("border", "1px solid #dee2e6")
                    thisBtn.html('<i class="fas fa-spinner fa-pulse"></i>')
                    thisBtn.prop("disabled", true);


                    $.ajax({
                        url: "http://3.7.47.11:3001/api/order/orderemails",
                        contentType: "application/json",
                        data: JSON.stringify({
                            "orderId": oid,
                            "status": status,
                            "adminEmail": adminEmail,
                            "vendorEmail": vendorEmail,
                            "invoice_number": invoice_number,
                            // "vendorEmail":'Sreenath@earthbased.store',
                            "userEmail": userEmail,
                            "userType": userType
                        }),
                        method: "POST",
                        success: function(response, textStatus, xhr) {
                            console.log(xhr.status)
                            if (xhr.status == 200) {
                                selectInput.html("")
                                statusBadge.text(status)
                                if (status == "Confirmed") {
                                    statusBadge.removeClass("bg-info")
                                    statusBadge.css("background","#0D6EFD");
                                    selectInput.html('<option value="">Select</option><option value="Shipped">Shipped</option>')
                                } else if (status == "Shipped") {
                                    statusBadge.css("background", "#004444");
                                    selectInput.html('<option value="">Select</option><option value="Delivered">Delivered</option>')
                                } else if (status == "Delivered") {
                                    statusBadge.css("background", "#008000");
                                }
                            }

                            alert(response.message);
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred: ' + error);
                        },
                        complete: function() {
                            if (status != "Delivered") {
                                $(".changeStatus").css("display", "inline");
                            }
                            $(".statusBadge").css("display", "inline");
                            $(".orderStatus").attr("hidden", true);
                            $(".btnSave").attr("hidden", true);
                            $(".btnClose").attr("hidden", true);
                            thisBtn.html('<i class="ri-check-line"></i>');
                            thisBtn.prop("disabled", false);
                        }
                    })
                }else{
                    selectInput.css("border", "1px solid red")
                }
            })


        });

        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var viewButtons = document.querySelectorAll(".view-btn");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal
        viewButtons.forEach(function(btn) {
            btn.onclick = function() {
                var rowData = JSON.parse(this.dataset.rowData);
                fetchOrderDetails(rowData);
            }
        });

        // Function to fetch order details and display in modal
        function fetchOrderDetails(rowData) {
            console.log(rowData)
            // Construct modal content using rowData
            var modalContent = `
                <div class="card-body">
                <h2>Order Details</h2>
                <table class="table">
                    <tr>
                        <td>Invoice Number</td>
                        <td>${rowData.invoice_number}</td>
                        <td>Order ID</td>
                        <td>${rowData.order_id}</td>
                    </tr>
                    <tr>
                        <td>Ordered Date</td>
                        <td>${rowData.order_placed_date}</td>
                        <td>Product Title</td>
                        <td>${rowData.product_title}</td>
                    </tr>
                    <tr>
                        <td>Product Quantity</td>
                        <td>${rowData.product_qty}</td>
                        <td>Product Price</td>
                        <td>${rowData.product_price}</td>
                    </tr>
                    <tr>
                        <td>Payment Status</td>
                        <td>${rowData.payment_status}</td>
                        <td>Product Type</td>
                        <td>${rowData.payment_mode}</td>
                    </tr>
                    <tr>
                        <td>Customer Name</td>
                        <td>${rowData.customer_name}</td>
                        <td>Customer Email</td>
                        <td>${rowData.customer_email}</td>
                    </tr>
                    <tr>
                        <td>Customer Contact</td>
                        <td>${rowData.customer_contact}</td>
                        <td>Total Amount</td>
                        <td>${rowData.amount}</td>
                    </tr>
                    
                </table>
                </div>
            `;

            // Display order details in modal
            document.getElementById("modalContent").innerHTML = modalContent;

            // Display the modal
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

    </script>
</body>

</html>