<?php 
session_start();
require_once "../backend/db.php"; 

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$orders = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders • ToyStore</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
body { background:#f8f9fa; }
.table thead th { background:#2874f0; color:white; }
.status-badge { padding:4px 10px; border-radius:12px; font-size:0.8rem; font-weight:600; color:white; display:inline-block; text-align:center; }
.status-Pending { background:#ffc107; }
.status-Shipped { background:#0d6efd; }
.status-Delivered { background:#28a745; }
.status-Cancelled { background:#dc3545; }
.btn-home { margin-bottom:20px; }
.product-img { width:60px; height:60px; object-fit:cover; border-radius:6px; }
</style>
</head>
<body>

<div class="container py-5">
    <a href="index.php" class="btn btn-primary btn-home">← Go to Home</a>

    <h2 class="fw-bold mb-4 text-gray-800 text-2xl">My Orders</h2>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info rounded shadow">
            You have not placed any orders yet. <a href="index.php" class="text-primary">Shop now</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="mb-4">
                <h5 class="mb-2">Order #TS<?= $order['id'] ?> - <?= date("d M Y, h:i A", strtotime($order['created_at'])) ?></h5>

                <table class="table table-striped shadow-sm bg-white rounded">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Name</th>
                            <th>Qty × Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql2 = "SELECT oi.*, t.name, t.image FROM order_items oi JOIN toys t ON oi.toy_id = t.id WHERE oi.order_id = ?";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param("i", $order['id']);
                    $stmt2->execute();
                    $res2 = $stmt2->get_result();
                    $items = $res2->fetch_all(MYSQLI_ASSOC);

                    foreach ($items as $item):
                        $totalPrice = $item['quantity'] * $item['price'];
                    ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($item['image']) ?>" class="product-img" alt=""></td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $item['quantity'] ?> × ₹<?= $item['price'] ?></td>
                            <td class="fw-bold text-success">₹<?= $totalPrice ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Order Total</td>
                            <td class="fw-bold">
                                ₹<?= $order['total'] ?> 
                                <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
