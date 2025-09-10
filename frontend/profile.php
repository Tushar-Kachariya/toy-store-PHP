<?php
session_start();
require_once "../backend/db.php"; 

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?= ($user['role'] === 'admin') ? 'Admin Profile' : 'My Profile' ?> • ToyStore</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">

  <nav class="bg-blue-600 p-3 shadow-md">
    <div class="container mx-auto flex justify-between items-center text-white">
      <h1 class="text-2xl font-bold">ToyStore</h1>
      <div>
        <a href="index.php" class="px-3 hover:underline">Home</a>
        <a href="cart.php" class="px-3 hover:underline"><i class="fa fa-shopping-cart"></i> Cart</a>
      </div>
    </div>
  </nav>

  <div class="container mx-auto py-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <div class="bg-white rounded-xl shadow p-5">
        <div class="flex items-center gap-3 border-b pb-4">
          <div class="w-16 h-16 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 text-2xl font-bold">
            <?= strtoupper(substr($user['name'], 0, 1)) ?>
          </div>
          <div>
            <h3 class="text-lg font-semibold"><?= htmlspecialchars($user['name']) ?></h3>
            <p class="text-sm text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
          </div>
        </div>

        <ul class="mt-4 space-y-2">
          <li><a href="profile.php" class="block px-3 py-2 rounded hover:bg-blue-50"><i class="fa fa-user"></i> My Profile</a></li>
          <?php if ($user['role'] === 'admin'): ?>
            <li><a href="admin.php" class="block px-3 py-2 rounded hover:bg-yellow-50 text-yellow-600"><i class="fa fa-tools"></i> Admin Dashboard</a></li>
          <?php else: ?>
            <li><a href="orders.php" class="block px-3 py-2 rounded hover:bg-green-50 text-green-600"><i class="fa fa-box"></i> My Orders</a></li>
          <?php endif; ?>
          <li><a href="../backend/auth.php?logout=1" class="block px-3 py-2 rounded hover:bg-red-50 text-red-600"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </div>

      <div class="col-span-2">
        <div class="bg-white shadow-lg rounded-xl p-6">
          <h2 class="text-2xl font-bold mb-4 border-b pb-2">Profile Information</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center">
              <i class="fa fa-id-card w-8 text-blue-500"></i>
              <span><strong>ID:</strong> <?= $user['id'] ?></span>
            </div>
            <div class="flex items-center">
              <i class="fa fa-user w-8 text-blue-500"></i>
              <span><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></span>
            </div>
            <div class="flex items-center">
              <i class="fa fa-envelope w-8 text-blue-500"></i>
              <span><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></span>
            </div>
            <div class="flex items-center">
              <i class="fa fa-user-shield w-8 text-blue-500"></i>
              <span><strong>Role:</strong> <?= ucfirst($user['role']) ?></span>
            </div>
            <div class="flex items-center">
              <i class="fa fa-calendar w-8 text-blue-500"></i>
              <span><strong>Member Since:</strong> <?= date("d M Y", strtotime($user['created_at'])) ?></span>
            </div>
          </div>

          <div class="mt-6 flex gap-3">
            <?php if ($user['role'] === 'admin'): ?>
              <a href="admin.php" class="btn btn-warning"><i class="fa fa-chart-line"></i> Admin Dashboard</a>
            <?php else: ?>
              <a href="orders.php" class="btn btn-primary"><i class="fa fa-box-open"></i> View Orders</a>
            <?php endif; ?>
            <a href="../backend/auth.php?logout=1" class="btn btn-danger"><i class="fa fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
