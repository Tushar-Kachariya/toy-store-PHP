<?php
session_start();
require_once "../backend/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $allowed = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        header("Location: admin-orders.php");
        exit;
    }
}

$sql = "SELECT o.*, u.name AS user_name, u.email AS user_email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();
$orders = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel • Orders</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
body { background:#f2f2f2; }
.status-badge { padding:4px 10px; border-radius:12px; font-size:0.85rem; font-weight:600; color:white; display:inline-block; text-align:center; }
.status-Pending { background:#ffc107; }
.status-Shipped { background:#0d6efd; }
.status-Delivered { background:#28a745; }
.status-Cancelled { background:#dc3545; }
.table thead { background:#2874f0; color:white; }
.table tbody tr:hover { background:#f1f5f9; }
.img-thumb { width:50px; height:50px; object-fit:cover; border-radius:5px; }
</style>
</head>
<body class="p-5">

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Admin Orders Panel</h2>
        <a href="profile.php" class="btn btn-secondary">← Go Back</a>
    </div>

    <?php if(empty($orders)): ?>
        <div class="alert alert-info rounded shadow">No orders found.</div>
    <?php else: ?>
        <div class="table-responsive shadow rounded bg-white">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Items</th>
                        <th>Total (₹)</th>
                        <th>Status</th>
                        <th>Placed On</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($orders as $order): ?>
                    <?php
            
                    $sql2 = "SELECT oi.*, t.name, t.image FROM order_items oi JOIN toys t ON oi.toy_id = t.id WHERE oi.order_id = ?";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param("i", $order['id']);
                    $stmt2->execute();
                    $res2 = $stmt2->get_result();
                    $items = $res2->fetch_all(MYSQLI_ASSOC);
                    ?>
                    <tr>
                        <td>#TS<?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['user_name']) ?></td>
                        <td><?= htmlspecialchars($order['user_email']) ?></td>
                        <td><?= htmlspecialchars($order['address'] ?? '-') ?></td>
                        <td>
                            <ul class="list-unstyled m-0">
                            <?php foreach($items as $item): ?>
                                <li class="flex items-center gap-2">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" class="img-thumb" alt="">
                                    <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        </td>
                        <td class="fw-bold text-success">₹<?= number_format($order['total'], 2) ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" class="form-select form-select-sm status-<?= $order['status'] ?>" onchange="this.form.submit()">
                                    <?php foreach(['Pending','Shipped','Delivered','Cancelled'] as $status): ?>
                                        <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td><?= date("d M Y, h:i A", strtotime($order['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
