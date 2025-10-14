  <?php include './config.php'; ?>
  <?php
    // --- Fetch Categories ---
    $all_categories = [];
    $sql_categories = "SELECT id, category_name FROM tbl_categories ORDER BY category_name ASC";
    $cat_result = $conn->query($sql_categories);

    if ($cat_result && $cat_result->num_rows > 0) {
        while ($row = $cat_result->fetch_assoc()) {
            $all_categories[] = $row;
        }
    }
    // We will use the $all_categories array to populate the dropdown menu.
    ?>
  <?php
    $user_id = $_SESSION['user_id'] ?? 0; // assuming user is logged in
    $cart_items = [];
    $cart_total = 0;


    $sql_cart_count = "SELECT COUNT(*) AS cart_count FROM tbl_cart WHERE user_id = $user_id";
    $result_cart_count = mysqli_query($conn, $sql_cart_count);
    $row_cart_count = mysqli_fetch_assoc($result_cart_count);
    $cart_count = $row_cart_count['cart_count'] ?? 0;

    if ($user_id > 0) {
        $sql_cart = "SELECT c.*, p.product_name, p.price, p.attachments FROM tbl_cart c
        JOIN tbl_products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";


        $cart_result = $conn->query($sql_cart);

        if ($cart_result && $cart_result->num_rows > 0) {
            while ($row = $cart_result->fetch_assoc()) {
                $cart_items[] = $row;
                $cart_total += ($row['price'] * $row['quantity']);
            }
        }
    }
    ?>

  <header class="header header-intro-clearance header-4">
      <div class="header-top">
          <div class="container">
              <div class="header-left">
                  <a href="tel:#"><i class="icon-phone"></i>Call: +0123 456 789</a>
              </div><!-- End .header-left -->

              <div class="header-right">

                  <ul class="top-menu">
                      <li>
                          <a href="#">Links</a>
                          <ul>
                              <li>
                                  <div class="header-dropdown">
                                      <a href="#">USD</a>
                                      <div class="header-menu">
                                          <ul>
                                              <li><a href="#">Eur</a></li>
                                              <li><a href="#">Usd</a></li>
                                          </ul>
                                      </div><!-- End .header-menu -->
                                  </div>
                              </li>
                              <li>
                                  <div class="header-dropdown">
                                      <a href="#">English</a>
                                      <div class="header-menu">
                                          <ul>
                                              <li><a href="#">English</a></li>
                                              <li><a href="#">French</a></li>
                                              <li><a href="#">Spanish</a></li>
                                          </ul>
                                      </div><!-- End .header-menu -->
                                  </div>
                              </li>
                              <li><a href="#signin-modal" data-toggle="modal">Sign in / Sign up</a></li>
                          </ul>
                      </li>
                  </ul><!-- End .top-menu -->
              </div><!-- End .header-right -->

          </div><!-- End .container -->
      </div><!-- End .header-top -->

      <div class="header-middle">
          <div class="container">
              <div class="header-left">
                  <button class="mobile-menu-toggler">
                      <span class="sr-only">Toggle mobile menu</span>
                      <i class="icon-bars"></i>
                  </button>

                  <a href="index.html" class="logo">
                      <img src="assets/images/demos/demo-4/logo.png" alt="Molla Logo" width="105" height="25">
                  </a>
              </div><!-- End .header-left -->

              <div class="header-center">
                  <div class="header-search header-search-extended header-search-visible d-none d-lg-block">
                      <a href="#" class="search-toggle" role="button"><i class="icon-search"></i></a>
                      <form action="#" method="get">
                          <div class="header-search-wrapper search-wrapper-wide">
                              <label for="q" class="sr-only">Search</label>
                              <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
                              <input type="search" class="form-control" name="q" id="q" placeholder="Search product ..." required>
                          </div><!-- End .header-search-wrapper -->
                      </form>
                  </div><!-- End .header-search -->
              </div>

              <div class="header-right">
                  <div class="dropdown compare-dropdown">


                      <div class="dropdown-menu dropdown-menu-right">
                          <ul class="compare-products">
                              <li class="compare-product">
                                  <a href="#" class="btn-remove" title="Remove Product"><i class="icon-close"></i></a>
                                  <h4 class="compare-product-title"><a href="product.html">Blue Night Dress</a></h4>
                              </li>
                              <li class="compare-product">
                                  <a href="#" class="btn-remove" title="Remove Product"><i class="icon-close"></i></a>
                                  <h4 class="compare-product-title"><a href="product.html">White Long Skirt</a></h4>
                              </li>
                          </ul>

                          <div class="compare-actions">
                              <a href="#" class="action-link">Clear All</a>
                              <a href="#" class="btn btn-outline-primary-2"><span>Compare</span><i class="icon-long-arrow-right"></i></a>
                          </div>
                      </div><!-- End .dropdown-menu -->
                  </div><!-- End .compare-dropdown -->

                  <div class="wishlist">
                      <a href="wishlist.html" title="Wishlist">
                          <div class="icon">
                              <i class="icon-heart-o"></i>
                              <span class="wishlist-count badge">3</span>
                          </div>
                          <p>Wishlist</p>
                      </a>
                  </div><!-- End .compare-dropdown -->

                  <div class="dropdown cart-dropdown">
                      <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
                          <div class="icon">
                              <i class="icon-shopping-cart"></i>
                              <span class="cart-count"><?= $cart_count ?></span>
                          </div>
                          <p>Cart</p>
                      </a>


                      <div class="dropdown-menu dropdown-menu-right">
                          <div class="dropdown-cart-products">
                              <?php if (!empty($cart_items)) : ?>
                                  <?php foreach ($cart_items as $item) : ?>
                                      <div class="product">
                                          <div class="product-cart-details">
                                              <h4 class="product-title">
                                                  <a href="product.php?id=<?= $item['product_id'] ?>">
                                                      <?= htmlspecialchars($item['product_name']) ?>
                                                  </a>
                                              </h4>
                                              <span class="cart-product-info">
                                                  <span class="cart-product-qty"><?= $item['quantity'] ?></span>
                                                  x $<?= number_format($item['price'], 2) ?>
                                              </span>
                                          </div>

                                          <figure class="product-image-container">
                                              <a href="product.php?id=<?= $item['product_id'] ?>" class="product-image">
                                                  <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="product">
                                              </a>
                                          </figure>
                                          <a href="remove_from_cart.php?id=<?= $item['id'] ?>" class="btn-remove" title="Remove Product">
                                              <i class="icon-close"></i>
                                          </a>
                                      </div>
                                  <?php endforeach; ?>
                              <?php else : ?>
                                  <p class="text-center p-3">Your cart is empty</p>
                              <?php endif; ?>
                          </div>

                          <?php if (!empty($cart_items)) : ?>
                              <div class="dropdown-cart-total">
                                  <span>Total</span>
                                  <span class="cart-total-price">$<?= number_format($cart_total, 2) ?></span>
                              </div>

                              <div class="dropdown-cart-action">
                                  <a href="cart.php" class="btn btn-primary">View Cart</a>
                                  <a href="checkout.php" class="btn btn-outline-primary-2">
                                      <span>Checkout</span><i class="icon-long-arrow-right"></i>
                                  </a>
                              </div>
                          <?php endif; ?>
                      </div>

                  </div><!-- End .cart-dropdown -->
              </div><!-- End .header-right -->
          </div><!-- End .container -->
      </div><!-- End .header-middle -->
      <div class="header-bottom sticky-header">
          <div class="container">
              <div class="header-left">
                  <div class="dropdown category-dropdown">
                      <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static" title="Browse Categories">
                          Browse Categories <i class="icon-angle-down"></i>
                      </a>

                      <div class="dropdown-menu">
                          <nav class="side-nav">
                              <ul class="menu-vertical sf-arrows">
                                  <li class="item-lead"><a href="#">Daily offers</a></li>
                                  <li class="item-lead"><a href="#">Gift Ideas</a></li>

                                  <?php
                                    if (!empty($all_categories)):
                                        foreach ($all_categories as $category):
                                            // Set the link to a dynamic category page using the ID
                                            $category_link = "category.html?id=" . $category['id'];
                                    ?>
                                          <li>
                                              <a href="<?php echo $category_link; ?>">
                                                  <?php echo htmlspecialchars($category['category_name']); ?>
                                              </a>
                                          </li>
                                      <?php
                                        endforeach;
                                    else:
                                        ?>
                                      <li><a href="#">No categories found</a></li>
                                  <?php endif; ?>
                              </ul>
                          </nav>
                      </div>
                  </div>
              </div>
              <div class="header-right">
                  <i class="la la-lightbulb-o"></i>
                  <p>Clearance<span class="highlight">&nbsp;Up to 30% Off</span></p>
              </div>
          </div>
      </div>
      <!-- End .header-bottom -->
  </header><!-- End .header -->