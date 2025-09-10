<?php
session_start();
require_once "../backend/db.php";

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$id = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id) {
    if ($action === "inc") {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
    } elseif ($action === "dec") {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM cart WHERE quantity <= 0 AND id=? AND user_id=?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
    }
}
header("Location: cart.php");
exit;
