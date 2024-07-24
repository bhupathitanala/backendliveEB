<!-- ========== Left Sidebar Start ========== -->
<?php  if($_SESSION['userType'] === 'ADMIN'){ ?>
    <div class="leftside-menu">

        <!-- Brand Logo Light -->
        <a href="./" class="logo logo-light">
            <span class="logo-lg">
                <img src="assets/images/earthbased-logo.webp" alt="logo">
            </span>
            <span class="logo-sm">
                <img src="assets/images/earthbased-logo.webp" alt="small logo">
            </span>
        </a>

        <!-- Brand Logo Dark -->
        <a href="./" class="logo logo-dark">
            <span class="logo-lg">
                <img src="assets/images/earthbased-logo.webp" alt="dark logo">
            </span>
            <span class="logo-sm">
                <img src="assets/images/earthbased-logo.webp" alt="small logo">
            </span>
        </a>

        <!-- Sidebar -left -->
        <div class="h-100" id="leftside-menu-container" data-simplebar>
            <!--- Sidemenu -->
            <ul class="side-nav">

                <li class="side-nav-title">Main</li>

                <li class="side-nav-item">
                    <a href="./" class="side-nav-link">
                        <i class="ri-dashboard-3-line"></i>
                        <span class="badge bg-success float-end">9+</span>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarMaps" aria-expanded="false" aria-controls="sidebarMaps" class="side-nav-link">
                        <i class="ri-apps-fill"></i>
                        <span> Categories </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarMaps">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="addcategory">Add Main Category</a>
                            </li>
                            <li>
                                <a href="categories">Main Categories</a>
                            </li>
                            <li>
                                <a href="addsubcategory">Add Sub Category</a>
                            </li>
                            <li>
                                <a href="subcategories">Sub Categories</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#brands" aria-expanded="false" aria-controls="brands" class="side-nav-link">
                        <i class="ri-gift-fill"></i>
                        <span> Brands </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="brands">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="addbrand">Add New Brand</a>
                            </li>
                            <li>
                                <a href="brands">View All Brands</a>
                            </li>                        
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#users" aria-expanded="false" aria-controls="users" class="side-nav-link">
                        <i class="ri-user-line"></i>
                        <span> Users </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="users">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="adduser">Add New User</a>
                            </li>
                            <li>
                                <a href="users">View All Users</a>
                            </li>                        
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a href="customers" class="side-nav-link">
                        <i class="ri-user-5-line"></i>
                        <span> Customers </span>
                    </a>
                </li>



                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#vendors" aria-expanded="false" aria-controls="vendors" class="side-nav-link">
                        <i class="ri-service-fill"></i>
                        <span> Vendors </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="vendors">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="addvendor">Add New Vendor</a>
                            </li>
                            <li>
                                <a href="vendors">View All Vendors</a>
                            </li>                        
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#products" aria-expanded="false" aria-controls="products" class="side-nav-link">
                        <i class="ri-gift-fill"></i>
                        <span> Products </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="products">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="product">Add New Product</a>
                            </li>
                            <li>
                                <a href="products">View All Products</a>
                            </li>
                            <li>
                                <a href="products_home">Home Page Products</a>
                            </li>
                        </ul>
                    </div>
                </li>


                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#Coupons" aria-expanded="false" aria-controls="Coupons" class="side-nav-link">
                        <i class="ri-gift-fill"></i>
                        <span> Coupons </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Coupons">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="addcoupon">Add New Coupon</a>
                            </li>
                            <li>
                                <a href="coupons">View All Coupons</a>
                            </li>                        
                        </ul>
                    </div>
                </li>


                <li class="side-nav-item">
                    <a href="orders" class="side-nav-link">
                        <i class="ri-shopping-bag-fill"></i>
                        <span class="badge bg-danger float-end"><?php $ordered = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) AS `neworders` FROM `order_details` WHERE `delivery_status`='ordered'"))['neworders']; if ( $ordered > 1 ) echo ($ordered - 1)."+"; else echo $orders; ?></span>
                        <span> Orders </span>
                    </a>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#Challenges" aria-expanded="false" aria-controls="Challenges" class="side-nav-link">
                        <i class="ri-open-arm-fill"></i>
                        <span> Challenges </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Challenges">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="addchallenge">Add Challenge</a>
                            </li>
                            <!-- <li>
                                <a href="challenges">View All Challenges</a>
                            </li>  -->                       
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#reviews" aria-expanded="false" aria-controls="reviews" class="side-nav-link">
                        <i class="ri-star-half-line"></i>
                        <span> Reviews </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="reviews">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="addreviews">Upload Reviews</a>
                            </li>
                            <li>
                                <a href="reviews">View All Reviews</a>
                            </li>                        
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#queries" aria-expanded="false" aria-controls="queries" class="side-nav-link">
                        <i class="ri-stethoscope-line"></i>
                        <span> Queries </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="queries">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="newqueries">New Queries</a>
                            </li>
                            <li>
                                <a href="querieslist">Completed</a>
                            </li>                        
                        </ul>
                    </div>
                </li>


                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#blogs" aria-expanded="false" aria-controls="blogs" class="side-nav-link">
                        <i class="ri-book-3-line"></i>
                        <span> Blogs </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="blogs">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="addblog">Add New Blog</a>
                            </li>
                            <li>
                                <a href="blogs">View All Blogs</a>
                            </li>                        
                        </ul>
                    </div>
                </li>

            </ul>
            <!--- End Sidemenu -->

            <div class="clearfix"></div>
        </div>
    </div>
