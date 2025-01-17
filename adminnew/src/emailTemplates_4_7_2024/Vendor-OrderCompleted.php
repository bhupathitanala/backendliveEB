<!DOCTYPE html>
<html lang="en">
<?php
    require_once('../services/config.php');
    $data = json_decode(file_get_contents("php://input"), true);
    // var_dump($data);
    // exit();
    //$address = $data['customer_address'];
    $orderId = $data['orderId'];
    $status = $data['status'];
    $sql = "SELECT * FROM `order_details` WHERE order_id ='$orderId'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    // print_r($row);
    // exit();
    $address = json_decode($row['customer_address'], true);
    
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Completed - <?php echo $orderId; ?></title>
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
            <h2>Order Completed - <?php echo $orderId; ?></h2>
            <p>Dear Vendor,</p>
            <p>We are pleased to inform you that the following order has been completed. Here are the details:</p>
            <h3>Order Details:</h3>
            <table class="order-details">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $row['product_title'] ?></td>
                        <td><?php echo $row['product_qty'] ?></td>
                        <td><?php echo $row['product_price'] ?></td>
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
