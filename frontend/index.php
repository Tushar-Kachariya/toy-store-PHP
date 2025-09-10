<?php
session_start();
require_once "../backend/db.php"; 

$user_id = null;
$cartCount = 0;
if (isset($_SESSION['user']['id'])) {
    $user_id = (int)$_SESSION['user']['id'];

    $stmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $cartCount = $row['total'] ?? 0;
}

$search = trim($_GET['q'] ?? ''); 
if ($search !== "") {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $res = mysqli_query($conn, "SELECT * FROM toys 
            WHERE name LIKE '%$searchEscaped%' 
               OR brand LIKE '%$searchEscaped%' 
               OR category LIKE '%$searchEscaped%' 
            ORDER BY id DESC");
} else {
    $res = mysqli_query($conn, "SELECT * FROM toys ORDER BY id DESC");
}

$toys = [];
while ($row = mysqli_fetch_assoc($res)) {
    $toys[] = $row;
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ToyStore – Flipkart Style</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { background:#f8f9fa; }
    .navbar-brand img { height:35px; }
    .product-img { height:180px; object-fit:cover; }
    .card:hover { transform: translateY(-5px); transition:0.3s; box-shadow:0 6px 15px rgba(0,0,0,.15); }
    .category-card { transition:0.3s; }
    .category-card:hover { transform:scale(1.05); }
    .floating-cart { position:fixed; bottom:20px; right:20px; z-index:1050; }
    .product-img {
  width: 80%;       
  height: auto;      
  object-fit: contain; 
}
.product-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 12px;
  overflow: hidden;
}
.product-card:hover {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 12px 25px rgba(0,0,0,0.18);
}

.product-img-wrapper {
  height: 220px;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
}
.product-img {
  max-height: 100%;
  max-width: 100%;
  object-fit: contain; 
  transition: transform 0.4s ease;
}
.product-card:hover .product-img {
  transform: scale(1.1);
}

.overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  opacity: 0;
  display: flex;
  gap: 0.5rem;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  transition: opacity 0.4s ease;
}
.product-card:hover .overlay {
  opacity: 1;
}

.text-truncate-multiline {
  display: -webkit-box;
  -webkit-line-clamp: 2; 
  -webkit-box-orient: vertical;
  overflow: hidden;
}


  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
    <img src="ecom.png" alt="logo" 
     class="img-fluid h-24 w-auto rounded-lg shadow-lg hover:scale-110 transition duration-300 ease-in-out" 
     style="max-height: 100px;">



   <form class="d-flex mx-auto w-50" role="search" method="GET" action="index.php" >
  <input 
    class="form-control" 
    id="search" 
    name="q"   
    type="search" 
    placeholder="Search for toys, brands and more" 
    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
    required
  >
  <button class="btn btn-warning ms-2" type="submit">Search</button>
</form>



    <div>
      <?php if(isset($_SESSION['user'])): ?>
        <a href="../backend/auth.php?logout=1" class="btn btn-light me-2">Logout</a>
        <a href="profile.php" class="btn btn-outline-light position-relative">Profile</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-light me-2">Login</a>
        <a href="register.php" class="btn btn-warning">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>


<div id="hero" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active"><img src="https://www.shutterstock.com/image-vector/online-toy-store-buying-different-260nw-2372960837.jpg" class="d-block w-100" style="max-height:400px;object-fit:cover;"></div>
    <div class="carousel-item"><img src="https://images.unsplash.com/photo-1596464716127-f2a82984de30" class="d-block w-100" style="max-height:400px;object-fit:cover;"></div>
    <div class="carousel-item"><img src="https://as2.ftcdn.net/jpg/03/23/24/81/1000_F_323248194_yeWGQAoqdHf85UMFgU4kkAGKdmHj4Wys.webp" class="d-block w-100" style="max-height:400px;object-fit:cover;"></div>
  </div>
</div>

<section class="container my-4">
  <div class="row g-3">
    <div class="col-md-4">
      <div class="bg-gradient-to-r from-pink-500 to-yellow-400 text-white p-4 rounded-xl shadow-lg">
        <h5 class="fw-bold">New Arrivals</h5>
        <p>Fresh toys added daily</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white p-4 rounded-xl shadow-lg">
        <h5 class="fw-bold">50% OFF</h5>
        <p>On selected dolls & puzzles</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="bg-gradient-to-r from-green-400 to-blue-500 text-white p-4 rounded-xl shadow-lg">
        <h5 class="fw-bold">Bestsellers</h5>
        <p>Shop the most loved toys</p>
      </div>
    </div>
  </div>
</section>

<section class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5>⚡ Flash Sale</h5>
    <div id="countdown" class="fw-bold text-danger"></div>
  </div>
  <div class="row g-3" id="flashProducts"></div>
</section>

<section class="container my-4">
  <h5 class="mb-3">Shop by Category</h5>
  <div class="row g-3 text-center">
    <div class="col-6 col-md-3"><div class="card category-card p-3"><img src="https://cdn-icons-png.flaticon.com/512/3076/3076094.png" class="w-50 mx-auto"><h6 class="mt-2">Cars</h6></div></div>
    <div class="col-6 col-md-3"><div class="card category-card p-3"><img src="https://cdn-icons-png.flaticon.com/512/615/615075.png" class="w-50 mx-auto"><h6 class="mt-2">Dolls</h6></div></div>
    <div class="col-6 col-md-3"><div class="card category-card p-3"><img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="w-50 mx-auto"><h6 class="mt-2">Action Figures</h6></div></div>
    <div class="col-6 col-md-3"><div class="card category-card p-3"><img src="https://cdn-icons-png.flaticon.com/512/4151/4151720.png" class="w-50 mx-auto"><h6 class="mt-2">Puzzles</h6></div></div>
  </div>
</section>

<main class="container my-4">
  <h5 class="mb-3 fw-bold text-primary">🔥 Top Deals on Toys</h5>
  <div class="row g-4">
    <?php if(count($toys) > 0): ?>
      <?php foreach($toys as $t): ?>
        <div class="col-md-3 col-sm-6">
          <div class="card h-100 shadow-sm border-0 rounded-3 product-card">
            <div class="position-relative overflow-hidden product-img-wrapper">
              <img src="<?= htmlspecialchars($t['image']) ?>" 
                   class="card-img-top product-img" 
                   alt="<?= htmlspecialchars($t['name']) ?>">
              <div class="overlay d-flex flex-column justify-content-center align-items-center text-center">
                <a href="add-to-cart.php?toy_id=<?= $t['id'] ?>" class="btn btn-sm btn-light shadow mb-2">🛒 Add to Cart</a>
                
              </div>
            </div>
            <div class="card-body d-flex flex-column">
              <h6 class="fw-bold text-dark text-truncate"><?= htmlspecialchars($t['name']) ?></h6>
              <p class="text-muted small mb-1">📦 Category: <?= htmlspecialchars($t['category']) ?></p>
              <p class="small text-secondary flex-grow-1 text-truncate-multiline"><?= substr($t['description'],0,80) ?>...</p>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="fw-bold text-success fs-6">₹<?= number_format($t['price'],2) ?></span>
                <span class="badge bg-warning text-dark">⭐ <?= $t['rating'] ?? "4.5" ?></span>
              </div>
              <p class="small mt-1 text-muted">Stock: <?= $t['stock'] ?? "Available" ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12 text-center text-danger fw-bold">❌ No toys found</div>
    <?php endif; ?>
  </div>
</main>




<section class="bg-white py-5 shadow-sm">
  <div class="container">
    <h5 class="text-center mb-4">What Our Customers Say ❤️</h5>
    <div id="reviews" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner text-center">
        <div class="carousel-item active"><blockquote>"Amazing toy store! My kids love it!"<br><span class="text-muted">– Rahul</span></blockquote></div>
        <div class="carousel-item"><blockquote>"Fast delivery & great discounts."<br><span class="text-muted">– Tushar</span></blockquote></div>
        <div class="carousel-item"><blockquote>"Superb quality toys at best price."<br><span class="text-muted">– Martin</span></blockquote></div>
      </div>
    </div>
  </div>
</section>

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
      © 2025 ToyStore Admin — Inspired by Flipkart Clone
    </div>
  </footer>

<?php if ($cartCount > 0): ?>
  <button class="btn btn-danger rounded-circle floating-cart shadow" onclick="location.href='cart.php'">
    🛒 <span id="cartCountMobile" class="badge bg-light text-dark"><?= $cartCount ?></span>
  </button>
<?php endif; ?>


<script>
fetch("../backend/toys.php")
  .then(r => r.json())
  .then(data => {
    let html = "";
    data.forEach(t => {
      const description = t.description.length > 200
        ? `${t.description.substring(0, 200)}... <a href="toy.php?id=${t.id}">Read More</a>`
        : t.description;

      html += `
      <div class="col-md-3">
        <div class="card h-100 shadow-sm">
          <img src="${t.image}" class="card-img-top product-img" alt="${t.name}">
          <div class="card-body d-flex flex-column">
            <h6 class="fw-bold">${t.name}</h6>
            <p class="text-muted small mb-1">Category: ${t.category}</p>
            <p class="small text-secondary flex-grow-1">${t.description.substring(0,100)}...</p>

            <div class="d-flex justify-content-between align-items-center">
              <span class="fw-bold text-success">₹${t.price}</span>
              <span class="badge bg-warning text-dark">⭐ ${t.rating ?? "4.5"}</span>
            </div>
            <p class="small mt-1 text-muted">Stock: ${t.stock ?? "Available"}</p>
            <a href="add-to-cart.php?toy_id=${t.id}" class="btn btn-sm btn-primary mt-2">Add to Cart</a>
          </div>
        </div>
      </div>`;
    });
    document.getElementById("products").innerHTML = html;
  });


function startCountdown(){
  let end = new Date().getTime()+3600*1000; // 1 hr
  setInterval(()=>{
    let now=new Date().getTime(), dist=end-now;
    if(dist<=0){ 
      document.getElementById("countdown").textContent="Sale Ended"; 
      return; 
    }
    let m=Math.floor((dist%(1000*60*60))/(1000*60));
    let s=Math.floor((dist%(1000*60))/1000);
    document.getElementById("countdown").textContent=`Ends in ${m}m ${s}s`;
  },1000);
}
startCountdown();

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
