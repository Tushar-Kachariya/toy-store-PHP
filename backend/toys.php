<?php
require_once "db.php";

$sql = "SELECT * FROM toys ORDER BY id DESC";
$res = mysqli_query($conn, $sql);

$toys = [];
while ($row = mysqli_fetch_assoc($res)) {
    $toys[] = $row;
}
echo json_encode($toys);
?>
