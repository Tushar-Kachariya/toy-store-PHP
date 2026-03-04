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
.toast-message.error { background: #d32f2f; }
.toast-message.success { background: #388e3c; }
@keyframes slideDown { to { opacity: 1; transform: translateY(0); } }
@keyframes fadeOut { to { opacity: 0; transform: translateY(-20px); } }
</style>
</head>
<body class="bg-gray-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
  <div class="container">
    <img src="logo.png" alt="ToyStore Logo"
     class="img-fluid"
     style="height: 60px; width: 90px; object-fit: contain;">

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
    
    <!-- Left Section -->
    <div class="bg-green-600 p-8 flex flex-col justify-center text-white">
      <h2 class="text-3xl font-bold mb-4">Join Us 🎁</h2>
      <p class="mb-6">Create your account to start shopping, track orders, and more.</p>
      <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
           alt="register" 
           class="w-40 mx-auto animate-bounce">
    </div>

    <!-- Right Section -->
    <div class="p-8">
      <h3 class="text-2xl font-bold mb-6 text-center">Register</h3>
      <form id="registerForm" action="../backend/auth.php" method="POST" class="space-y-4">
        <div>
          <input type="text" name="name" id="name" class="form-control py-2" placeholder="Full Name">
          <small class="text-danger" id="nameError"></small>
        </div>
        <div>
          <input type="email" name="email" id="email" class="form-control py-2" placeholder="Email">
          <small class="text-danger" id="emailError"></small>
        </div>
        <div>
          <input type="password" name="password" id="password" class="form-control py-2" placeholder="Password">
          <small class="text-danger" id="passwordError"></small>
        </div>
        <div>
          <input type="password" name="confirm_password" id="confirm_password" class="form-control py-2" placeholder="Confirm Password">
          <small class="text-danger" id="confirmPasswordError"></small>
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

<script>
// Client-side validation
document.getElementById("registerForm").addEventListener("submit", function(e) {
  let valid = true;

  const name = document.getElementById("name");
  const email = document.getElementById("email");
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirm_password");

  const nameError = document.getElementById("nameError");
  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");
  const confirmPasswordError = document.getElementById("confirmPasswordError");

  // Reset messages
  nameError.textContent = "";
  emailError.textContent = "";
  passwordError.textContent = "";
  confirmPasswordError.textContent = "";

  // Validation
  if (name.value.trim() === "") {
    nameError.textContent = "Full name is required";
    valid = false;
  } else if (name.value.length < 3) {
    nameError.textContent = "Name must be at least 3 characters";
    valid = false;
  }

  if (email.value.trim() === "") {
    emailError.textContent = "Email is required";
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    emailError.textContent = "Invalid email format";
    valid = false;
  }

  if (password.value.trim() === "") {
    passwordError.textContent = "Password is required";
    valid = false;
  } else if (password.value.length < 6) {
    passwordError.textContent = "Password must be at least 6 characters";
    valid = false;
  }

  if (confirmPassword.value.trim() === "") {
    confirmPasswordError.textContent = "Please confirm your password";
    valid = false;
  } else if (confirmPassword.value !== password.value) {
    confirmPasswordError.textContent = "Passwords do not match";
    valid = false;
  }

  if (!valid) e.preventDefault();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
