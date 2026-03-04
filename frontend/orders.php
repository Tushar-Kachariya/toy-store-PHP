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
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .status-badge { @apply px-3 py-1 rounded-full text-xs font-semibold text-white; }
    .status-Pending { background:#fbbf24; }   /* amber-500 */
    .status-Shipped { background:#3b82f6; }   /* blue-500 */
    .status-Delivered { background:#22c55e; } /* green-500 */
    .status-Cancelled { background:#ef4444; } /* red-500 */
</style>
</head>
<body class="bg-gray-100">

<div class="max-w-5xl mx-auto px-4 py-8">
    <a href="index.php" class="inline-block mb-6 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
        ← Go to Home
    </a>

    <h2 class="text-3xl font-bold text-gray-800 mb-6">My Orders</h2>

    <?php if (empty($orders)): ?>
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg shadow">
            You have not placed any orders yet. 
            <a href="index.php" class="font-semibold underline">Shop now</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="mb-8 bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h5 class="font-semibold text-gray-700">
                        Order #TS<?= $order['id'] ?> 
                        <span class="text-sm text-gray-500 ml-2"><?= date("d M Y, h:i A", strtotime($order['created_at'])) ?></span>
                    </h5>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="px-4 py-2">Product</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Qty × Price</th>
                                <th class="px-4 py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
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
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" class="w-14 h-14 rounded-md object-cover border" alt="">
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-700"><?= htmlspecialchars($item['name']) ?></td>
                                <td class="px-4 py-2"><?= $item['quantity'] ?> × ₹<?= $item['price'] ?></td>
                                <td class="px-4 py-2 font-semibold text-green-600">₹<?= $totalPrice ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-4 py-2 text-right font-semibold text-gray-700">Order Total</td>
                                <td class="px-4 py-2 font-bold">
                                    ₹<?= $order['total'] ?> 
                                    <span class="status-badge status-<?= htmlspecialchars($order['status']) ?> ml-2">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
