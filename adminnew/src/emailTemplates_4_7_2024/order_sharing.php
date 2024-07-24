<!DOCTYPE html>
<html lang="en">
<?php
    require_once('../services/config.php');
    $data = json_decode(file_get_contents("php://input"), true);
    // var_dump($data);
    // exit();
    //$address = $data['customer_address'];
    $order_id = $data['orderId'];
    $invoice_number = $data['invoice_number'];
    $title = $data['title'];
    $usertype = $data['usertype'];
    $status = $data['status'];
    $sql = "SELECT * FROM `order_details` WHERE order_id ='$order_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    // print_r($row);
    // exit();
    $address = json_decode($row['customer_address'], true);
    
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        body {
            background-color: #edf1e4;
            color: #422D09;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
            font-size: 16px;
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
            border: 1px solid #dddddd;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="https://earthbased.store/?v=a83cd41b5721">
                <img src="https://earthbased.store/static/media/footer_logo.d72cb08204191c52b659.png" alt="EarthBased Logo">
            </a>
        </div>
        <div class="content">
            <h2><?php echo $title; ?></h2>
            <p>Dear <?php echo $usertype; ?>,</p>
            <p>We are excited to inform you that your order is now being processed. Here are the details of your order:</p>
            <h3>Order Details:</h3>
            <table class="order-details">
                <thead>
                    <tr class="bg-dark" rowspan="2">
                        <th>#</th>
                        <th>Invoice ID</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
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
                            <td><?php echo $invoice_number; ?></td>
                            <td><?php echo $row['product_title']; ?></td>
                            <td><?php echo $row['product_qty']; ?></td>
                            <td style="text-align:right">₹ <?php echo ($row['product_qty'] * $row['product_price']); ?></td>                                                    
                        </tr>
                    <?php  } ?>
                    <tr>
                        <td colspan="4" style="text-align:right">Sub Total</td>                                         
                        <td style="text-align:right">₹ <?php echo $subtotal; ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right">Shipping Charges</td>                                         
                        <td style="text-align:right">₹ <?php echo $shipping_charges; ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right">COD Charges</td>                                         
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
                        <td colspan="4" style="text-align:right">Discount</td>                                         
                        <td style="text-align:right">- ₹ <?php echo $discount; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4" style="text-align:right">Total</td>                                         
                        <td style="text-align:right">₹ <?php echo ($subtotal + $shipping_charges + $cod_charges) - $discount; ?></td>
                    </tr>                   
                </tbody>
            </table>            
            <h3>Billing Address:</h3>
            <p>
                Name : <?php echo $address['firstName']; ?>,
                <br />
                Address : <?php echo $address['address']; ?>,
                <br />
                City : <?php echo $address['city']; ?>,
                <br />
                Pincode : <?php echo $address['pincode']; ?>,
                <br />
                State : <?php echo $address['state']; ?>.
            </p>
            <h3>Shipping Address:</h3>
            <p>
                Name : <?php echo $address['firstName']; ?>,
                <br />
                Address : <?php echo $address['address']; ?>,
                <br />
                City : <?php echo $address['city']; ?>,
                <br />
                Pincode : <?php echo $address['pincode']; ?>,
                <br />
                State : <?php echo $address['state']; ?>.
            </p>
            <p>If you have any questions or concerns regarding your order, please don't hesitate to contact us.</p>
        </div>
        <div class="footer">
            <p>Thank you for choosing EarthBased!</p>
            <p><a href="https://earthbased.store/?v=a83cd41b5721">Visit our store</a> for more amazing plant-based products.</p>
        </div>
    </div>
</body>

</html>