<?php
include 'config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
print_r($_SESSION);
// **SESSION CHECK: If the user is already logged in, redirect them**
// if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === TRUE) {
//     header("Location: dashboard.php");
//     exit();
// }
$message = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $designation =  'website user';
    $password_raw = $_POST['password'] ?? '';
    $phone_number = trim($_POST['phone_number'] ?? '');
    $terms_agreed = isset($_POST['terms']);

    // ✅ Validation
    if (empty($firstname)) {
        $errors['firstname'] = "First name is required.";
    }
    if (empty($lastname)) {
        $errors['lastname'] = "Last name is required.";
    }
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (empty($phone_number)) {
        $errors['phone_number'] = "Phone number is required.";
    } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $phone_number)) {
        $errors['phone_number'] = "Invalid phone number format.";
    }
    if (empty($password_raw)) {
        $errors['password'] = "Password is required.";
    }
    if (!$terms_agreed) {
        $errors['terms'] = "You must agree to the Terms and Conditions.";
    }

    // ✅ If no validation errors, process the sign-up
    if (empty($errors)) {
        $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO tbl_users (firstname, lastname, email, designation, password, phone_number) VALUES (?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            error_log("SQL Prepare Error: " . $conn->error);
            $message = '<div class="alert alert-danger text-white">A server error occurred.</div>';
        } else {
            $stmt->bind_param("ssssss", $firstname, $lastname, $email, $designation, $password_hashed, $phone_number);

            try {
                if ($stmt->execute()) {
                    $_SESSION['user_id'] = $stmt->insert_id;
                    $_SESSION['user_name'] = $firstname;
                    $_SESSION['designation'] = $designation === "Administrator" ? "admin" : "user";
                    $_SESSION['logged_in'] = TRUE;
                    // header("Location: dashboard.php");
                    // exit();
                } else {
                    $message = '<div class="alert alert-danger text-white">A server error occurred. Please try again.</div>';
                }
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $errors['email'] = "An account with this email already exists. Please sign in.";
                } else {
                    $message = '<div class="alert alert-danger text-white">A general database error occurred.</div>';
                }
            } finally {
                $stmt->close();
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">


<!-- molla/index-4.html  22 Nov 2019 09:53:08 GMT -->

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kharido.pk - eCommerce </title>
    <meta name="keywords" content="HTML5 Template">
    <meta name="description" content="Kharido.pk - eCommerce">
    <meta name="author" content="p-themes">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/icons/favicon-16x16.png">
    <link rel="manifest" href="assets/images/icons/site.html">
    <link rel="mask-icon" href="assets/images/icons/safari-pinned-tab.svg" color="#666666">
    <link rel="shortcut icon" href="assets/images/icons/favicon.ico">
    <meta name="apple-mobile-web-app-title" content="Molla">
    <meta name="application-name" content="Molla">
    <meta name="msapplication-TileColor" content="#cc9966">
    <meta name="msapplication-config" content="assets/images/icons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css">
    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/owl-carousel/owl.carousel.css">
    <link rel="stylesheet" href="assets/css/plugins/magnific-popup/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/plugins/jquery.countdown.css">
    <!-- Main CSS File -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/skins/skin-demo-4.css">
    <link rel="stylesheet" href="assets/css/demos/demo-4.css">
</head>

<body>
    <div class="page-wrapper">
        <?php include 'layout/header.php'; ?>

        <main class="main">
            <div class="intro-slider-container mb-5">
                <div class="intro-slider owl-carousel owl-theme owl-nav-inside owl-light" data-toggle="owl"
                    data-owl-options='{
                        "dots": true,
                        "nav": false, 
                        "responsive": {
                            "1200": {
                                "nav": true,
                                "dots": false
                            }
                        }
                    }'>
                    <div class="intro-slide" style="background-image: url(assets/images/demos/demo-4/slider/slide-1.png);">
                        <div class="container intro-content">
                            <div class="row justify-content-end">
                                <div class="col-auto col-sm-7 col-md-6 col-lg-5">
                                    <h3 class="intro-subtitle text-third">Deals and Promotions</h3><!-- End .h3 intro-subtitle -->
                                    <h1 class="intro-title">Beats by</h1>
                                    <h1 class="intro-title">Dre Studio 3</h1><!-- End .intro-title -->

                                    <div class="intro-price">
                                        <sup class="intro-old-price">$349,95</sup>
                                        <span class="text-third">
                                            $279<sup>.99</sup>
                                        </span>
                                    </div><!-- End .intro-price -->

                                    <a href="category.html" class="btn btn-primary btn-round">
                                        <span>Shop More</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </a>
                                </div><!-- End .col-lg-11 offset-lg-1 -->
                            </div><!-- End .row -->
                        </div><!-- End .intro-content -->
                    </div><!-- End .intro-slide -->

                    <div class="intro-slide" style="background-image: url(assets/images/demos/demo-4/slider/slide-2.png);">
                        <div class="container intro-content">
                            <div class="row justify-content-end">
                                <div class="col-auto col-sm-7 col-md-6 col-lg-5">
                                    <h3 class="intro-subtitle text-primary">New Arrival</h3><!-- End .h3 intro-subtitle -->
                                    <h1 class="intro-title">Apple iPad Pro <br>12.9 Inch, 64GB </h1><!-- End .intro-title -->

                                    <div class="intro-price">
                                        <sup>Today:</sup>
                                        <span class="text-primary">
                                            $999<sup>.99</sup>
                                        </span>
                                    </div><!-- End .intro-price -->

                                    <a href="category.html" class="btn btn-primary btn-round">
                                        <span>Shop More</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </a>
                                </div><!-- End .col-md-6 offset-md-6 -->
                            </div><!-- End .row -->
                        </div><!-- End .intro-content -->
                    </div><!-- End .intro-slide -->
                </div><!-- End .intro-slider owl-carousel owl-simple -->

                <span class="slider-loader"></span><!-- End .slider-loader -->
            </div><!-- End .intro-slider-container -->

            <div class="container">
                <h2 class="title text-center mb-4">Explore Popular Categories</h2><!-- End .title text-center -->

                <?php

                // 2. SQL Query
                // Assuming you want them in a specific order, e.g., by id
                $sql = "SELECT id, category_name FROM tbl_categories ORDER BY id ASC";
                $result = $conn->query($sql);
                // Initialize an array to store fetched categories
                $categories = [];
                if ($result && $result->num_rows > 0) {
                    // Fetch all rows into the array
                    while ($row = $result->fetch_assoc()) {
                        $categories[] = $row;
                    }
                }

                // Close connection (optional, but good practice if no more queries follow)
                mysqli_close($conn);

                // 3. Generate the HTML structure
                $image_counter = 1; // To map to cats/1.png, cats/2.png, etc.
                ?>

                <div class="cat-blocks-container">
                    <div class="row">

                        <?php
                        if (!empty($categories)):
                            foreach ($categories as $category):
                                // Sanitize category name for potential safe use in URLs or IDs (optional, but recommended)
                                $safe_category_name = htmlspecialchars($category['category_name']);

                                // Construct the dynamic image source (assuming image numbering matches fetch order)
                                $image_src = "assets/images/demos/demo-4/cats/" . $image_counter . ".png";

                                // Construct the category link dynamically
                                // You might change "category.html" to "category.php?id=" . $category['id'] or a SEO-friendly slug
                                $category_link = "category.html?id=" . $category['id'];
                        ?>

                                <div class="col-6 col-sm-4 col-lg-2">
                                    <a href="<?php echo $category_link; ?>" class="cat-block">
                                        <figure>
                                            <span>
                                                <img src="<?php echo $image_src; ?>" alt="<?php echo $safe_category_name; ?> image">
                                            </span>
                                        </figure>
                                        <h3 class="cat-block-title"><?php echo $safe_category_name; ?></h3>
                                    </a>
                                </div><?php
                                        $image_counter++; // Increment counter for the next image
                                    endforeach;
                                else:
                                        ?>
                            <div class="col-12">
                                <p>No categories found.</p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div><!-- End .cat-blocks-container -->
                <div class="mb-3"></div><!-- End .mb-5 -->

                <?php


                // --- 2. Establish Connection ---
                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // --- 3. Fetch Data ---

                // Fetch categories for mapping (to show name in the product block)
                $category_map = [];
                $cat_result = $conn->query("SELECT id, category_name FROM tbl_categories");
                if ($cat_result && $cat_result->num_rows > 0) {
                    while ($row = $cat_result->fetch_assoc()) {
                        $category_map[$row['id']] = $row['category_name'];
                    }
                }

                // Fetch active products, ordered to keep 'isHot' products first
                $products_data = [];
                $sql = "SELECT p.*, c.category_name 
        FROM tbl_products p 
        JOIN tbl_categories c ON p.category_id = c.id 
        WHERE p.isActive = 1 
        ORDER BY p.isHot DESC, p.created_at DESC";

                $prod_result = $conn->query($sql);

                if ($prod_result && $prod_result->num_rows > 0) {
                    while ($row = $prod_result->fetch_assoc()) {
                        // Convert attachments JSON string (e.g., '["file1.jpg", "file2.jpg"]') to a PHP array
                        $row['attachments'] = json_decode($row['attachments'], true);
                        $products_data[] = $row;
                    }
                }

                $conn->close();

                // --- 4. Group products by Category ID for the carousel tabs ---
                $grouped_products = [];
                foreach ($products_data as $product) {
                    $grouped_products[$product['category_id']][] = $product;
                }

                // Map the HTML tab IDs to the respective product groups
                $tabs = [
                    'new-all-tab'       => $products_data, // The combined list for the "All" tab
                    'new-computers-tab' => $grouped_products[11] ?? [],
                    'new-tv-tab'        => $grouped_products[14] ?? [],
                    'new-phones-tab'    => $grouped_products[13] ?? [],
                    'new-watches-tab'   => $grouped_products[16] ?? [],
                    'new-cameras-tab' => $grouped_products[12] ?? [],
                    'new-audio-tab' => $grouped_products[15] ?? [],
                    // Add other groups here (e.g., Digital Cameras ID 12, Audio ID 15)
                ];

                // Helper function to format price
                function format_price($price)
                {
                    return '$' . number_format((float)$price, 2, '.', ',');
                }

                // Helper function for dummy rating (e.g., 60% = 3 stars, 100% = 5 stars)
                function get_rating_width()
                {
                    // In a real application, this would come from a tbl_reviews table.
                    return rand(60, 100);
                }

                // Helper function for dummy review count
                function get_review_count()
                {
                    // In a real application, this would come from a count on tbl_reviews.
                    return rand(2, 12);
                }
                ?>

                <div class="container new-arrivals">
                    <div class="heading heading-flex mb-3">
                        <div class="heading-left">
                            <h2 class="title">New Arrivals</h2><!-- End .title -->
                        </div><!-- End .heading-left -->

                        <div class="heading-right">
                            <ul class="nav nav-pills nav-border-anim justify-content-center" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="new-all-link" data-toggle="tab" href="#new-all-tab" role="tab" aria-controls="new-all-tab" aria-selected="true">All</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="new-tv-link" data-toggle="tab" href="#new-tv-tab" role="tab" aria-controls="new-tv-tab" aria-selected="false">TV</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="new-computers-link" data-toggle="tab" href="#new-computers-tab" role="tab" aria-controls="new-computers-tab" aria-selected="false">Computers</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="new-phones-link" data-toggle="tab" href="#new-phones-tab" role="tab" aria-controls="new-phones-tab" aria-selected="false">Tablets & Cell Phones</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="new-watches-link" data-toggle="tab" href="#new-watches-tab" role="tab" aria-controls="new-watches-tab" aria-selected="false">Smartwatches</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="new-audio-link" data-toggle="tab" href="#new-audio-tab" role="tab" aria-controls="new-audio-tab" aria-selected="false">Audio</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="new-cameras-link" data-toggle="tab" href="#new-cameras-tab" role="tab" aria-controls="new-cameras-tab" aria-selected="false">Digital Cameras</a>
                                </li>


                            </ul>
                        </div><!-- End .heading-right -->
                    </div><!-- End .heading -->

                    <div class="tab-content tab-content-carousel just-action-icons-sm">

                        <?php
                        $first_tab = true;

                        // Loop through the defined tabs/groups
                        foreach ($tabs as $tab_id => $products):
                            $is_active = $first_tab ? 'show active' : 'fade';
                        ?>

                            <div class="tab-pane p-0 <?php echo $is_active; ?>" id="<?php echo $tab_id; ?>" role="tabpanel" aria-labelledby="<?php echo $tab_id . '-link'; ?>">

                                <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl"
                                    data-owl-options='{
            "nav": true, 
            "dots": true,
            "margin": 20,
            "loop": false,
            "responsive": {
                "0": {"items":2},
                "480": {"items":2},
                "768": {"items":3},
                "992": {"items":4},
                "1200": {"items":5}
            }}'>

                                    <?php
                                    // Loop through products in the current tab/group
                                    foreach ($products as $product):
                                        $category_name = $product['category_name'];
                                        $product_link = 'product.html?id=' . $product['id'];
                                        // Use the first image attachment path
                                        $image_src = 'admin_dashboard/' . $product['attachments'][0] ?? 'assets/images/placeholder.jpg';

                                        // Determine labels
                                        $labels = '';
                                        if ($product['isHot'] == '1') {
                                            $labels .= '<span class="product-label label-circle label-top">Hot</span>';
                                        }
                                        if (strtotime($product['created_at']) > strtotime('-7 days')) {
                                            $labels .= '<span class="product-label label-circle label-new">New</span>';
                                        }
                                    ?>

                                        <div class="product product-2">
                                            <figure class="product-media">
                                                <?php echo $labels; ?>
                                                <!-- In Future If want the image to be Clickable then just put the image inside this anchor tag -->
                                                <!-- <a href="<?php echo $product_link; ?>">
                                                </a> -->
                                                <img src="<?php echo htmlspecialchars($image_src); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image" style="height: 300px; object-fit: cover;">


                                                <div class="product-action-vertical">
                                                    <a href="#" class="btn-product-icon btn-wishlist" title="Add to wishlist"></a>
                                                </div>
                                                <div class="product-action">
                                                    <?php
                                                    // Check if session is started and user is logged in
                                                    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == 1 && isset($_SESSION['designation']) && $_SESSION['designation'] == "user") {
                                                        // Logged-in user: go to cart
                                                    ?>
                                                        <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn-product btn-cart" title="Add to cart">
                                                            <span>add to cart</span>
                                                        </a>
                                                    <?php
                                                    } else {
                                                        // User not logged in: trigger modal
                                                    ?>
                                                        <a href="#" class="btn-product btn-cart trigger-login" title="Add to cart">
                                                            <span>add to cart</span>
                                                        </a>
                                                    <?php
                                                    }
                                                    ?>
                                                    <a href="popup/quickView.php?id=<?php echo $product['id']; ?>" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></a>
                                                </div>
                                            </figure>
                                            <div class="product-body">
                                                <div class="product-cat">
                                                    <a href="category.html?id=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($category_name); ?></a>
                                                </div>
                                                <h3 class="product-title"><a href="<?php echo $product_link; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a></h3>
                                                <div class="product-price">
                                                    <?php echo format_price($product['price']); ?>
                                                </div>
                                                <div class="ratings-container">
                                                    <div class="ratings">
                                                        <div class="ratings-val" style="width: <?php echo get_rating_width(); ?>%;"></div>
                                                    </div><span class="ratings-text">( <?php echo get_review_count(); ?> Reviews )</span>
                                                </div>
                                            </div>
                                        </div><?php endforeach; // End product loop 
                                                ?>

                                </div>
                            </div><?php
                                    $first_tab = false;
                                endforeach; // End tab loop 
                                    ?>

                    </div>
                </div><!-- End .container -->

                <div class="mb-6"></div><!-- End .mb-6 -->

                <div class="container">
                    <div class="cta cta-border mb-5" style="background-image: url(assets/images/demos/demo-4/bg-1.jpg);">
                        <img src="assets/images/demos/demo-4/camera.png" alt="camera" class="cta-img">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="cta-content">
                                    <div class="cta-text text-right text-white">
                                        <p>Shop Today’s Deals <br><strong>Awesome Made Easy. HERO7 Black</strong></p>
                                    </div><!-- End .cta-text -->
                                    <a href="#" class="btn btn-primary btn-round"><span>Shop Now - $429.99</span><i class="icon-long-arrow-right"></i></a>
                                </div><!-- End .cta-content -->
                            </div><!-- End .col-md-12 -->
                        </div><!-- End .row -->
                    </div><!-- End .cta -->
                </div><!-- End .container -->

                <?php

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                // Fetch all necessary data
                $products_data = [];
                $sql = "SELECT p.*, c.category_name 
        FROM tbl_products p 
        JOIN tbl_categories c ON p.category_id = c.id 
        WHERE p.isActive = '1'";
                $prod_result = $conn->query($sql);

                if ($prod_result && $prod_result->num_rows > 0) {
                    while ($row = $prod_result->fetch_assoc()) {
                        $row['attachments'] = json_decode($row['attachments'], true);
                        $products_data[] = $row;
                    }
                }
                $conn->close();

                // --- 2. Sorting Logic for Trending Tabs ---

                $trending_tabs = [
                    'trending-top-tab'  => [], // Top Rated
                    'trending-best-tab' => [], // Best Selling
                    'trending-sale-tab' => [], // On Sale
                ];

                // --- Simulation Logic ---
                foreach ($products_data as $product) {
                    // Top Rated (Simulated: all active products, shuffled for variety)
                    $trending_tabs['trending-top-tab'][] = $product;

                    // Best Selling (Simulated: Products marked as isHot=1)
                    if ($product['isHot'] == '1') {
                        $trending_tabs['trending-best-tab'][] = $product;
                    }

                    // On Sale (Simulated: Products where price is less than a certain threshold or marked as isHot=1)
                    if ($product['isHot'] == '1' || $product['price'] < 500) {
                        $trending_tabs['trending-sale-tab'][] = $product;
                    }
                }

                // Simple shuffle for Top Rated to simulate review score ordering
                shuffle($trending_tabs['trending-top-tab']);


                ?>



                <div class="bg-light pt-5 pb-6">
                    <div class="container trending-products">
                        <div class="heading heading-flex mb-3">
                            <div class="heading-left">
                                <h2 class="title">Trending Products</h2>
                            </div>
                            <div class="heading-right">
                                <ul class="nav nav-pills nav-border-anim justify-content-center" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="trending-top-link" data-toggle="tab" href="#trending-top-tab" role="tab" aria-controls="trending-top-tab" aria-selected="true">Top Rated</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="trending-best-link" data-toggle="tab" href="#trending-best-tab" role="tab" aria-controls="trending-best-tab" aria-selected="false">Best Selling</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="trending-sale-link" data-toggle="tab" href="#trending-sale-tab" role="tab" aria-controls="trending-sale-tab" aria-selected="false">On Sale</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-5col d-none d-xl-block">
                                <div class="banner">
                                    <a href="#">
                                        <img src="assets/images/demos/demo-4/banners/banner-4.jpg" alt="banner">
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-4-5col">
                                <div class="tab-content tab-content-carousel just-action-icons-sm">

                                    <?php
                                    $first_tab_trending = true;
                                    // Loop through the trending tabs defined in PHP
                                    foreach ($trending_tabs as $tab_id => $products):
                                        $is_active = $first_tab_trending ? 'show active' : 'fade';
                                        $first_tab_trending = false; // Set to false after the first loop iteration
                                    ?>

                                        <div class="tab-pane p-0 <?php echo $is_active; ?>" id="<?php echo $tab_id; ?>" role="tabpanel" aria-labelledby="<?php echo $tab_id . '-link'; ?>">

                                            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl"
                                                data-owl-options='{
                            "nav": true, 
                            "dots": false,
                            "margin": 20,
                            "loop": false,
                            "responsive": {
                                "0": {"items":2},
                                "480": {"items":2},
                                "768": {"items":3},
                                "992": {"items":4}
                            }}'>

                                                <?php
                                                // Loop through products in the current tab/group
                                                foreach ($products as $product):
                                                    $category_name = $product['category_name'];
                                                    $product_link = 'product.html?id=' . $product['id'];
                                                    $image_src = 'admin_dashboard/' . ($product['attachments'][0] ?? 'assets/images/placeholder.jpg');

                                                    // Determine labels (Hot/New/Sale)
                                                    $labels = '';
                                                    if ($product['isHot'] == '1') {
                                                        $labels .= '<span class="product-label label-circle label-hot">Hot</span>';
                                                    }
                                                    if (strtotime($product['created_at']) > strtotime('-7 days')) {
                                                        $labels .= '<span class="product-label label-circle label-new">New</span>';
                                                    }
                                                    // You would add a 'Sale' label here if your DB had a 'sale_price' field.
                                                    // Example: if ($product['sale_price'] < $product['price']) { ... }
                                                ?>

                                                    <div class="product product-2">
                                                        <figure class="product-media product-media-fix">
                                                            <?php echo $labels; ?>

                                                            <a href="<?php echo $product_link; ?>">
                                                                <img src="<?php echo htmlspecialchars($image_src); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                                                            </a>

                                                            <div class="product-action-vertical">
                                                                <a href="#" class="btn-product-icon btn-wishlist" title="Add to wishlist"></a>
                                                            </div>
                                                            <div class="product-action">
                                                                <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn-product btn-cart" title="Add to cart"><span>add to cart</span></a>
                                                                <a href="popup/quickView.php?id=<?php echo $product['id']; ?>" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></a>
                                                            </div>
                                                        </figure>
                                                        <div class="product-body">
                                                            <div class="product-cat">
                                                                <a href="category.html?id=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($category_name); ?></a>
                                                            </div>
                                                            <h3 class="product-title"><a href="<?php echo $product_link; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a></h3>
                                                            <div class="product-price">
                                                                <?php echo format_price($product['price']); ?>
                                                            </div>
                                                            <div class="ratings-container">
                                                                <div class="ratings">
                                                                    <div class="ratings-val" style="width: <?php echo get_rating_width(); ?>%;"></div>
                                                                </div><span class="ratings-text">( <?php echo get_review_count(); ?> Reviews )</span>
                                                            </div>
                                                        </div>
                                                    </div><?php endforeach; // End product loop 
                                                            ?>

                                            </div>
                                        </div><?php endforeach; // End tab loop 
                                                ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End .bg-light pt-5 pb-6 -->

                <div class="mb-5"></div><!-- End .mb-5 -->



                <div class="mb-4"></div><!-- End .mb-4 -->

                <div class="container">
                    <hr class="mb-0">
                </div><!-- End .container -->

                <div class="icon-boxes-container bg-transparent">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-6 col-lg-3">
                                <div class="icon-box icon-box-side">
                                    <span class="icon-box-icon text-dark">
                                        <i class="icon-rocket"></i>
                                    </span>
                                    <div class="icon-box-content">
                                        <h3 class="icon-box-title">Free Shipping</h3><!-- End .icon-box-title -->
                                        <p>Orders $50 or more</p>
                                    </div><!-- End .icon-box-content -->
                                </div><!-- End .icon-box -->
                            </div><!-- End .col-sm-6 col-lg-3 -->

                            <div class="col-sm-6 col-lg-3">
                                <div class="icon-box icon-box-side">
                                    <span class="icon-box-icon text-dark">
                                        <i class="icon-rotate-left"></i>
                                    </span>

                                    <div class="icon-box-content">
                                        <h3 class="icon-box-title">Free Returns</h3><!-- End .icon-box-title -->
                                        <p>Within 30 days</p>
                                    </div><!-- End .icon-box-content -->
                                </div><!-- End .icon-box -->
                            </div><!-- End .col-sm-6 col-lg-3 -->

                            <div class="col-sm-6 col-lg-3">
                                <div class="icon-box icon-box-side">
                                    <span class="icon-box-icon text-dark">
                                        <i class="icon-info-circle"></i>
                                    </span>

                                    <div class="icon-box-content">
                                        <h3 class="icon-box-title">Get 20% Off 1 Item</h3><!-- End .icon-box-title -->
                                        <p>when you sign up</p>
                                    </div><!-- End .icon-box-content -->
                                </div><!-- End .icon-box -->
                            </div><!-- End .col-sm-6 col-lg-3 -->

                            <div class="col-sm-6 col-lg-3">
                                <div class="icon-box icon-box-side">
                                    <span class="icon-box-icon text-dark">
                                        <i class="icon-life-ring"></i>
                                    </span>

                                    <div class="icon-box-content">
                                        <h3 class="icon-box-title">We Support</h3><!-- End .icon-box-title -->
                                        <p>24/7 amazing services</p>
                                    </div><!-- End .icon-box-content -->
                                </div><!-- End .icon-box -->
                            </div><!-- End .col-sm-6 col-lg-3 -->
                        </div><!-- End .row -->
                    </div><!-- End .container -->
                </div><!-- End .icon-boxes-container -->
        </main><!-- End .main -->

        <?php include 'layout/footer.php'; ?>
    </div><!-- End .page-wrapper -->

    <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
    <!-- Mobile Menu -->
    <div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->
    <div class="mobile-menu-container mobile-menu-light">
        <div class="mobile-menu-wrapper">
            <span class="mobile-menu-close"><i class="icon-close"></i></span>

            <form action="#" method="get" class="mobile-search">
                <label for="mobile-search" class="sr-only">Search</label>
                <input type="search" class="form-control" name="mobile-search" id="mobile-search" placeholder="Search in..." required>
                <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
            </form>

            <ul class="nav nav-pills-mobile nav-border-anim" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="mobile-menu-link" data-toggle="tab" href="#mobile-menu-tab" role="tab" aria-controls="mobile-menu-tab" aria-selected="true">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="mobile-cats-link" data-toggle="tab" href="#mobile-cats-tab" role="tab" aria-controls="mobile-cats-tab" aria-selected="false">Categories</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="mobile-menu-tab" role="tabpanel" aria-labelledby="mobile-menu-link">
                    <nav class="mobile-nav">
                        <ul class="mobile-menu">
                            <li class="active">
                                <a href="index.html">Home</a>

                                <ul>
                                    <li><a href="index-1.html">01 - furniture store</a></li>
                                    <li><a href="index-2.html">02 - furniture store</a></li>
                                    <li><a href="index-3.html">03 - electronic store</a></li>
                                    <li><a href="index-4.html">04 - electronic store</a></li>
                                    <li><a href="index-5.html">05 - fashion store</a></li>
                                    <li><a href="index-6.html">06 - fashion store</a></li>
                                    <li><a href="index-7.html">07 - fashion store</a></li>
                                    <li><a href="index-8.html">08 - fashion store</a></li>
                                    <li><a href="index-9.html">09 - fashion store</a></li>
                                    <li><a href="index-10.html">10 - shoes store</a></li>
                                    <li><a href="index-11.html">11 - furniture simple store</a></li>
                                    <li><a href="index-12.html">12 - fashion simple store</a></li>
                                    <li><a href="index-13.html">13 - market</a></li>
                                    <li><a href="index-14.html">14 - market fullwidth</a></li>
                                    <li><a href="index-15.html">15 - lookbook 1</a></li>
                                    <li><a href="index-16.html">16 - lookbook 2</a></li>
                                    <li><a href="index-17.html">17 - fashion store</a></li>
                                    <li><a href="index-18.html">18 - fashion store (with sidebar)</a></li>
                                    <li><a href="index-19.html">19 - games store</a></li>
                                    <li><a href="index-20.html">20 - book store</a></li>
                                    <li><a href="index-21.html">21 - sport store</a></li>
                                    <li><a href="index-22.html">22 - tools store</a></li>
                                    <li><a href="index-23.html">23 - fashion left navigation store</a></li>
                                    <li><a href="index-24.html">24 - extreme sport store</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="category.html">Shop</a>
                                <ul>
                                    <li><a href="category-list.html">Shop List</a></li>
                                    <li><a href="category-2cols.html">Shop Grid 2 Columns</a></li>
                                    <li><a href="category.html">Shop Grid 3 Columns</a></li>
                                    <li><a href="category-4cols.html">Shop Grid 4 Columns</a></li>
                                    <li><a href="category-boxed.html"><span>Shop Boxed No Sidebar<span class="tip tip-hot">Hot</span></span></a></li>
                                    <li><a href="category-fullwidth.html">Shop Fullwidth No Sidebar</a></li>
                                    <li><a href="product-category-boxed.html">Product Category Boxed</a></li>
                                    <li><a href="product-category-fullwidth.html"><span>Product Category Fullwidth<span class="tip tip-new">New</span></span></a></li>
                                    <li><a href="cart.php">Cart</a></li>
                                    <li><a href="checkout.html">Checkout</a></li>
                                    <li><a href="wishlist.html">Wishlist</a></li>
                                    <li><a href="#">Lookbook</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="product.html" class="sf-with-ul">Product</a>
                                <ul>
                                    <li><a href="product.html">Default</a></li>
                                    <li><a href="product-centered.html">Centered</a></li>
                                    <li><a href="product-extended.html"><span>Extended Info<span class="tip tip-new">New</span></span></a></li>
                                    <li><a href="product-gallery.html">Gallery</a></li>
                                    <li><a href="product-sticky.html">Sticky Info</a></li>
                                    <li><a href="product-sidebar.html">Boxed With Sidebar</a></li>
                                    <li><a href="product-fullwidth.html">Full Width</a></li>
                                    <li><a href="product-masonry.html">Masonry Sticky Info</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Pages</a>
                                <ul>
                                    <li>
                                        <a href="about.html">About</a>

                                        <ul>
                                            <li><a href="about.html">About 01</a></li>
                                            <li><a href="about-2.html">About 02</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="contact.html">Contact</a>

                                        <ul>
                                            <li><a href="contact.html">Contact 01</a></li>
                                            <li><a href="contact-2.html">Contact 02</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="login.html">Login</a></li>
                                    <li><a href="faq.html">FAQs</a></li>
                                    <li><a href="404.html">Error 404</a></li>
                                    <li><a href="coming-soon.html">Coming Soon</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="blog.html">Blog</a>

                                <ul>
                                    <li><a href="blog.html">Classic</a></li>
                                    <li><a href="blog-listing.html">Listing</a></li>
                                    <li>
                                        <a href="#">Grid</a>
                                        <ul>
                                            <li><a href="blog-grid-2cols.html">Grid 2 columns</a></li>
                                            <li><a href="blog-grid-3cols.html">Grid 3 columns</a></li>
                                            <li><a href="blog-grid-4cols.html">Grid 4 columns</a></li>
                                            <li><a href="blog-grid-sidebar.html">Grid sidebar</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#">Masonry</a>
                                        <ul>
                                            <li><a href="blog-masonry-2cols.html">Masonry 2 columns</a></li>
                                            <li><a href="blog-masonry-3cols.html">Masonry 3 columns</a></li>
                                            <li><a href="blog-masonry-4cols.html">Masonry 4 columns</a></li>
                                            <li><a href="blog-masonry-sidebar.html">Masonry sidebar</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#">Mask</a>
                                        <ul>
                                            <li><a href="blog-mask-grid.html">Blog mask grid</a></li>
                                            <li><a href="blog-mask-masonry.html">Blog mask masonry</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#">Single Post</a>
                                        <ul>
                                            <li><a href="single.html">Default with sidebar</a></li>
                                            <li><a href="single-fullwidth.html">Fullwidth no sidebar</a></li>
                                            <li><a href="single-fullwidth-sidebar.html">Fullwidth with sidebar</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="elements-list.html">Elements</a>
                                <ul>
                                    <li><a href="elements-products.html">Products</a></li>
                                    <li><a href="elements-typography.html">Typography</a></li>
                                    <li><a href="elements-titles.html">Titles</a></li>
                                    <li><a href="elements-banners.html">Banners</a></li>
                                    <li><a href="elements-product-category.html">Product Category</a></li>
                                    <li><a href="elements-video-banners.html">Video Banners</a></li>
                                    <li><a href="elements-buttons.html">Buttons</a></li>
                                    <li><a href="elements-accordions.html">Accordions</a></li>
                                    <li><a href="elements-tabs.html">Tabs</a></li>
                                    <li><a href="elements-testimonials.html">Testimonials</a></li>
                                    <li><a href="elements-blog-posts.html">Blog Posts</a></li>
                                    <li><a href="elements-portfolio.html">Portfolio</a></li>
                                    <li><a href="elements-cta.html">Call to Action</a></li>
                                    <li><a href="elements-icon-boxes.html">Icon Boxes</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav><!-- End .mobile-nav -->
                </div>
                <!-- .End .tab-pane -->
                <div class="tab-pane fade" id="mobile-cats-tab" role="tabpanel" aria-labelledby="mobile-cats-link">
                    <nav class="mobile-cats-nav">
                        <ul class="mobile-cats-menu">
                            <li><a class="mobile-cats-lead" href="#">Daily offers</a></li>
                            <li><a class="mobile-cats-lead" href="#">Gift Ideas</a></li>
                            <li><a href="#">Beds</a></li>
                            <li><a href="#">Lighting</a></li>
                            <li><a href="#">Sofas & Sleeper sofas</a></li>
                            <li><a href="#">Storage</a></li>
                            <li><a href="#">Armchairs & Chaises</a></li>
                            <li><a href="#">Decoration </a></li>
                            <li><a href="#">Kitchen Cabinets</a></li>
                            <li><a href="#">Coffee & Tables</a></li>
                            <li><a href="#">Outdoor Furniture </a></li>
                        </ul><!-- End .mobile-cats-menu -->
                    </nav><!-- End .mobile-cats-nav -->
                </div><!-- .End .tab-pane -->
            </div>
            <!-- End .tab-content -->

            <div class="social-icons">
                <a href="#" class="social-icon" target="_blank" title="Facebook"><i class="icon-facebook-f"></i></a>
                <a href="#" class="social-icon" target="_blank" title="Twitter"><i class="icon-twitter"></i></a>
                <a href="#" class="social-icon" target="_blank" title="Instagram"><i class="icon-instagram"></i></a>
                <a href="#" class="social-icon" target="_blank" title="Youtube"><i class="icon-youtube"></i></a>
            </div><!-- End .social-icons -->
        </div><!-- End .mobile-menu-wrapper -->
    </div><!-- End .mobile-menu-container -->

    <!-- Sign in / Register Modal -->
    <div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="icon-close"></i></span>
                    </button>

                    <div class="form-box">
                        <div class="form-tab">
                            <ul class="nav nav-pills nav-fill nav-border-anim" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="signin-tab" data-toggle="tab" href="#signin" role="tab" aria-controls="signin" aria-selected="true">Sign In</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">Register</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="tab-content-5">
                                <div class="tab-pane fade show active" id="signin" role="tabpanel" aria-labelledby="signin-tab">
                                    <form method="POST" action="sign-in.php">
                                        <div class="form-group">
                                            <label for="signin-email">Email address *</label>
                                            <input type="email" class="form-control" id="signin-email" name="email" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="signin-password">Password *</label>
                                            <input type="password" class="form-control" id="signin-password" name="password" required>
                                        </div>

                                        <div class="form-footer">
                                            <button type="submit" class="btn btn-outline-primary-2">
                                                <span>LOG IN</span>
                                                <i class="icon-long-arrow-right"></i>
                                            </button>

                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="remember_me" class="custom-control-input" id="signin-remember">
                                                <label class="custom-control-label" for="signin-remember">Remember Me</label>
                                            </div>

                                            <a href="#" class="forgot-link">Forgot Your Password?</a>
                                        </div>
                                    </form>

                                    <div class="form-choice">
                                        <p class="text-center">or sign in with</p>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <a href="#" class="btn btn-login btn-g">
                                                    <i class="icon-google"></i>
                                                    Login With Google
                                                </a>
                                            </div><!-- End .col-6 -->
                                            <div class="col-sm-6">
                                                <a href="#" class="btn btn-login btn-f">
                                                    <i class="icon-facebook-f"></i>
                                                    Login With Facebook
                                                </a>
                                            </div><!-- End .col-6 -->
                                        </div><!-- End .row -->
                                    </div><!-- End .form-choice -->
                                </div><!-- .End .tab-pane -->
                                <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                                    <form method="POST">
                                        <!-- <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="register-first-name">First Name *</label>
                                                <input type="text" class="form-control" id="register-first-name" name="register-first-name" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="register-last-name">Last Name *</label>
                                                <input type="text" class="form-control" id="register-last-name" name="register-last-name" required>
                                            </div>
                                        </div> -->

                                        <div class="row">
                                            <div class="form-group  col-md-6">
                                                <label class="form-label">First Name</label>
                                                <div class="input-group input-group-outline mb-1">
                                                    <input type="text" class="form-control" name="firstname"
                                                        value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>" />
                                                </div>
                                                <?php if (isset($errors['firstname'])): ?>
                                                    <small class="text-danger"><?php echo $errors['firstname']; ?></small>
                                                <?php endif; ?>

                                            </div>
                                            <div class="form-group  col-md-6">
                                                <label class="form-label">Last Name</label>
                                                <div class="input-group input-group-outline mb-1">
                                                    <input type="text" class="form-control" name="lastname"
                                                        value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>" />
                                                </div>
                                                <?php if (isset($errors['lastname'])): ?>
                                                    <small class="text-danger"><?php echo $errors['lastname']; ?></small>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                        <!-- Phone Number -->
                                        <div class="form-group">
                                            <label class="form-label">Phone Number</label>
                                            <div class="input-group input-group-outline mb-1">
                                                <input type="tel" class="form-control" name="phone_number"
                                                    value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>" />
                                            </div>
                                            <?php if (isset($errors['phone_number'])): ?>
                                                <small class="text-danger"><?php echo $errors['phone_number']; ?></small>
                                            <?php endif; ?>
                                        </div><!-- End .form-group -->
                                        <div class="form-group">
                                            <label class="form-label">Email</label>
                                            <div class="input-group input-group-outline mb-1">
                                                <input type="email" class="form-control" name="email"
                                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
                                            </div>
                                            <?php if (isset($errors['email'])): ?>
                                                <small class="text-danger"><?php echo $errors['email']; ?></small>
                                            <?php endif; ?>
                                        </div>


                                        <div class="form-group">
                                            <label class="form-label">Password</label>
                                            <div class="input-group input-group-outline mb-1">
                                                <input type="password" class="form-control" name="password" />
                                            </div>
                                            <?php if (isset($errors['password'])): ?>
                                                <small class="text-danger"><?php echo $errors['password']; ?></small>
                                            <?php endif; ?>
                                        </div><!-- End .form-group -->

                                        <div class="form-footer">
                                            <button type="submit" class="btn btn-outline-primary-2">
                                                <span>SIGN UP</span>
                                                <i class="icon-long-arrow-right"></i>
                                            </button>
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" value="agreed" id="flexCheckDefault" name="terms"
                                                    <?php echo isset($_POST['terms']) ? 'checked' : ''; ?> />
                                                <label class="custom-control-label" for="flexCheckDefault">
                                                    I agree to the
                                                    <a href="javascript:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                                                </label>
                                            </div>
                                            <?php if (isset($errors['terms'])): ?>
                                                <small class="text-danger"><?php echo $errors['terms']; ?></small>
                                            <?php endif; ?>


                                            <!-- End .custom-checkbox -->
                                        </div><!-- End .form-footer -->
                                    </form>
                                    <div class="form-choice">
                                        <p class="text-center">or sign in with</p>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <a href="#" class="btn btn-login btn-g">
                                                    <i class="icon-google"></i>
                                                    Login With Google
                                                </a>
                                            </div><!-- End .col-6 -->
                                            <div class="col-sm-6">
                                                <a href="#" class="btn btn-login  btn-f">
                                                    <i class="icon-facebook-f"></i>
                                                    Login With Facebook
                                                </a>
                                            </div><!-- End .col-6 -->
                                        </div><!-- End .row -->
                                    </div><!-- End .form-choice -->
                                </div><!-- .End .tab-pane -->
                            </div><!-- End .tab-content -->
                        </div><!-- End .form-tab -->
                    </div><!-- End .form-box -->
                </div><!-- End .modal-body -->
            </div><!-- End .modal-content -->
        </div><!-- End .modal-dialog -->
    </div><!-- End .modal -->


    <!-- Plugins JS File -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.hoverIntent.min.js"></script>
    <script src="assets/js/jquery.waypoints.min.js"></script>
    <script src="assets/js/superfish.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/bootstrap-input-spinner.js"></script>
    <script src="assets/js/jquery.plugin.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/jquery.countdown.min.js"></script>
    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/demos/demo-4.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.trigger-login').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    $('#signin-modal').modal('show');
                });
            });
        });
    </script>
</body>

</html>