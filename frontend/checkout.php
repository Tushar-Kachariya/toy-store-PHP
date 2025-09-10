<?php
session_start();
require "../backend/db.php"; 
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user']['id'];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$stmt = $conn->prepare("
    SELECT c.id as cart_id, c.quantity, t.*
    FROM cart c
    JOIN toys t ON c.toy_id = t.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (!$cartItems) {
    header("Location: cart.php");
    exit;
}

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$address = $_POST['address'] ?? 'Not Provided';

try {
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, address, status, created_at) VALUES (?, ?, ?, 'Placed', NOW())");
    $stmt->bind_param("ids", $user_id, $total, $address);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, toy_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmtItem->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmtItem->execute();
    }

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

} catch (Exception $e) {
    die("Order failed: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Checkout • ToyStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f1f3f6; }
    .success-card { max-width: 600px; margin:auto; background:#fff; border-radius:12px; padding:30px; box-shadow:0 4px 12px rgba(0,0,0,0.1); text-align:center; }
    .success-icon { font-size:70px; color:#28a745; }
    .order-summary { text-align:left; background:#f9f9f9; padding:15px; border-radius:8px; margin-top:20px; }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="success-card">
      <div class="success-icon mb-3">✔️</div>
      <h3 class="fw-bold text-success">Order Placed Successfully!</h3>
      <p class="text-muted">Thank you for shopping with <b>ToyStore</b>.</p>

      <div class="order-summary">
        <h6 class="fw-bold">Order Summary</h6>
        <p><b>Order ID:</b> #TS<?= htmlspecialchars($order_id) ?></p>
        <p><b>Total:</b> ₹<?= number_format($total, 2) ?></p>
        <p><b>Address:</b> <?= htmlspecialchars($address) ?></p>
        <p><b>Status:</b> Placed</p>
        <p><b>Estimated Delivery:</b> 3 - 5 business days</p>
      </div>

      <div class="d-flex justify-content-center gap-3 mt-4">
        <a href="index.php" class="btn btn-primary px-4">Continue Shopping</a>
        <a href="orders.php" class="btn btn-outline-secondary px-4">View Orders</a>
      </div>
    </div>
  </div>
</body>
</html>
