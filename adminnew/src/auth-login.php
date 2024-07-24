<?php 
include 'partials/main.php'; 
$_SESSION['error'] = null;
if (isset($_POST['submit'])) {
    $email = filter_input(INPUT_POST, 'email');
    $password = filter_input(INPUT_POST, 'password');
    if (strlen($email) == 0) {
        $_SESSION['error'] =  "Please enter a email";
        $_POST['email'] = null;
    } else {
        $sql = "SELECT * FROM `admin_users` WHERE email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Fetch user details
            $user = $result->fetch_assoc();

            // Verify password hash
            if($user['status']!=1){
                $_SESSION['error'] = "User Inactive";
            }
            else if (password_verify($password, $user['password'])) {
                $_SESSION["user"] = true;
                $_SESSION['userName'] = $user['name'];
                $_SESSION['userId'] = $user['ID'];
                $_SESSION['userType'] = $user['user_type'];
                $_SESSION['userPermissions'] = $user['permissions'];
                $_SESSION['userEmail'] = $user['email'];
                // vendor id
                $user_id = $user['ID'];
                $vendor_fetch = mysqli_fetch_array(mysqli_query($conn, "SELECT `vendorID` from `vendors` WHERE `user_id`='$user_id'"));
                $_SESSION['vendorId'] = $vendor_fetch['vendorID'];
                
                header('Location: ./');
            } else {
                // Password is incorrect
                $_SESSION['error'] = "Invalid details.";
            }
        } else {
            // User does not exist
            $_SESSION['error'] = "User not found.";
        }

        $conn->close();

    }
}



?>

<head>
    <?php
    $title = "Log In";
    include 'partials/title-meta.php'; ?>

    <?php include 'partials/head-css.php'; ?>
</head>

<body class="authentication-bg position-relative">
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-8 col-lg-6">
                    <div class="card overflow-hidden">
                        <div class="row g-0">
                            <div class="col-lg-12">
                                <div class="d-flex flex-column h-100">
                                    <div class="auth-brand p-3">
                                        <a href="index" class="logo-light">
                                            <img src="assets/images/earthbased-logo.webp" alt="logo" height="70">
                                        </a>
                                        <a href="index" class="logo-dark">
                                            <img src="assets/images/earthbased-logo.webp" alt="dark logo" height="70">
                                        </a>
                                    </div>
                                    <h3 class="text-center text-danger"><span><?php echo $_SESSION['error'] ?></span></h3>
                                    <div class="p-3">
                                        <h4 class="fs-20">Sign In</h4>
                                        <p class="text-muted mb-3">Enter your email address and password to access
                                            account.
                                        </p>

                                        <!-- form -->
                                        <form method="POST">
                                            <div class="mb-3">
                                                <label for="emailaddress" class="form-label">Email address</label>
                                                <input class="form-control" type="email" id="emailaddress" name="email" placeholder="Enter your email" value="<?php echo ($_POST['email']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <a href="auth-forgotpw" class="text-muted float-end"><small>Forgot
                                                        your
                                                        password?</small></a>
                                                <label for="password" class="form-label">Password</label>
                                                <input class="form-control" type="password" id="password" placeholder="Enter your password" required name="password">
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                                    <label class="form-check-label" for="checkbox-signin">Remember
                                                        me</label>
                                                </div>
                                            </div>
                                            <div class="mb-0 text-start">
                                                <button class="btn btn-soft-primary w-100" type="submit" name="submit"><i class="ri-login-circle-fill me-1"></i> <span class="fw-bold">Log
                                                        In</span> </button>
                                            </div>
                                        </form>
                                        <!-- end form-->
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <footer class="footer footer-alt fw-medium">
        <span class="text-dark">
            <script>
                document.write(new Date().getFullYear())
            </script> © EarthBased - Developed by Techvol
        </span>
    </footer>
    <?php include 'partials/footer-scripts.php'; ?>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>