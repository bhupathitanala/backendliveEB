<!DOCTYPE html>
<html lang="en">
<?php
    require_once('../services/config.php');
    $data = json_decode(file_get_contents("php://input"), true);
    $fullname = $data['name'];
    $message = $data['message'];
    $customer_email = $data['email'];
    
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EarthBased Store | Contact</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="content">
            <h1>Customer Information </h1>
            <p>Name : <?php echo $fullname; ?></p>
            <p>Mail : <?php echo $customer_email; ?></p>
            <p>Message : <?php echo $message; ?></p>
        </div>
        
    </div>
</body>
</html>
