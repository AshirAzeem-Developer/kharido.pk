<?php
session_start(); // 💡 CRITICAL FIX: Session MUST be started here
include '../config.php';
require_once 'email_sender.php';
require_once 'email_template.php';

// 1. Security & Validation
$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0 || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit();
}

// Extract POST data
$total_amount = floatval($_POST['total_amount'] ?? 0);
$shipping_cost = floatval($_POST['shipping_cost'] ?? 0);
$payment_method = $_POST['payment_method'] ?? 'cod';

// Ensure this file only handles COD submissions
if ($payment_method !== 'cod') {
    header("Location: ../checkout.php?error=Invalid Payment Method Submitted to COD Processor");
    exit();
}

// Collect address components
$address1 = trim($_POST['address1'] ?? '');
$address2 = trim($_POST['address2'] ?? '');
$city     = trim($_POST['city'] ?? '');
$state    = trim($_POST['state'] ?? '');
$postcode = trim($_POST['postcode'] ?? '');
$country  = trim($_POST['country'] ?? '');

$shipping_address = implode(', ', array_filter([$address1, $address2, $city, $state, $postcode, $country]));
$billing_address = $shipping_address;

// ----------------------------------------------------------------------
// 💡 CRITICAL FIX: DB Connection & Transaction Management is placed here
// ----------------------------------------------------------------------
$conn = null;
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database Connection failed.");
    }
    $conn->begin_transaction();

    // 2. RE-FETCH USER EMAIL AND NAME (CRITICAL FOR EMAIL)
    $user_data = [];
    $stmt_user_fetch = $conn->prepare("SELECT email, firstname FROM tbl_users WHERE id = ?");
    $stmt_user_fetch->bind_param("i", $user_id);
    $stmt_user_fetch->execute();
    $result_user_fetch = $stmt_user_fetch->get_result();

    if ($result_user_fetch->num_rows === 0) {
        throw new Exception("User data not found.");
    }
    $user_data = $result_user_fetch->fetch_assoc();
    $stmt_user_fetch->close();

    // 3. FETCH CART ITEMS
    $cart_sql = "SELECT c.product_id, c.quantity, p.price, p.product_name 
                 FROM tbl_cart c
                 JOIN tbl_products p ON c.product_id = p.id 
                 WHERE c.user_id = ? FOR UPDATE";

    $stmt_cart = $conn->prepare($cart_sql);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart_result = $stmt_cart->get_result();

    if ($cart_result->num_rows == 0) {
        throw new Exception("Cart is empty.");
    }

    $order_items_data = [];
    $calculated_total = 0.00;
    while ($item = $cart_result->fetch_assoc()) {
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $item['unit_price'] = $item['price'];
        $calculated_total += $item['subtotal'];
        $order_items_data[] = $item;
    }
    $stmt_cart->close();

    $final_total = $calculated_total + $shipping_cost;

    $order_number = 'ORD-' . time() . '-' . rand(100, 999);
    $payment_status = 'pending';
    $order_status = 'pending';

    // 4. Insert into tbl_orders
    $order_sql = "INSERT INTO tbl_orders (order_number, user_id, total_amount, payment_status, payment_method, shipping_address, billing_address, order_status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($order_sql);
    $stmt_order->bind_param("sidsssss", $order_number, $user_id, $final_total, $payment_status, $payment_method, $shipping_address, $billing_address, $order_status);
    $stmt_order->execute();
    $order_id = $conn->insert_id;
    $stmt_order->close();

    // 5. Insert into tbl_order_items
    $items_sql = "INSERT INTO tbl_order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($items_sql);
    foreach ($order_items_data as $item) {
        $stmt_items->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['unit_price']);
        $stmt_items->execute();
    }
    $stmt_items->close();

    // 6. Clear cart and Commit
    $clear_cart_sql = "DELETE FROM tbl_cart WHERE user_id = ?";
    $stmt_clear = $conn->prepare($clear_cart_sql);
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();
    $stmt_clear->close();
    $conn->commit();

    // 7. Email Logic Integration
    $order_summary = [
        'order_number' => $order_number,
        'final_total' => $final_total,
        'shipping_cost' => $shipping_cost,
        'items' => $order_items_data,
    ];
    $email_body = generateOrderConfirmationBody($order_summary, $user_data['firstname']);
    $email_subject = "Kharido.pk: COD Order Confirmation #{$order_number}";

    sendOrderConfirmationEmail($user_data['email'], $email_subject, $email_body);

    // 8. Success Redirect
    $_SESSION['order_success'] = "Your COD order #$order_number has been placed successfully!";
    header("Location: ../order_confirmation.php?order_id=$order_id");
    exit();
} catch (Exception $e) {
    if ($conn) {
        $conn->rollback();
    }
    $_SESSION['order_error'] = "Order placement failed: " . $e->getMessage();
    error_log("COD Order Error for User $user_id: " . $e->getMessage());
    header("Location: ../checkout.php?error=1");
    exit();
} finally {
    if ($conn) {
        $conn->close();
    }
}
