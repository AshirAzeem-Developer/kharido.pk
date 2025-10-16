<?php
// Include configuration, assuming it contains $servername, $username, etc.
include 'config.php';

// --- 1. Database Connection and ID Retrieval ---

// Retrieve the product ID from the URL (e.g., product.php?id=5)
$product_id = $_GET['id'] ?? 0;

// --- 2. Fetch Single Product Data ---

// Prepared statement to securely fetch product details, including category name
$sql = "SELECT p.*, c.category_name 
        FROM tbl_products p 
        JOIN tbl_categories c ON p.category_id = c.id 
        WHERE p.id = ? AND p.isActive = '1'
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$product = null;
if ($result->num_rows > 0) {
  $product = $result->fetch_assoc();
  // Decode attachments array 
  $product['attachments'] = json_decode($product['attachments'], true);
}

// Close connection immediately after fetching data
$stmt->close();
$conn->close();

if (!$product) {
  // If no product is found, redirect or show an error page
  header("Location: 404.html");
  exit;
}

// --- 3. Setup Variables and Helper Functions (for clean HTML output) ---

// Helper function to format price
function format_price($price)
{
  return '$' . number_format((float)$price, 2, '.', ',');
}

// Dummy functions for demonstration (replace with real data in a production environment)
function get_rating_width()
{
  return rand(60, 100);
}
function get_review_count()
{
  return rand(2, 12);
}

$product_title = htmlspecialchars($product['product_name']);
$product_price = format_price($product['price']);
$product_description = nl2br(htmlspecialchars($product['description'])); // Use nl2br for paragraphs
$category_name = htmlspecialchars($product['category_name']);
$image_attachments = $product['attachments'] ?? [];

// Dummy Ratings
$rating_width = get_rating_width();
$review_count = get_review_count();
?>

<!DOCTYPE html>
<html lang="en">
<!-- molla/product.html  22 Nov 2019 09:54:50 GMT -->

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Molla - eCommerce</title>
  <meta name="keywords" content="HTML5 Template" />
  <meta name="description" content="Molla - eCommerce" />
  <meta name="author" content="p-themes" />
  <!-- Favicon -->
  <link
    rel="apple-touch-icon"
    sizes="180x180"
    href="assets/images/icons/apple-touch-icon.png" />
  <link
    rel="icon"
    type="image/png"
    sizes="32x32"
    href="assets/images/icons/favicon-32x32.png" />
  <link
    rel="icon"
    type="image/png"
    sizes="16x16"
    href="assets/images/icons/favicon-16x16.png" />
  <link rel="manifest" href="assets/images/icons/site.html" />
  <link
    rel="mask-icon"
    href="assets/images/icons/safari-pinned-tab.svg"
    color="#666666" />
  <link rel="shortcut icon" href="assets/images/icons/favicon.ico" />
  <meta name="apple-mobile-web-app-title" content="Molla" />
  <meta name="application-name" content="Molla" />
  <meta name="msapplication-TileColor" content="#cc9966" />
  <meta
    name="msapplication-config"
    content="assets/images/icons/browserconfig.xml" />
  <meta name="theme-color" content="#ffffff" />
  <!-- Plugins CSS File -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
  <link
    rel="stylesheet"
    href="assets/css/plugins/owl-carousel/owl.carousel.css" />
  <link
    rel="stylesheet"
    href="assets/css/plugins/magnific-popup/magnific-popup.css" />
  <!-- Main CSS File -->
  <link rel="stylesheet" href="assets/css/style.css" />
  <link
    rel="stylesheet"
    href="assets/css/plugins/nouislider/nouislider.css" />
</head>