<?php }else if(in_array($_SESSION['userType'], array("VENDOR","SUBADMIN"))){ ?>
    <div class="leftside-menu">

        <!-- Brand Logo Light -->
        <a href="./" class="logo logo-light">
            <span class="logo-lg">
                <img src="assets/images/earthbased-logo.webp" alt="logo">
            </span>
            <span class="logo-sm">
                <img src="assets/images/earthbased-logo.webp" alt="small logo">
            </span>
        </a>

        <!-- Brand Logo Dark -->
        <a href="./" class="logo logo-dark">
            <span class="logo-lg">
                <img src="assets/images/earthbased-logo.webp" alt="dark logo">
            </span>
            <span class="logo-sm">
                <img src="assets/images/earthbased-logo.webp" alt="small logo">
            </span>
        </a>

        <!-- Sidebar -left -->
        <div class="h-100" id="leftside-menu-container" data-simplebar>
            <!--- Sidemenu -->
            <ul class="side-nav">

                <li class="side-nav-title">Main</li>

                <li class="side-nav-item">
                    <a href="./" class="side-nav-link">
                        <i class="ri-dashboard-3-line"></i>
                        <span class="badge bg-success float-end">9+</span>
                        <span> Dashboard </span>
                    </a>
                </li>



                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#products" aria-expanded="false" aria-controls="products" class="side-nav-link">
                        <i class="ri-gift-fill"></i>
                        <span> Products </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="products">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="product">Add New Product</a>
                            </li>
                            <li>
                                <a href="products">View All Products</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <!--- End Sidemenu -->

            <div class="clearfix"></div>
        </div>
    </div>
<?php }else if($_SESSION['userType'] === 'BLOGGER'){ ?>
    <div class="leftside-menu">

        <!-- Brand Logo Light -->
        <a href="./" class="logo logo-light">
            <span class="logo-lg">
                <img src="assets/images/earthbased-logo.webp" alt="logo">
            </span>
            <span class="logo-sm">
                <img src="assets/images/earthbased-logo.webp" alt="small logo">
            </span>
        </a>

        <!-- Brand Logo Dark -->
        <a href="./" class="logo logo-dark">
            <span class="logo-lg">
                <img src="assets/images/earthbased-logo.webp" alt="dark logo">
            </span>
            <span class="logo-sm">
                <img src="assets/images/earthbased-logo.webp" alt="small logo">
            </span>
        </a>

        <!-- Sidebar -left -->
        <div class="h-100" id="leftside-menu-container" data-simplebar>
            <!--- Sidemenu -->
            <ul class="side-nav">

                <li class="side-nav-title">Main</li>

                <li class="side-nav-item">
                    <a href="./" class="side-nav-link">
                        <i class="ri-dashboard-3-line"></i>
                        <span class="badge bg-success float-end">9+</span>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#blogs" aria-expanded="false" aria-controls="blogs" class="side-nav-link">
                        <i class="ri-book-3-line"></i>
                        <span> Blogs </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="blogs">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="blogs">View All Blogs</a>
                            </li>                        
                        </ul>
                    </div>
                </li>
            </ul>

            <!--- End Sidemenu -->

            <div class="clearfix"></div>
        </div>
    </div>
<?php }else if($_SESSION['userType'] === 'NUTRITION'){ ?>
    <div class="leftside-menu">

        <!-- Brand Logo Light -->
        <a href="./" class="logo logo-light">
            <span class="logo-lg">
                <img src="assets/images/earthbased-logo.webp" alt="logo">
            </span>
            <span class="logo-sm">
                <img src="assets/images/earthbased-logo.webp" alt="small logo">
            </span>
        </a>

        <!-- Brand Logo Dark -->
        <a href="./" class="logo logo-dark">
            <span class="logo-lg">
                <img src="assets/images/earthbased-logo.webp" alt="dark logo">
            </span>
            <span class="logo-sm">
                <img src="assets/images/earthbased-logo.webp" alt="small logo">
            </span>
        </a>

        <!-- Sidebar -left -->
        <div class="h-100" id="leftside-menu-container" data-simplebar>
            <!--- Sidemenu -->
            <ul class="side-nav">

                <li class="side-nav-title">Main</li>

                <li class="side-nav-item">
                    <a href="./" class="side-nav-link">
                        <i class="ri-dashboard-3-line"></i>
                        <span class="badge bg-success float-end">9+</span>
                        <span> Dashboard </span>
                    </a>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#Challenges" aria-expanded="false" aria-controls="Challenges" class="side-nav-link">
                        <i class="ri-open-arm-fill"></i>
                        <span> Challenges </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="Challenges">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="addchallenge">Add Challenge</a>
                            </li>
                            <!-- <li>
                                <a href="challenges">View All Challenges</a>
                            </li>  -->                       
                        </ul>
                    </div>
                </li>

                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#queries" aria-expanded="false" aria-controls="queries" class="side-nav-link">
                        <i class="ri-stethoscope-line"></i>
                        <span> Queries </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="queries">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="newqueries">New Queries</a>
                            </li>
                            <li>
                                <a href="querieslist">Completed</a>
                            </li>                        
                        </ul>
                    </div>
                </li>
            </ul>

            <!--- End Sidemenu -->

            <div class="clearfix"></div>
        </div>
    </div>
<?php } ?>

<!-- ========== Left Sidebar End ========== -->