<?php
session_start();

$_SESSION = [];

session_destroy();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

foreach ($_COOKIE as $key => $value) {
    setcookie($key, '', time() - 3600, '/');
}

header("Location: index.php");
exit;
?>