<body>
  <div class="page-wrapper">
    <?php include 'layout/header.php'; ?>

    <main class="main">
      <nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0">
        <div class="container d-flex align-items-center">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">
              <?php echo $product_title; ?>
            </li>
          </ol>

          <nav class="product-pager ml-auto" aria-label="Product">
            <a
              class="product-pager-link product-pager-prev"
              href="#"
              aria-label="Previous"
              tabindex="-1">
              <i class="icon-angle-left"></i>
              <span>Prev</span>
            </a>

            <a
              class="product-pager-link product-pager-next"
              href="#"
              aria-label="Next"
              tabindex="-1">
              <span>Next</span>
              <i class="icon-angle-right"></i>
            </a>
          </nav>
          <!-- End .pager-nav -->
        </div>
        <!-- End .container -->
      </nav>
      <!-- End .breadcrumb-nav -->

      <div class="page-content">
        <div class="container">
          <div class="product-details-top">
            <div class="row">
              <div class="col-md-6">
                <div class="product-gallery product-gallery-vertical">
                  <div class="row">
                    <figure class="product-main-image">
                      <?php
                      $main_image_src = '../admin_dashboard/' . ($image_attachments[0] ?? 'assets/images/placeholder.jpg');
                      ?>
                      <img
                        id="product-zoom"
                        src="<?php echo $main_image_src; ?>"
                        data-zoom-image="<?php echo $main_image_src; ?>"
                        alt="<?php echo $product_title; ?>"
                        style="max-height: 500px; object-fit: contain;" />

                      <a href="#" id="btn-product-gallery" class="btn-product-gallery">
                        <i class="icon-arrows"></i>
                      </a>
                    </figure>
                    <!-- End .product-main-image -->

                    <div id="product-zoom-gallery" class="product-image-gallery">
                      <?php
                      $thumb_counter = 0;
                      foreach ($image_attachments as $image_path):
                        $thumb_src = '../admin_dashboard/' . $image_path;
                        $is_active = ($thumb_counter === 0) ? ' active' : '';
                      ?>
                        <a class="product-gallery-item<?php echo $is_active; ?>" href="#"
                          data-image="<?php echo $thumb_src; ?>"
                          data-zoom-image="<?php echo $thumb_src; ?>">
                          <img src="<?php echo $thumb_src; ?>" alt="<?php echo $product_title . ' Thumbnail ' . ($thumb_counter + 1); ?>" />
                        </a>
                      <?php
                        $thumb_counter++;
                      endforeach;
                      ?>
                    </div>
                    <!-- End .product-image-gallery -->
                  </div>
                  <!-- End .row -->
                </div>
                <!-- End .product-gallery -->
              </div>
              <!-- End .col-md-6 -->

              <div class="col-md-6">
                <div class="product-details">
                  <h1 class="product-title">
                    <?php echo $product_title; ?>
                  </h1>
                  <!-- End .product-title -->

                  <div class="ratings-container">
                    <div class="ratings">
                      <div class="ratings-val" style="width: <?php echo $rating_width; ?>%"></div>
                    </div>
                    <a
                      class="ratings-text"
                      href="#product-review-link"
                      id="review-link">( <?php echo $review_count; ?> Reviews )</a>
                  </div>
                  <!-- End .rating-container -->

                  <div class="product-price"><?php echo $product_price; ?></div>
                  <div class="product-content">
                    <p><?php echo $product_description; ?></p>
                  </div>
                  <!-- End .product-content -->

                  <div class="details-filter-row details-row-size">
                    <label for="qty">Qty:</label>
                    <div class="product-details-quantity">
                      <input
                        type="number"
                        id="qty"
                        class="form-control"
                        value="1"
                        min="1"
                        max="10"
                        step="1"
                        data-decimals="0"
                        required />
                    </div>
                  </div>
                  <!-- End .details-filter-row -->

                  <div class="product-details-action">
                    <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn-product btn-cart"><span>add to cart</span></a>

                    <div class="details-action-wrapper">
                      <a href="#" class="btn-product btn-wishlist" title="Wishlist"><span>Add to Wishlist</span></a>
                      <a href="#" class="btn-product btn-compare" title="Compare"><span>Add to Compare</span></a>
                    </div>
                  </div>
                  <!-- End .product-details-action -->

                  <div class="product-details-footer">
                    <div class="product-cat">
                      <span>Category:</span>
                      <a href="category.html?id=<?php echo $product['category_id']; ?>"><?php echo $category_name; ?></a>
                    </div>
                    <div class="social-icons social-icons-sm">
                      <span class="social-label">Share:</span>
                    </div>
                  </div>
                  <!-- End .product-details-footer -->
                </div>
                <!-- End .product-details -->
              </div>
              <!-- End .col-md-6 -->
            </div>
            <!-- End .row -->
          </div>
          <!-- End .product-details-top -->

          <div class="product-details-tab">
            <div class="tab-content">
              <div
                class="tab-pane fade show active"
                id="product-desc-tab"
                role="tabpanel"
                aria-labelledby="product-desc-link">
                <div class="product-desc-content">
                  <h3>Product Description</h3>
                  <p><?php echo $product_description; ?></p>

                </div>
              </div>
            </div>
          </div>
          <!-- End .product-details-tab -->

          <h2 class="title text-center mb-4">You May Also Like</h2>
          <!-- End .title text-center -->

          <div
            class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow"
            data-toggle="owl"
            data-owl-options='{
                            "nav": false, 
                            "dots": true,
                            "margin": 20,
                            "loop": false,
                            "responsive": {
                                "0": {
                                    "items":1
                                },
                                "480": {
                                    "items":2
                                },
                                "768": {
                                    "items":3
                                },
                                "992": {
                                    "items":4
                                },
                                "1200": {
                                    "items":4,
                                    "nav": true,
                                    "dots": false
                                }
                            }
                        }'>
            <div class="product product-7 text-center">
              <figure class="product-media">
                <span class="product-label label-new">New</span>
                <a href="product.html">
                  <img
                    src="assets/images/products/product-4.jpg"
                    alt="Product image"
                    class="product-image" />
                </a>

                <div class="product-action-vertical">
                  <a
                    href="#"
                    class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                  <a
                    href="popup/quickView.html"
                    class="btn-product-icon btn-quickview"
                    title="Quick view"><span>Quick view</span></a>
                  <a
                    href="#"
                    class="btn-product-icon btn-compare"
                    title="Compare"><span>Compare</span></a>
                </div>
                <!-- End .product-action-vertical -->

                <div class="product-action">
                  <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>
                </div>
                <!-- End .product-action -->
              </figure>
              <!-- End .product-media -->

              <div class="product-body">
                <div class="product-cat">
                  <a href="#">Women</a>
                </div>
                <!-- End .product-cat -->
                <h3 class="product-title">
                  <a href="product.html">Brown paperbag waist <br />pencil skirt</a>
                </h3>
                <!-- End .product-title -->
                <div class="product-price">$60.00</div>
                <!-- End .product-price -->
                <div class="ratings-container">
                  <div class="ratings">
                    <div class="ratings-val" style="width: 20%"></div>
                    <!-- End .ratings-val -->
                  </div>
                  <!-- End .ratings -->
                  <span class="ratings-text">( 2 Reviews )</span>
                </div>
                <!-- End .rating-container -->

                <div class="product-nav product-nav-thumbs">
                  <a href="#" class="active">
                    <img
                      src="assets/images/products/product-4-thumb.jpg"
                      alt="product desc" />
                  </a>
                  <a href="#">
                    <img
                      src="assets/images/products/product-4-2-thumb.jpg"
                      alt="product desc" />
                  </a>

                  <a href="#">
                    <img
                      src="assets/images/products/product-4-3-thumb.jpg"
                      alt="product desc" />
                  </a>
                </div>
                <!-- End .product-nav -->
              </div>
              <!-- End .product-body -->
            </div>
            <!-- End .product -->

            <div class="product product-7 text-center">
              <figure class="product-media">
                <span class="product-label label-out">Out of Stock</span>
                <a href="product.html">
                  <img
                    src="assets/images/products/product-6.jpg"
                    alt="Product image"
                    class="product-image" />
                </a>

                <div class="product-action-vertical">
                  <a
                    href="#"
                    class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                  <a
                    href="popup/quickView.html"
                    class="btn-product-icon btn-quickview"
                    title="Quick view"><span>Quick view</span></a>
                  <a
                    href="#"
                    class="btn-product-icon btn-compare"
                    title="Compare"><span>Compare</span></a>
                </div>
                <!-- End .product-action-vertical -->

                <div class="product-action">
                  <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>
                </div>
                <!-- End .product-action -->
              </figure>
              <!-- End .product-media -->

              <div class="product-body">
                <div class="product-cat">
                  <a href="#">Jackets</a>
                </div>
                <!-- End .product-cat -->
                <h3 class="product-title">
                  <a href="product.html">Khaki utility boiler jumpsuit</a>
                </h3>
                <!-- End .product-title -->
                <div class="product-price">
                  <span class="out-price">$120.00</span>
                </div>
                <!-- End .product-price -->
                <div class="ratings-container">
                  <div class="ratings">
                    <div class="ratings-val" style="width: 80%"></div>
                    <!-- End .ratings-val -->
                  </div>
                  <!-- End .ratings -->
                  <span class="ratings-text">( 6 Reviews )</span>
                </div>
                <!-- End .rating-container -->
              </div>
              <!-- End .product-body -->
            </div>
            <!-- End .product -->

            <div class="product product-7 text-center">
              <figure class="product-media">
                <span class="product-label label-top">Top</span>
                <a href="product.html">
                  <img
                    src="assets/images/products/product-11.jpg"
                    alt="Product image"
                    class="product-image" />
                </a>

                <div class="product-action-vertical">
                  <a
                    href="#"
                    class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                  <a
                    href="popup/quickView.html"
                    class="btn-product-icon btn-quickview"
                    title="Quick view"><span>Quick view</span></a>
                  <a
                    href="#"
                    class="btn-product-icon btn-compare"
                    title="Compare"><span>Compare</span></a>
                </div>
                <!-- End .product-action-vertical -->

                <div class="product-action">
                  <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>
                </div>
                <!-- End .product-action -->
              </figure>
              <!-- End .product-media -->

              <div class="product-body">
                <div class="product-cat">
                  <a href="#">Shoes</a>
                </div>
                <!-- End .product-cat -->
                <h3 class="product-title">
                  <a href="product.html">Light brown studded Wide fit wedges</a>
                </h3>
                <!-- End .product-title -->
                <div class="product-price">$110.00</div>
                <!-- End .product-price -->
                <div class="ratings-container">
                  <div class="ratings">
                    <div class="ratings-val" style="width: 80%"></div>
                    <!-- End .ratings-val -->
                  </div>
                  <!-- End .ratings -->
                  <span class="ratings-text">( 1 Reviews )</span>
                </div>
                <!-- End .rating-container -->

                <div class="product-nav product-nav-thumbs">
                  <a href="#" class="active">
                    <img
                      src="assets/images/products/product-11-thumb.jpg"
                      alt="product desc" />
                  </a>
                  <a href="#">
                    <img
                      src="assets/images/products/product-11-2-thumb.jpg"
                      alt="product desc" />
                  </a>

                  <a href="#">
                    <img
                      src="assets/images/products/product-11-3-thumb.jpg"
                      alt="product desc" />
                  </a>
                </div>
                <!-- End .product-nav -->
              </div>
              <!-- End .product-body -->
            </div>
            <!-- End .product -->

            <div class="product product-7 text-center">
              <figure class="product-media">
                <a href="product.html">
                  <img
                    src="assets/images/products/product-10.jpg"
                    alt="Product image"
                    class="product-image" />
                </a>

                <div class="product-action-vertical">
                  <a
                    href="#"
                    class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                  <a
                    href="popup/quickView.html"
                    class="btn-product-icon btn-quickview"
                    title="Quick view"><span>Quick view</span></a>
                  <a
                    href="#"
                    class="btn-product-icon btn-compare"
                    title="Compare"><span>Compare</span></a>
                </div>
                <!-- End .product-action-vertical -->

                <div class="product-action">
                  <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>
                </div>
                <!-- End .product-action -->
              </figure>
              <!-- End .product-media -->

              <div class="product-body">
                <div class="product-cat">
                  <a href="#">Jumpers</a>
                </div>
                <!-- End .product-cat -->
                <h3 class="product-title">
                  <a href="product.html">Yellow button front tea top</a>
                </h3>
                <!-- End .product-title -->
                <div class="product-price">$56.00</div>
                <!-- End .product-price -->
                <div class="ratings-container">
                  <div class="ratings">
                    <div class="ratings-val" style="width: 0%"></div>
                    <!-- End .ratings-val -->
                  </div>
                  <!-- End .ratings -->
                  <span class="ratings-text">( 0 Reviews )</span>
                </div>
                <!-- End .rating-container -->
              </div>
              <!-- End .product-body -->
            </div>
            <!-- End .product -->

            <div class="product product-7 text-center">
              <figure class="product-media">
                <a href="product.html">
                  <img
                    src="assets/images/products/product-7.jpg"
                    alt="Product image"
                    class="product-image" />
                </a>

                <div class="product-action-vertical">
                  <a
                    href="#"
                    class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                  <a
                    href="popup/quickView.html"
                    class="btn-product-icon btn-quickview"
                    title="Quick view"><span>Quick view</span></a>
                  <a
                    href="#"
                    class="btn-product-icon btn-compare"
                    title="Compare"><span>Compare</span></a>
                </div>
                <!-- End .product-action-vertical -->

                <div class="product-action">
                  <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>
                </div>
                <!-- End .product-action -->
              </figure>
              <!-- End .product-media -->

              <div class="product-body">
                <div class="product-cat">
                  <a href="#">Jeans</a>
                </div>
                <!-- End .product-cat -->
                <h3 class="product-title">
                  <a href="product.html">Blue utility pinafore denim dress</a>
                </h3>
                <!-- End .product-title -->
                <div class="product-price">$76.00</div>
                <!-- End .product-price -->
                <div class="ratings-container">
                  <div class="ratings">
                    <div class="ratings-val" style="width: 20%"></div>
                    <!-- End .ratings-val -->
                  </div>
                  <!-- End .ratings -->
                  <span class="ratings-text">( 2 Reviews )</span>
                </div>
                <!-- End .rating-container -->
              </div>
              <!-- End .product-body -->
            </div>
            <!-- End .product -->
          </div>
          <!-- End .owl-carousel -->
        </div>
        <!-- End .container -->
      </div>
      <!-- End .page-content -->
    </main>
    <!-- End .main -->

    <?php include 'layout/footer.php'; ?>
    <!-- End .footer -->
  </div>
  <!-- End .page-wrapper -->
  <button id="scroll-top" title="Back to Top">
    <i class="icon-arrow-up"></i>
  </button>

  <!-- Sticky Bar -->
  <div class="sticky-bar bg-white">
    <div class="container">
      <div class="row">
        <div class="col-6">
          <figure class="product-media">
            <a href="product.html">
              <img
                src="assets/images/products/sticky/product-1.jpg"
                alt="Product image" />
            </a>
          </figure>
          <!-- End .product-media -->
          <h4 class="product-title">
            <a href="product.html">Dark yellow lace cut out swing dress</a>
          </h4>
          <!-- End .product-title -->
        </div>
        <!-- End .col-6 -->

        <div class="col-6 justify-content-end">
          <div class="product-price">$84.00</div>


          <div class="product-details-action">
            <a href="#" class="btn-product btn-cart"><span>add to cart</span></a>
            <a href="#" class="btn-product btn-wishlist" title="Wishlist"><span>Add to Wishlist</span></a>
          </div>
          <!-- End .product-details-action -->
        </div>
        <!-- End .col-6 -->
      </div>
      <!-- End .row -->
    </div>
    <!-- End .container -->
  </div>
  <!-- End .sticky-bar -->

  <!-- Mobile Menu -->
  <div class="mobile-menu-overlay"></div>
  <!-- End .mobil-menu-overlay -->

  <div class="mobile-menu-container">
    <div class="mobile-menu-wrapper">
      <span class="mobile-menu-close"><i class="icon-close"></i></span>

      <form action="#" method="get" class="mobile-search">
        <label for="mobile-search" class="sr-only">Search</label>
        <input
          type="search"
          class="form-control"
          name="mobile-search"
          id="mobile-search"
          placeholder="Search in..."
          required />
        <button class="btn btn-primary" type="submit">
          <i class="icon-search"></i>
        </button>
      </form>

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
              <li>
                <a href="index-18.html">18 - fashion store (with sidebar)</a>
              </li>
              <li><a href="index-19.html">19 - games store</a></li>
              <li><a href="index-20.html">20 - book store</a></li>
              <li><a href="index-21.html">21 - sport store</a></li>
              <li><a href="index-22.html">22 - tools store</a></li>
              <li>
                <a href="index-23.html">23 - fashion left navigation store</a>
              </li>
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
              <li>
                <a href="category-boxed.html"><span>Shop Boxed No Sidebar<span class="tip tip-hot">Hot</span></span></a>
              </li>
              <li>
                <a href="category-fullwidth.html">Shop Fullwidth No Sidebar</a>
              </li>
              <li>
                <a href="product-category-boxed.html">Product Category Boxed</a>
              </li>
              <li>
                <a href="product-category-fullwidth.html"><span>Product Category Fullwidth<span class="tip tip-new">New</span></span></a>
              </li>
              <li><a href="cart.php">Cart</a></li>
              <li><a href="checkout.php">Checkout</a></li>
              <li><a href="wishlist.html">Wishlist</a></li>
              <li><a href="#">Lookbook</a></li>
            </ul>
          </li>
          <li>
            <a href="product.html" class="sf-with-ul">Product</a>
            <ul>
              <li><a href="product.html">Default</a></li>
              <li><a href="product-centered.html">Centered</a></li>
              <li>
                <a href="product-extended.html"><span>Extended Info<span class="tip tip-new">New</span></span></a>
              </li>
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
                  <li>
                    <a href="blog-masonry-2cols.html">Masonry 2 columns</a>
                  </li>
                  <li>
                    <a href="blog-masonry-3cols.html">Masonry 3 columns</a>
                  </li>
                  <li>
                    <a href="blog-masonry-4cols.html">Masonry 4 columns</a>
                  </li>
                  <li>
                    <a href="blog-masonry-sidebar.html">Masonry sidebar</a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="#">Mask</a>
                <ul>
                  <li><a href="blog-mask-grid.html">Blog mask grid</a></li>
                  <li>
                    <a href="blog-mask-masonry.html">Blog mask masonry</a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="#">Single Post</a>
                <ul>
                  <li><a href="single.html">Default with sidebar</a></li>
                  <li>
                    <a href="single-fullwidth.html">Fullwidth no sidebar</a>
                  </li>
                  <li>
                    <a href="single-fullwidth-sidebar.html">Fullwidth with sidebar</a>
                  </li>
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
              <li>
                <a href="elements-product-category.html">Product Category</a>
              </li>
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
      </nav>
      <!-- End .mobile-nav -->

      <div class="social-icons">
        <a href="#" class="social-icon" target="_blank" title="Facebook"><i class="icon-facebook-f"></i></a>
        <a href="#" class="social-icon" target="_blank" title="Twitter"><i class="icon-twitter"></i></a>
        <a href="#" class="social-icon" target="_blank" title="Instagram"><i class="icon-instagram"></i></a>
        <a href="#" class="social-icon" target="_blank" title="Youtube"><i class="icon-youtube"></i></a>
      </div>
      <!-- End .social-icons -->
    </div>
    <!-- End .mobile-menu-wrapper -->
  </div>
  <!-- End .mobile-menu-container -->

  <!-- Sign in / Register Modal -->
  <div
    class="modal fade"
    id="signin-modal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close">
            <span aria-hidden="true"><i class="icon-close"></i></span>
          </button>

          <div class="form-box">
            <div class="form-tab">
              <ul class="nav nav-pills nav-fill" role="tablist">
                <li class="nav-item">
                  <a
                    class="nav-link active"
                    id="signin-tab"
                    data-toggle="tab"
                    href="#signin"
                    role="tab"
                    aria-controls="signin"
                    aria-selected="true">Sign In</a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link"
                    id="register-tab"
                    data-toggle="tab"
                    href="#register"
                    role="tab"
                    aria-controls="register"
                    aria-selected="false">Register</a>
                </li>
              </ul>
              <div class="tab-content" id="tab-content-5">
                <div
                  class="tab-pane fade show active"
                  id="signin"
                  role="tabpanel"
                  aria-labelledby="signin-tab">
                  <form action="#">
                    <div class="form-group">
                      <label for="singin-email">Username or email address *</label>
                      <input
                        type="text"
                        class="form-control"
                        id="singin-email"
                        name="singin-email"
                        required />
                    </div>
                    <!-- End .form-group -->

                    <div class="form-group">
                      <label for="singin-password">Password *</label>
                      <input
                        type="password"
                        class="form-control"
                        id="singin-password"
                        name="singin-password"
                        required />
                    </div>
                    <!-- End .form-group -->

                    <div class="form-footer">
                      <button type="submit" class="btn btn-outline-primary-2">
                        <span>LOG IN</span>
                        <i class="icon-long-arrow-right"></i>
                      </button>

                      <div class="custom-control custom-checkbox">
                        <input
                          type="checkbox"
                          class="custom-control-input"
                          id="signin-remember" />
                        <label
                          class="custom-control-label"
                          for="signin-remember">Remember Me</label>
                      </div>
                      <!-- End .custom-checkbox -->

                      <a href="#" class="forgot-link">Forgot Your Password?</a>
                    </div>
                    <!-- End .form-footer -->
                  </form>
                  <div class="form-choice">
                    <p class="text-center">or sign in with</p>
                    <div class="row">
                      <div class="col-sm-6">
                        <a href="#" class="btn btn-login btn-g">
                          <i class="icon-google"></i>
                          Login With Google
                        </a>
                      </div>
                      <!-- End .col-6 -->
                      <div class="col-sm-6">
                        <a href="#" class="btn btn-login btn-f">
                          <i class="icon-facebook-f"></i>
                          Login With Facebook
                        </a>
                      </div>
                      <!-- End .col-6 -->
                    </div>
                    <!-- End .row -->
                  </div>
                  <!-- End .form-choice -->
                </div>
                <!-- .End .tab-pane -->
                <div
                  class="tab-pane fade"
                  id="register"
                  role="tabpanel"
                  aria-labelledby="register-tab">
                  <form action="#">
                    <div class="form-group">
                      <label for="register-email">Your email address *</label>
                      <input
                        type="email"
                        class="form-control"
                        id="register-email"
                        name="register-email"
                        required />
                    </div>
                    <!-- End .form-group -->

                    <div class="form-group">
                      <label for="register-password">Password *</label>
                      <input
                        type="password"
                        class="form-control"
                        id="register-password"
                        name="register-password"
                        required />
                    </div>
                    <!-- End .form-group -->

                    <div class="form-footer">
                      <button type="submit" class="btn btn-outline-primary-2">
                        <span>SIGN UP</span>
                        <i class="icon-long-arrow-right"></i>
                      </button>

                      <div class="custom-control custom-checkbox">
                        <input
                          type="checkbox"
                          class="custom-control-input"
                          id="register-policy"
                          required />
                        <label
                          class="custom-control-label"
                          for="register-policy">I agree to the
                          <a href="#">privacy policy</a> *</label>
                      </div>
                      <!-- End .custom-checkbox -->
                    </div>
                    <!-- End .form-footer -->
                  </form>
                  <div class="form-choice">
                    <p class="text-center">or sign in with</p>
                    <div class="row">
                      <div class="col-sm-6">
                        <a href="#" class="btn btn-login btn-g">
                          <i class="icon-google"></i>
                          Login With Google
                        </a>
                      </div>
                      <!-- End .col-6 -->
                      <div class="col-sm-6">
                        <a href="#" class="btn btn-login btn-f">
                          <i class="icon-facebook-f"></i>
                          Login With Facebook
                        </a>
                      </div>
                      <!-- End .col-6 -->
                    </div>
                    <!-- End .row -->
                  </div>
                  <!-- End .form-choice -->
                </div>
                <!-- .End .tab-pane -->
              </div>
              <!-- End .tab-content -->
            </div>
            <!-- End .form-tab -->
          </div>
          <!-- End .form-box -->
        </div>
        <!-- End .modal-body -->
      </div>
      <!-- End .modal-content -->
    </div>
    <!-- End .modal-dialog -->
  </div>
  <!-- End .modal -->

  <!-- Plugins JS File -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/jquery.hoverIntent.min.js"></script>
  <script src="assets/js/jquery.waypoints.min.js"></script>
  <script src="assets/js/superfish.min.js"></script>
  <script src="assets/js/owl.carousel.min.js"></script>
  <script src="assets/js/bootstrap-input-spinner.js"></script>
  <script src="assets/js/jquery.elevateZoom.min.js"></script>
  <script src="assets/js/bootstrap-input-spinner.js"></script>
  <script src="assets/js/jquery.magnific-popup.min.js"></script>
  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
</body>

</html>