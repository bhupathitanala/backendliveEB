<!DOCTYPE html>
<html lang="en">
<?php
    require_once('../services/config.php');
    $data = json_decode(file_get_contents("php://input"), true);
    // $address = json_decode($data['customer_address'], true);
    $invoice_number = $data['invoice_number']; // Assuming order_ids is an array of integers
    //$productids = $data['productids']; // Assuming order_ids is an array of integers
    // Explode the productids string into an array of integers
    $productIdsArray = explode(',', $data['productids']);

    // Convert each element in the array to an integer
    $productIdsArray = array_map('intval', $productIdsArray);

    // Now $productIdsArray contains the product IDs as integers
    // print_r($productIdsArray);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Placed - <?php echo $data['invoice_number']; ?></title>
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
            <h2>New Order Placed - <?php echo $data['invoice_number']; ?></h2>
            <p>Dear Vendor,</p>
            <p>A new order has been placed on EarthBased. Please find the details of the order below:</p>
            <h3>Order Details:</h3>
            <table class="order-details">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th style="width:80px;text-align:right">Price</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $x=1;                    
                    $sql = "SELECT `product_title`,`product_qty`,`product_price`,`order_id`,`payment_type`,`shipping_charges`,`cod_charges`,`amount`,`coupon_code`,`customer_address` FROM `order_details` WHERE invoice_number='$invoice_number' AND product_id IN (" . implode(',', $productIdsArray) . ")";
                    $result = $conn->query($sql);
                    $cnt = $result->num_rows;

                    $subtotal = 0;
                    while ($row = $result->fetch_assoc()) {
                        // $subtotal = $subtotal + $row['product_price'];
                        // $shipping_charges = $row['shipping_charges'];
                        // $cod_charges = $row['cod_charges'];
                        // $payment_type = $row['payment_type'];
                        // $amount = $row['amount'];
                        $address = json_decode($row['customer_address'], true);
                        ?>
                        <tr>
                            <td><?php echo $x++ ?></td>
                            <td><?php echo $row['order_id'] ?></td>
                            <td><?php echo $row['product_title'] ?></td>
                            <td><?php echo $row['product_qty'] ?></td>
                            <td style="text-align:right">₹ <?php echo $row['product_price'] ?>.00</td>                                                    
                        </tr>
                    <?php  } ?>
                    
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
            <p>Please process this order as soon as possible. If you have any questions or concerns, feel free to contact us.</p>
        </div>
        <div class="footer">
            <p>Thank you for choosing EarthBased!</p>
            <p><a href="https://earthbased.store/?v=a83cd41b5721">Visit our store</a> for more amazing plant-based products.</p>
        </div>
    </div>
</body>

</html>
