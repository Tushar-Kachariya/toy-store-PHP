<?php
session_start();
require_once "../backend/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_POST['user_id'];
$total = $_POST['total'];
$cart_ids = explode(',', $_POST['cart_ids']);
$address = $_POST['address'] ?? 'Not Provided';

$sql = "INSERT INTO orders (user_id, total, address, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ids", $user_id, $total, $address);
$stmt->execute();

$order_id = $conn->insert_id;

foreach ($cart_ids as $cart_id) {
    $sqlItem = "INSERT INTO order_items (order_id, toy_id, quantity, price)
                SELECT ?, toy_id, quantity, price FROM cart WHERE id = ?";
    $stmtItem = $conn->prepare($sqlItem);
    $stmtItem->bind_param("ii", $order_id, $cart_id);
    $stmtItem->execute();
}

$in = str_repeat('?,', count($cart_ids)-1) . '?';
$stmtClear = $conn->prepare("DELETE FROM cart WHERE id IN ($in)");
$stmtClear->bind_param(str_repeat('i', count($cart_ids)), ...$cart_ids);
$stmtClear->execute();

header("Location: order-success.php?order_id=$order_id");
exit;
?>
    