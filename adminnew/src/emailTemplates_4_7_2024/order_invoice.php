<!DOCTYPE html>
<html lang="en">
<?php
    require_once('../services/config.php');
    $data = json_decode(file_get_contents("php://input"), true);
    $address = json_decode($data['customer_address'], true);
    $order_id = $data['order_id']; // Assuming order_ids is an array of integers
    $invoice_number = $data['invoice_number']; // Assuming order_ids is an array of integers
    $productIdsArray = explode(',', $data['productids']);

    // Convert each element in the array to an integer
    $productIdsArray = array_map('intval', $productIdsArray);
                        
    $sql1 = "SELECT a.order_id,a.invoice_number,date(ordered_date) as ordered_date,a.product_id,a.customer_name,a.customer_email,a.customer_contact, b.vendor_id,c.*,d.email,d.mobile FROM `order_details` a join productsnew b on a.product_id=b.ID join vendors c on c.vendorID=b.vendor_id join admin_users d on d.ID=c.user_id WHERE invoice_number='$invoice_number' AND order_id='$order_id' LIMIT 1";
    //SELECT a.order_id,a.invoice_number,a.product_id,b.vendor_id,c.*  FROM `order_details` a join productsnew b on a.product_id=b.ID join vendors c on c.vendorID=b.vendor_id WHERE invoice_number='$invoice_number' AND order_id='$order_id' LIMIT 1;
    //echo $sql;
    
    $result1 = $conn->query($sql1);
    $row1 = $result1->fetch_assoc();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - <?php echo $order_id; ?></title>
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
        table td, th {
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
                    <b>Sold By</b><br/>
                    Vendor Name: <?php echo $row1['name'];?><br/>
                    Address: <?php echo $row1['address'];?><br/>
                    GSTIN: <?php echo $row1['gstin'];?> <br/>
                    Phone Number: <?php echo $row1['mobile'];?> <br/>
                    Email: <?php echo $row1['email'];?> <br/>
                </p>
                <br/>
                <p>
                    Invoice No: <?php echo $invoice_number; ?><br/>
                    Order ID: <?php echo $order_id; ?><br/>
                </p>
            </div>
        </div>
        <div class="clearfix"></div>
        

        <div class="content">
            <h2 class="mb-0">INVOICE</h2>
            <div class="">
                <div class="width-50 pull-left">
                    <p>                        
                        <b>Billing Address</b><br />
                        Name : <?php echo $row1['customer_name']; ?>,
                            <br />
                        Address: <?php echo $address['address']; ?>,
                            <br />
                        Phone No: <?php echo $row1['customer_contact']; ?>,
                            <br />
                        Email: <?php echo $row1['customer_email']; ?>.
                    </p>
                </div>
                <div class="width-50 pull-right">
                    <p>                        
                        <b>Shipping Address</b><br />
                        Name : <?php echo $row1['customer_name']; ?>,
                            <br />
                        Address: <?php echo $address['address']; ?>,
                            <br />
                        Phone No: <?php echo $row1['customer_contact']; ?>,
                            <br />
                        Email: <?php echo $row1['customer_email']; ?>.
                    </p>
                </div>                
                <div class="clearfix"></div>
            </div>
            <h3>Order Details</h3>
            <?php
                    
                    
                    // $sql = "SELECT a.product_title,a.product_qty,a.product_price,a.order_id,a.payment_type,a.amount,a.coupon_code,a.customer_address,b.tax_status,b.tax,b.shipping_charges,b.cod_charges,c.state FROM order_details a join productsnew b on a.product_id = b.ID join vendors c on c.vendorID=b.vendor_id where a.order_id='$order_id' AND a.invoice_number='$invoice_number'";
                    // echo ($sql);
            ?>
            <table class="order-details">
                <thead>
                    <tr class="bg-dark" rowspan="2">
                        <th>S.No</th>
                        <th>Product Details</th>
                        <th>Qty</th>
                        <th>Cost</th>
                        <th>Tax Rate</th>
                        <th colspan="3">Tax Type</th>
                        <th style="width:80px;text-align:right">Total Amount</th>
                    </tr>
                    <tr class="bg-dark">                        
                        <th colspan="5">&nbsp;</th>
                        <th>IGST</th>
                        <th>CGST</th>
                        <th>SGST</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <?php
                    $x=1;
                    
                    // $sql = "SELECT `product_title`,`product_qty`,`product_price`,`order_id`,`payment_type`,`shipping_charges`,`cod_charges`,`amount`,`coupon_code` FROM `order_details` WHERE invoice_number='$invoice_number' AND order_id='$order_id' AND product_id IN (" . implode(',', $productIdsArray) . ")";
                    $sql = "SELECT a.product_title,a.product_qty,a.product_price,a.order_id,a.payment_type,a.amount,a.coupon_code,a.customer_address,b.tax_status,b.tax,b.shipping_charges,b.cod_charges,c.state FROM order_details a join productsnew b on a.product_id = b.ID join vendors c on c.vendorID=b.vendor_id where a.order_id='$order_id' AND a.invoice_number='$invoice_number'";
                    
                    
                    $result = $conn->query($sql);
                    $cnt = $result->num_rows;
                    $subtotal = 0;
                    while ($row = $result->fetch_assoc()) {
                        $subtotal = $subtotal + ($row['product_qty'] * $row['product_price']);
                        $shipping_charges = $row['shipping_charges'];
                        $cod_charges = $row['cod_charges'];
                        $payment_type = $row['payment_type'];
                        $amount = $row['amount'];
                        $tax_status = $row['tax_status'];
                        
                        $vendorstate = $row['state'];
                        $address = json_decode($row['customer_address'], true);
                        $customerstate = $address['state'];
                        $coupon_code = $row['coupon_code'];
                        $product_qty = $row['product_qty'];
                        
                        $IGST = '';
                        $CGST = '';
                        $SGST = '';
                        $original_cost = $row['product_price'];
                        if($tax_status === 'Taxable'){
                            if($row['tax']!=null){
                                $tax = $row['tax'];
                                $original_cost = ($row['product_price'])/(1+($tax)/100);

                                $taxAmount = ($tax/100 ) * $original_cost;
                                
                                // Calculate the total amount including tax
                                //$totalAmount = $row['product_price'] + $taxAmount;
                                if($vendorstate === $customerstate){
                                    $CGST = ($taxAmount/2);
                                    $SGST = ($taxAmount/2);
                                }else{
                                    $IGST = $taxAmount;
                                }
                                $tax = $tax.' %';
                            }
                            
                        }
                        ?>
                        <tr>
                            <td><?php echo $x++ ?></td>
                            <td><?php echo $row['product_title']; ?></td>
                            <td><?php echo $row['product_qty']; ?></td>
                            <td><?php echo sprintf("%.2f", $original_cost); ?></td>
                            <td><?php echo $tax; ?></td>
                            <td><?php echo $IGST!='' ? sprintf("%.2f", $IGST) : ''; ?></td>
                            <td><?php echo $CGST!='' ? sprintf("%.2f", $CGST) : ''; ?></td>
                            <td><?php echo $SGST!='' ? sprintf("%.2f", $SGST) : ''; ?></td>
                            <td style="text-align:right">₹ <?php echo ($row['product_qty'] * $row['product_price']); ?></td>                                                    
                        </tr>
                    <?php  } ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Sub Total</td>                                         
                        <td style="text-align:right">₹ <?php echo $subtotal; ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Shipping Charges</td>                                         
                        <td style="text-align:right">₹ <?php echo $shipping_charges; ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">COD Charges</td>                                         
                        <td style="text-align:right">₹ <?php echo $cod_charges; ?></td>
                    </tr> 
                    <?php
                        // Assuming $subtotal is the original subtotal amount
                        $subtotal_after_discount = 0;
                        if($coupon_code === 'EB15'){
                            // Calculate the discount amount
                            $discount_percentage = 15; // 15% discount
                            $discount = ($subtotal * $discount_percentage) / 100;

                            // Calculate the discounted subtotal
                            $subtotal_after_discount = $subtotal - $discount;
                        ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Discount</td>                                         
                        <td style="text-align:right">- ₹ <?php echo $discount; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2">Total</td>                                         
                        <td style="text-align:right">₹ <?php echo ($subtotal + $shipping_charges + $cod_charges) - $discount; ?></td>
                    </tr>                   
                </tbody>
            </table>
            
            <div class="clearfix"></div>
            <div class="mt-50"></div>
            <div class="text-center fix-bottom">
                <ul><li>This is an eletronic generated Inovice, thus NO Signature is required</li></ul>
                <p>@EarthBased.store. All rights reserved</p>
            </div>
        </div>
    </div>
</body>
</html>