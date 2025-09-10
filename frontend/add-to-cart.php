<?php
session_start();
require_once "../backend/db.php";


if (!isset($_SESSION['user']['id'])) {
    die("❌ You must login first.");
}

if (!isset($_GET['toy_id']) || !is_numeric($_GET['toy_id'])) {
    die("❌ Invalid toy ID.");
}

$user_id = (int)$_SESSION['user']['id'];
$toy_id  = (int)$_GET['toy_id'];


$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND toy_id=?");
$stmt->bind_param("ii", $user_id, $toy_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    $newQty = $row['quantity'] + 1;
    $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
    $stmt->bind_param("ii", $newQty, $row['id']);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("INSERT INTO cart(user_id, toy_id, quantity) VALUES(?,?,1)");
    $stmt->bind_param("ii", $user_id, $toy_id);
    $stmt->execute();
}

header("Location: cart.php");
exit;
?>
