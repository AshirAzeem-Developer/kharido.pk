<?php
// sign-in.php
include 'config.php'; // includes session_start()

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email'] ?? '');
  $password_raw = $_POST['password'] ?? '';

  if (empty($email) || empty($password_raw)) {
    $message = '<div class="alert alert-danger">Please enter both email and password.</div>';
  } else {
    $stmt = $conn->prepare("SELECT id, firstname, password, designation FROM tbl_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
      $user = $result->fetch_assoc();
      $hashed_password = $user['password'];

      if (password_verify($password_raw, $hashed_password)) {
        // ✅ Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['firstname'];
        $_SESSION['designation'] = (trim($user['designation']) === "Administrator") ? "admin" : "user";
        $_SESSION['logged_in'] = TRUE;

        // ✅ Optional: Remember Me (basic version)
        if (isset($_POST['remember_me'])) {
          setcookie("remember_email", $email, time() + (86400 * 30), "/"); // 30 days
        }

        // ✅ Redirect based on role
        if ($_SESSION['designation'] === 'admin') {
          header("Location: admin/dashboard.php");
        } else {
          header("Location: index.php");
        }
        exit;
      } else {
        $message = '<div class="alert alert-danger">Invalid email or password.</div>';
      }
    } else {
      $message = '<div class="alert alert-danger">Invalid email or password.</div>';
    }

    $stmt->close();
  }
}

$conn->close();
