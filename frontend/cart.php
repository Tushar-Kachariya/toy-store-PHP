<?php
session_start();
require_once "../backend/db.php";
require_once "../backend/auth.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user']['id'];

$stmt = $conn->prepare("
    SELECT c.id as cart_id, c.quantity, t.*
    FROM cart c
    JOIN toys t ON c.toy_id = t.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>My Cart • ToyStore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
      <a class="navbar-brand flex items-center gap-2" href="index.php">
        <img src="https://cdn-icons-png.flaticon.com/512/3081/3081648.png" width="35" alt="logo"/>
        <span class="fw-bold text-primary">ToyStore</span>
      </a>
      <form class="d-flex ms-auto mt-3">
        <input class="form-control me-2 rounded-pill" type="search" placeholder="Search for toys...">
        <button class="btn btn-outline-primary rounded-pill">Search</button>
      </form>
      <a href="cart.php" class="ms-4 btn btn-primary rounded-pill mt-3 ">
        🛒 Cart <span class="badge bg-light text-dark" id="cart-count"><?= count($cartItems) ?></span>
      </a>
    </div>
  </nav>

<div class="container py-5">
  <div class="row">
    <div class="col-lg-8">
      <div class="bg-white p-4 shadow rounded-2xl mb-4">
        <h4 class="mb-3 fw-bold">My Cart (<?= count($cartItems) ?> Items)</h4>

        <?php if (!$cartItems): ?>
          <p class="text-muted">Your cart is empty. <a href="index.php" class="text-primary">Shop toys now</a></p>
        <?php else: ?>
          <?php foreach ($cartItems as $item): ?>
            <div class="d-flex align-items-center border-bottom py-3">
              <img src="<?= htmlspecialchars($item['image']) ?>" class="w-20 h-20 rounded me-3" alt="toy"/>
              <div class="flex-grow-1">
                <h6 class="fw-bold"><?= htmlspecialchars($item['name']) ?></h6>
                <p class="text-muted small mb-1"><?= htmlspecialchars($item['brand'] ?? 'Generic') ?> • <?= htmlspecialchars($item['category']) ?></p>
                <span class="fw-bold text-success">₹<?= number_format($item['price'], 2) ?></span>
                <div class="mt-2 flex items-center gap-2">
                  <a href="update-cart.php?id=<?= $item['cart_id'] ?>&action=dec" class="btn btn-sm btn-outline-secondary rounded-pill">-</a>
                  <span class="px-2"><?= $item['quantity'] ?></span>
                  <a href="update-cart.php?id=<?= $item['cart_id'] ?>&action=inc" class="btn btn-sm btn-outline-secondary rounded-pill">+</a>
                  <a href="remove-from-cart.php?id=<?= $item['cart_id'] ?>" class="btn btn-sm btn-danger ms-3 rounded-pill">Remove</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($cartItems): ?>
    <div class="col-lg-4">
      <div class="bg-white p-4 shadow rounded-2xl">
        <h5 class="fw-bold mb-3">Price Details</h5>
        <ul class="list-group list-group-flush mb-3">
          <li class="list-group-item d-flex justify-content-between">Price (<?= count($cartItems) ?> items) 
            <span>₹<?= number_format($total, 2) ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between">Shipping 
            <span class="text-success"><?= $total > 1000 ? 'FREE' : '₹50' ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between fw-bold">Total Amount 
            <span>₹<?= number_format($total + ($total > 1000 ? 0 : 50), 2) ?></span>
          </li>
        </ul>

        <form method="POST" action="checkout.php">
          <input type="hidden" name="user_id" value="<?= $user_id ?>">
          <input type="hidden" name="total" value="<?= $total + ($total > 1000 ? 0 : 50) ?>">
          <input type="hidden" name="cart_ids" value="<?= implode(',', array_column($cartItems, 'cart_id')) ?>">
          <input type="text" name="address" class="form-control mb-2" placeholder="Enter delivery address" required>
          <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold">Place Order</button>
        </form>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
