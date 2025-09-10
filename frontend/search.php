<?php
require_once "../backend/db.php";

$search = $_GET['query'] ?? '';
$search = "%{$search}%";

$stmt = $conn->prepare("SELECT * FROM toys WHERE name LIKE ? OR category LIKE ?");
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$res = $stmt->get_result();
$toys = $res->fetch_all(MYSQLI_ASSOC);
?>
