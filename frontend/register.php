<?php session_start(); 
 if (isset($_SESSION['errors'])): ?>
  <div class="toast-message error">
    <?= htmlspecialchars(implode(" • ", $_SESSION['errors'])) ?>
  </div>
  <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
  <div class="toast-message success">
    <?= htmlspecialchars($_SESSION['success']) ?>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>



<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Register • ToyStore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
.toast-message {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #2874f0; 
  color: #fff;
  padding: 12px 20px;
  border-radius: 6px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  font-size: 14px;
  z-index: 9999;
  opacity: 0;
  transform: translateY(-20px);
  animation: slideDown 0.4s forwards, fadeOut 0.5s ease 3s forwards;
}
.toast-message.error {
  background: #d32f2f; 
}
.toast-message.success {
  background: #388e3c; 
}
@keyframes slideDown {
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeOut {
  to { opacity: 0; transform: translateY(-20px); }
}
</style>

</head>
<body class="bg-gray-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="https://upload.wikimedia.org/wikipedia/commons/0/08/Flipkart_logo.png" alt="logo" class="me-2" style="height:35px;">
      <span class="fw-bold">ToyStore</span>
    </a>
    <form class="d-flex mx-auto w-50" role="search">
      <input class="form-control" id="search" type="search" placeholder="Search for toys, brands and more">
    </form>
    <div>
      <a href="login.php" class="btn btn-light me-2">Login</a>
      <a href="register.php" class="btn btn-warning">Register</a>
    </div>
  </div>
</nav>

<div class="flex items-center justify-center min-h-screen">
  <div class="bg-white shadow-lg rounded-2xl grid md:grid-cols-2 w-[900px] overflow-hidden">
    
    <div class="bg-green-600 p-8 flex flex-col justify-center text-white">
      <h2 class="text-3xl font-bold mb-4">Join Us 🎁</h2>
      <p class="mb-6">Create your account to start shopping, track orders, and more.</p>
      <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
           alt="register" 
           class="w-40 mx-auto animate-bounce">
    </div>

    <div class="p-8">
      <h3 class="text-2xl font-bold mb-6 text-center">Register</h3>
      <form action="../backend/auth.php" method="POST" class="space-y-4">
        <div>
          <input type="text" name="name" class="form-control py-2" placeholder="Full Name" required>
        </div>
        <div>
          <input type="email" name="email" class="form-control py-2" placeholder="Email" required>
        </div>
        <div>
          <input type="password" name="password" class="form-control py-2" placeholder="Password" required>
        </div>
        <div>
          <input type="password" name="confirm_password" class="form-control py-2" placeholder="Confirm Password" required>
        </div>
        <button type="submit" name="register" class="btn btn-success w-100 py-2">Register</button>
        <p class="text-center text-sm mt-3">
          Already have an account? 
          <a href="login.php" class="text-blue-600 hover:underline">Login</a>
        </p>
      </form>
    </div>

  </div>
</div>

<footer class="bg-gray-900 text-gray-300 mt-6">
  <div class="container mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 py-10 px-6">
    <div>
      <h4 class="font-semibold text-white mb-3">ABOUT</h4>
      <ul class="space-y-1">
        <li><a href="#" class="hover:text-white">Contact Us</a></li>
        <li><a href="#" class="hover:text-white">About Us</a></li>
        <li><a href="#" class="hover:text-white">Careers</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-semibold text-white mb-3">HELP</h4>
      <ul class="space-y-1">
        <li><a href="#" class="hover:text-white">Payments</a></li>
        <li><a href="#" class="hover:text-white">Shipping</a></li>
        <li><a href="#" class="hover:text-white">Returns</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-semibold text-white mb-3">POLICY</h4>
      <ul class="space-y-1">
        <li><a href="#" class="hover:text-white">Terms of Use</a></li>
        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
        <li><a href="#" class="hover:text-white">Security</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-semibold text-white mb-3">SOCIAL</h4>
      <ul class="space-y-1">
        <li><a href="#" class="hover:text-white">Facebook</a></li>
        <li><a href="#" class="hover:text-white">Twitter</a></li>
        <li><a href="#" class="hover:text-white">YouTube</a></li>
      </ul>
    </div>
  </div>
  <div class="bg-gray-800 text-center py-3 text-sm text-gray-400">
    © 2025 ToyStore — Inspired by Flipkart Clone
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
