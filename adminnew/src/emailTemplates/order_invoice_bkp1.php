<!DOCTYPE html>
<html lang="en">
<?php
    require_once('../services/config.php');
    $data = json_decode(file_get_contents("php://input"), true);
    $address = json_decode($data['customer_address'], true);
    $invoice_number = $data['invoice_number']; // Assuming order_ids is an array of integers
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - <?php echo $data['invoice_number']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            /*background-color: #edf1e4;*/
            color: #422D09;
            font-family: "Poppins", sans-serif;
            font-weight: 400;
            font-style: normal;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            /*border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);*/
            font-size: 12px;
            position: relative;
        }
        table td {
            font-size: 12px;
        }
        .container a {
            text-decoration: none;
            color: #422D09;
        }
        .header {
            text-align: center;
            padding: 20px;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 20px;
        }
        .content h2 {
            color: #422D09;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .content p {
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .order-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .order-details th,
        .order-details td {
            border: 1px solid #eeeeee;
            padding: 8px;
            text-align: left;
        }
        .order-details th {
            background-color: #f2f2f2;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #422D09;
            background-color: #edf1e4;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        .footer a {
            color: #468F5E;
            text-decoration: none;
            margin: 0 5px;
        }
        .clearfix {
            content: "";
            display: table;
            clear: both;
        }
        .width-50 {
            width: 50%;
        }
        .pull-left {
            float: left;
        }
        .pull-right {
            float: right;
        }
        .text-center {
            text-align: center;
        }
        .mt-50 {
            margin-top: 50px;
        }
        .fix-bottom {
            width: 95%;
            position: absolute;
            bottom: 0;
        }
        .mb-0 {
            margin-bottom: 0;
        }
        .bg-dark {
            background-color: #000;
            color: #fff;
        }
        .bg-dark th {
            background-color: #000;
            color: #fff;
        }
        .invoice-data div {
            display: inline-block;
            min-width: 130px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="">
            <div class="width-50 pull-left">
                <a href="https://earthbased.store/?v=a83cd41b5721">
                    <img src="https://earthbased.store/static/media/footer_logo.d72cb08204191c52b659.png" alt="EarthBased Logo" width="40%">
                </a>
            </div>
            <div class="width-50 pull-right">
                <p>
                    <b>Earthbased</b><br/>
                    Address Flat B, 37-2-14,<br/>Vijayasindhu Towers,<br/>Market St, Urban Kakinada,<br/>Andhra Pradesh-533005<br/><br/>
                    Email: <a href="mailto:hello@earthbased.store">hello@earthbased.store</a><br/>
                    Phone Number: +91 9646492525<br/>
                    GST: 37AAJFE4559K1ZX
                </p>
            </div>
        </div>
        <div class="clearfix"></div>
        

        <div class="content">
            <h2 class="mb-0">INVOICE</h2>
            <div class="">
                <div class="width-50 pull-left">
                    <p>
                        <?php echo $address['firstName']; ?>,
                        <br />
                        <?php echo $address['address']; ?>,
                        <br />
                        <?php echo $address['city']; ?>,
                        <br />
                        <?php echo $address['pincode']; ?>,
                        <br />
                        <?php echo $address['state']; ?>.
                    </p>
                </div>
                <div class="width-50 pull-right invoice-data">
                    <table border="0" style="width:100%">
                        <tr>
                            <td>Invoice Number:</td>
                            <td><?php echo $data['invoice_number']; ?></td>
                        </tr>
                        <tr>
                            <td>Order Date:</td>
                            <td><?php echo date('d-m-Y',strtotime($data['ordered_date'])); ?></td>
                        </tr>
                        <?php 
                        if($data['payment_type'] === 'cod') { 
                            $data['payment_type'] = 'Cash On Delivery';
                        } ?>
                        <tr>
                            <td>Payment Method:</td>
                            <td><?php echo ucfirst($data['payment_type']); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
            <h3>Order Details</h3>
            <table class="order-details">
                <thead>
                    <tr class="bg-dark">
                        <th>#</th>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th style="width:80px;text-align:right">Price</th>
                    </tr>
                </thead>
                <?php
                    $x=1;
                    
                    $sql = "SELECT `product_title`,`product_qty`,`product_price`,`order_id`,`payment_type`,`shipping_charges`,`cod_charges`,`amount`,`coupon_code` FROM `order_details` WHERE invoice_number='$invoice_number'";
                    //echo $sql;
                    
                    $result = $conn->query($sql);
                    $cnt = $result->num_rows;
                    $subtotal = 0;
                    while ($row = $result->fetch_assoc()) {
                        $subtotal = $subtotal + $row['product_price'];
                        $shipping_charges = $row['shipping_charges'];
                        $cod_charges = $row['cod_charges'];
                        $payment_type = $row['payment_type'];
                        $amount = $row['amount'];
                        ?>
                        <tr>
                            <td><?php echo $x++ ?></td>
                            <td><?php echo $row['order_id'] ?></td>
                            <td><?php echo $row['product_title'] ?></td>
                            <td><?php echo $row['product_qty'] ?></td>
                            <td style="text-align:right">₹ <?php echo $row['product_price'] ?>.00</td>                                                    
                        </tr>
                    <?php  } ?>
                    <tr>
                        <td colspan="4" style="text-align:right">Subtotal</td>
                        <td style="text-align:right">₹ <?php echo $subtotal; ?>.00</td>                                                    
                    </tr>
                    <?php
                    // Assuming $subtotal is the original subtotal amount
                    if($row['coupon_code'] === 'EB15'){
                        // Calculate the discount amount
                        $discount_percentage = 15; // 15% discount
                        $discount = ($subtotal * $discount_percentage) / 100;

                        // Calculate the discounted subtotal
                        $subtotal_after_discount = $subtotal - $discount;
                        ?>
                        <tr>
                            <td colspan="4" style="text-align:right">Coupon Discount (15%)</td>
                            <td style="text-align:right">- ₹ <?php echo $discount; ?>.00</td>                                                    
                        </tr>
                    <?php } ?>                    
                    <tr>
                        <td colspan="4" style="text-align:right">Shipping Charges</td>
                        <td style="text-align:right">₹ <?php echo $shipping_charges; ?></td>                                                    
                    </tr>
                    <?php if($payment_type === 'cod') { ?>
                    <tr>
                        <td colspan="4" style="text-align:right">COD Charges</td>
                        <td style="text-align:right">₹ <?php echo $cod_charges; ?></td>                                                    
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4" style="text-align:right">Total<small>(Including all taxes)</small></td>
                        <td style="text-align:right">₹ <?php echo $amount; ?></td>                                                    
                    </tr>
                </tbody>
            </table>
            
            <div class="clearfix"></div>
            <div class="mt-50"></div>
            <div class="text-center fix-bottom">
                <hr />
                <p>@EarthBased.store. All rights reserved</p>
            </div>
        </div>
    </div>
</body>
</html>