<?php
session_start(); // CRITICAL: Session must be started for AJAX endpoint
include '../config.php';
require_once 'email_sender.php'; // Assuming these files exist in the same directory
require_once 'email_template.php';

header('Content-Type: application/json'); // Ensure JSON response header is set

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0 || $_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Unauthorized or invalid request.']);
    exit();
}

// 1. COLLECT POST DATA
$total_amount     = floatval($_POST['total_amount'] ?? 0);
$shipping_cost    = floatval($_POST['shipping_cost'] ?? 0);
$payment_method   = 'paypal';
$paypal_order_id  = trim($_POST['paypal_order_id'] ?? '');

// Collect INDIVIDUAL form fields for address construction
$address1         = trim($_POST['address1'] ?? '');
$address2         = trim($_POST['address2'] ?? '');
$city             = trim($_POST['city'] ?? '');
$state            = trim($_POST['state'] ?? '');
$postcode         = trim($_POST['postcode'] ?? '');
$country          = trim($_POST['country'] ?? '');
$order_notes      = trim($_POST['order_notes'] ?? '');

// CONSTRUCT THE FINAL ADDRESS STRING
$shipping_address = implode(', ', array_filter([$address1, $address2, $city, $state, $postcode, $country]));
$billing_address  = $shipping_address;

if (empty($paypal_order_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing PayPal Order ID.']);
    exit();
}

$conn = null;
try {
    // 💡 FIX: Establish DB Connection and Transaction
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("DB connection failed.");
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

    // 3. Set Statuses for PayPal Order
    // NOTE: In a real app, external API capture/verification would happen here.
    $payment_status = 'paid';
    $order_status = 'processing';

    // 4. FETCH CART ITEMS
    $cart_sql = "SELECT c.product_id, c.quantity, p.price, p.product_name 
                 FROM tbl_cart c JOIN tbl_products p ON c.product_id = p.id
                 WHERE c.user_id = ? FOR UPDATE";

    $stmt_cart = $conn->prepare($cart_sql);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart_result = $stmt_cart->get_result();

    if ($cart_result->num_rows == 0) {
        throw new Exception("Cart is empty.");
    }

    $order_items_data = [];
    $calculated_subtotal = 0.00;
    while ($item = $cart_result->fetch_assoc()) {
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $calculated_subtotal += $item['subtotal'];
        $item['unit_price'] = $item['price'];
        $order_items_data[] = $item;
    }
    $stmt_cart->close();

    $final_total = $calculated_subtotal + $shipping_cost;

    $order_number = 'ORD-' . time() . '-' . rand(100, 999);

    // 5. Insert into tbl_orders
    $order_sql = "INSERT INTO tbl_orders (order_number, user_id, total_amount, payment_status, payment_method, shipping_address, billing_address, order_status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($order_sql);
    // Note the final bind param sequence: sidsssss
    $stmt_order->bind_param("sidsssss", $order_number, $user_id, $final_total, $payment_status, $payment_method, $shipping_address, $billing_address, $order_status);
    $stmt_order->execute();
    $order_id = $conn->insert_id;
    $stmt_order->close();

    // 6. Insert into tbl_order_items (Logic remains the same)
    $items_sql = "INSERT INTO tbl_order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($items_sql);
    foreach ($order_items_data as $item) {
        $stmt_items->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['unit_price']);
        $stmt_items->execute();
    }
    $stmt_items->close();

    // 7. Clear the user's cart
    $clear_cart_sql = "DELETE FROM tbl_cart WHERE user_id = ?";
    $stmt_clear = $conn->prepare($clear_cart_sql);
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();
    $stmt_clear->close();

    $conn->commit(); // DB commit successful

    // 8. EMAIL LOGIC INTEGRATION
    $order_summary = [
        'order_number' => $order_number,
        'final_total' => $final_total,
        'shipping_cost' => $shipping_cost,
        'items' => $order_items_data,
    ];

    $email_body = generateOrderConfirmationBody($order_summary, $user_data['firstname']);
    $email_subject = "Kharido.pk: PayPal Order Confirmation #{$order_number}";

    // Execute the modular email function (No need to check return value, logging handles failure)
    sendOrderConfirmationEmail($user_data['email'], $email_subject, $email_body);

    // 9. Send success response
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} catch (Exception $e) {
    if ($conn) {
        $conn->rollback();
    }
    // 💡 This error message will be returned to the AJAX 'error' handler
    error_log("PayPal Capture Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Order failed during server processing.']);
} finally {
    if ($conn) {
        $conn->close();
    }
}
