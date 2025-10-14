<?php
// Include configuration, assuming it contains $servername, $username, etc.
include '../config.php';

// --- 1. Database Connection and ID Retrieval ---

// Get the product ID from the URL parameter
$product_id = $_GET['id'] ?? 0; // Use a default/safe value if ID is missing


// --- 2. Fetch Single Product Data ---
// Prepared statement to securely fetch product details, including category name
$sql = "SELECT p.*, c.category_name 
        FROM tbl_products p 
        JOIN tbl_categories c ON p.category_id = c.id 
        WHERE p.id = ? AND p.isActive = '1'
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id); // 'i' indicates an integer ID
$stmt->execute();
$result = $stmt->get_result();

$product = null;
if ($result->num_rows > 0) {
  $product = $result->fetch_assoc();
  // Decode attachments array (must be done after fetching)
  $product['attachments'] = json_decode($product['attachments'], true);

  // --- Helper Functions (Re-defining here for a self-contained file) ---
  // Note: If these helpers are in config.php or a separate functions.php, remove them here.
  function format_price($price)
  {
    return '$' . number_format((float)$price, 2, '.', ',');
  }
  // Dummy functions for demonstration (replace with real logic if available)
  function get_rating_width($reviews)
  {
    return (count($reviews) > 0) ? rand(60, 100) : 0;
  }
  function get_review_count($reviews)
  {
    return rand(2, 12);
  }
  // Assuming you have review data to pass to these, currently using dummy count/width
  $reviews_data = []; // Placeholder for real review data fetch
}

// Close statement and connection
$stmt->close();
$conn->close();

if (!$product) {
  // Display error if product is not found or inactive
  echo '<div class="container quickView-container"><div class="alert alert-warning">Product not found.</div></div>';
  exit;
}

// --- Setup Variables for HTML ---
$product_link = 'product.php?id=' . $product['id'];
$image_attachments = $product['attachments'] ?? [];
$price_html = format_price($product['price']);
$rating_width = get_rating_width($reviews_data);
$review_count = get_review_count($reviews_data);
$category_name = htmlspecialchars($product['category_name']);
$description = htmlspecialchars($product['description']);

?>

<div class="container quickView-container">
  <div class="quickView-content">
    <div class="row">
      <div class="col-lg-7 col-md-6">
        <div class="row">
          <div class="product-left">
            <?php
            $dot_counter = 1;
            if (!empty($image_attachments)):
              foreach ($image_attachments as $image_path):
                $hash_id = ['one', 'two', 'three', 'four', 'five'][$dot_counter - 1] ?? 'img-' . $dot_counter;
                $is_active = ($dot_counter === 1) ? ' active' : '';
            ?>
                <a href="#<?php echo $hash_id; ?>" class="carousel-dot<?php echo $is_active; ?>">
                  <img src="<?php echo htmlspecialchars('../admin_dashboard/' . $image_path); ?>" />
                </a>
            <?php
                $dot_counter++;
              endforeach;
            endif;
            ?>
          </div>

          <div class="product-right">
            <div
              class="owl-carousel owl-theme owl-nav-inside owl-light mb-0"
              data-toggle="owl"
              data-owl-options='{
                                "dots": false,
                                "nav": false, 
                                "URLhashListener": true,
                                "responsive": {"900": {"nav": true, "dots": true}}
                            }'>
              <?php
              $slide_counter = 1;
              if (!empty($image_attachments)):
                foreach ($image_attachments as $image_path):
                  $hash_id = ['one', 'two', 'three', 'four', 'five'][$slide_counter - 1] ?? 'img-' . $slide_counter;
              ?>
                  <div class="intro-slide" data-hash="<?php echo $hash_id; ?>">
                    <img
                      src="<?php echo htmlspecialchars('../admin_dashboard/' . $image_path); ?>"
                      alt="<?php echo $product['product_name']; ?> Image <?php echo $slide_counter; ?>"
                      style="max-height: 400px; object-fit: contain;" />
                    <!-- <a href="<?php echo $product_link; ?>" class="btn-fullscreen">
                      <i class="icon-arrows"></i>
                    </a> -->
                  </div>
              <?php
                  $slide_counter++;
                endforeach;
              endif;
              ?>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-md-6">
        <h2 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <h3 class="product-price"><?php echo $price_html; ?></h3>

        <div class="ratings-container">
          <div class="ratings">
            <div class="ratings-val" style="width: <?php echo $rating_width; ?>%"></div>
          </div>
          <span class="ratings-text">( <?php echo $review_count; ?> Reviews )</span>
        </div>

        <p class="product-txt"><?php echo $description; ?></p>

        <div class="details-filter-row details-row-size">
          <label for="qty">Qty:</label>
          <div class="product-details-quantity">
            <input type="number" id="qty" class="form-control" value="1" min="1" max="10" step="1" data-decimals="0" required />
          </div>
        </div>
        <div class="product-details-action">
          <div class="details-action-wrapper">
            <a href="#" class="btn-product btn-wishlist" title="Wishlist"><span>Add to Wishlist</span></a>
            <!-- <a href="#" class="btn-product btn-compare" title="Compare"><span>Add to Compare</span></a> -->
          </div>
          <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn-product btn-cart"><span>add to cart</span></a>
        </div>

        <div class="product-details-footer">
          <div class="product-cat">
            <span>Category:</span>
            <a href="category.html?id=<?php echo $product['category_id']; ?>"><?php echo $category_name; ?></a>
          </div>
          <!-- <div class="social-icons social-icons-sm">
            <span class="social-label">Share:</span>
          </div> -->
        </div>
      </div>
    </div>
  </div>
</div>