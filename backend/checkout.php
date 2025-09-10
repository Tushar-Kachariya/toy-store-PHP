<?php
session_start();
require_once "../backend/db.php";

if (!isset($_SESSION['user']['id'])) {
    die("❌ You must login to checkout.");
}

$user_id = (int)$_SESSION['user']['id'];
$total = 0;

$sql = "SELECT c.quantity, t.price 
        FROM cart c 
        JOIN toys t ON c.toy_id = t.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("🛒 Your cart is empty. <a href='../frontend/index.php'>Back to shop</a>");
}

while ($row = $res->fetch_assoc()) {
    $total += $row['price'] * $row['quantity'];
}

$stmt = $conn->prepare("INSERT INTO orders(user_id,total) VALUES(?,?)");
$stmt->bind_param("id", $user_id, $total);

if ($stmt->execute()) {
    
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    echo "🎉 Order placed successfully!";
    echo "<br><a href='../frontend/index.php'>Back to Shop</a>";
} else {
    echo "❌ Error: " . $conn->error;
}
?>
