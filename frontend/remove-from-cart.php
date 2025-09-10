<?php
session_start();
require_once "../backend/db.php";

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$id = intval($_GET['id'] ?? 0);

if ($id) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}

header("Location: cart.php");
exit;
