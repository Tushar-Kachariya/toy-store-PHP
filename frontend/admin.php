<?php
session_start();
require_once "../backend/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("❌ Access denied. Admins only.");
}

/* ---------- File upload config ---------- */
$uploadDir = __DIR__ . "/uploads";
$publicBase = "uploads";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function handleImageUpload($fieldName, $uploadDir, $publicBase) {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $allowed = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp'
    ];
    $ext = array_search($mime, $allowed, true);
    if ($ext === false) {
        return null;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return null;
    }
    $basename = bin2hex(random_bytes(8));
    $targetName = $basename . "." . $ext;
    $targetPath = rtrim($uploadDir, "/") . "/" . $targetName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return null;
    }
    @chmod($targetPath, 0644);
    return rtrim($publicBase, "/") . "/" . $targetName;
}

/* ---------- Fetch toy for edit ---------- */
$editToy = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM toys WHERE id=$id");
    $editToy = mysqli_fetch_assoc($result) ?: null;
}

/* ---------- Update ---------- */
if (isset($_POST['update'])) {
    $id       = (int) $_POST['id'];
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $brand    = mysqli_real_escape_string($conn, $_POST['brand']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price    = (float) $_POST['price'];
    $desc     = mysqli_real_escape_string($conn, $_POST['description']);

    $newImagePath = handleImageUpload('image', $uploadDir, $publicBase);
    $imageSql = $newImagePath ? ", image='" . mysqli_real_escape_string($conn, $newImagePath) . "'" : "";

    mysqli_query($conn, "UPDATE toys SET 
                name='$name', brand='$brand', category='$category', 
                price=$price, description='$desc' $imageSql
            WHERE id=$id");
    header("Location: admin.php");
    exit;
}

/* ---------- Insert ---------- */
if (isset($_POST['add'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $brand    = mysqli_real_escape_string($conn, $_POST['brand']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price    = (float) $_POST['price'];
    $desc     = mysqli_real_escape_string($conn, $_POST['description']);

    $imagePath = handleImageUpload('image', $uploadDir, $publicBase) ?? '';

    mysqli_query($conn, "INSERT INTO toys (name, brand, category, price, image, description) 
            VALUES ('$name', '$brand', '$category', $price, '" . mysqli_real_escape_string($conn, $imagePath) . "', '$desc')");
    header("Location: admin.php");
    exit;
}

/* ---------- Delete ---------- */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($conn, "DELETE FROM toys WHERE id=$id");
    header("Location: admin.php");
    exit;
}

/* ---------- Stats ---------- */
$totalToys   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM toys"))['cnt'] ?? 0;
$totalBrands = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT brand) AS cnt FROM toys"))['cnt'] ?? 0;
$totalCats   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT category) AS cnt FROM toys"))['cnt'] ?? 0;

/* ---------- Search ---------- */
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

/* ---------- Predefined Categories ---------- */
$categories = [
    "Soft Toys",
    "Action Figures",
    "Educational",
    "Puzzles",
    "Board Games",
    "Dolls",
    "Vehicles",
    "Outdoor",
    "Electronics"
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ToyStore Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { background: #f9fafb; font-family: 'Inter', sans-serif; }
    .table { border-radius: 12px; overflow: hidden; }
    .table th { background: #f1f5f9; color: #374151; }
    .table tr:nth-child(even) { background: #f9fafb; }
    .table tr:hover { background: #e5f1ff; transition: 0.2s; }
    .btn { border-radius: 8px !important; font-weight: 500; }
    .btn-warning { background: #fbbf24 !important; border: none; }
    .btn-danger { background: #ef4444 !important; border: none; }
    .btn-success { background: #10b981 !important; border: none; }
    .btn-secondary { background: #9ca3af !important; border: none; }
  </style>
</head>
<body class="flex flex-col min-h-screen">

  <header class="bg-blue-600 text-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto flex items-center justify-between py-2 px-4">
      <img src="logo.png" alt="ToyStore Logo" class="img-fluid" style="height: 60px; width: 90px; object-fit: contain;">
      <div class="flex-1 mx-6 hidden md:flex">
        <form method="get" action="admin.php" class="flex w-full">
          <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" 
                 class="w-full rounded-l-md px-3 py-2 text-black" placeholder="Search toys...">
          <button class="bg-yellow-400 px-4 rounded-r-md text-black font-semibold">🔍</button>
        </form>
      </div>
      <div class="flex items-center gap-4">
        <span>👤 <?= $_SESSION['user']['name'] ?></span>
        <a href="logout.php" class="bg-white text-blue-600 px-3 py-1 rounded">Logout</a>
      </div>
    </div>
  </header>

  <div class="flex flex-1">
    <aside class="w-64 bg-white shadow-lg min-h-screen p-4 hidden md:block">
      <h3 class="text-xl font-bold mb-6">📊 Admin Panel</h3>
      <nav class="flex flex-col gap-3">
        <a href="admin.php" class="p-2 rounded-lg bg-blue-50 text-blue-700 font-semibold">🧸 Toys</a>
        <a href="admin-orders.php" class="p-2 rounded-lg hover:bg-blue-100 text-gray-700">📦 Orders</a>
        <a href="profile.php" class="p-2 rounded-lg hover:bg-blue-100 text-gray-700">👤 Profile</a>
      </nav>
    </aside>

    <main class="flex-1 p-5">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow"><h5>Total Toys</h5><p class="text-2xl font-bold text-blue-600"><?= $totalToys ?></p></div>
        <div class="bg-white rounded-xl p-4 shadow"><h5>Brands</h5><p class="text-2xl font-bold text-green-600"><?= $totalBrands ?></p></div>
        <div class="bg-white rounded-xl p-4 shadow"><h5>Categories</h5><p class="text-2xl font-bold text-purple-600"><?= $totalCats ?></p></div>
      </div>

      <div class="bg-white rounded-2xl shadow-md p-4 mb-4">
        <h4 class="mb-3 font-bold">
          <?= $editToy ? "✏️ Edit Toy: " . htmlspecialchars($editToy['name']) : "➕ Add New Toy" ?>
        </h4>
        <form method="post" class="row g-3" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?= $editToy['id'] ?? '' ?>">
          <div class="col-md-3"><input class="form-control" name="name" placeholder="Toy Name" value="<?= $editToy['name'] ?? '' ?>" required></div>
          <div class="col-md-2"><input class="form-control" name="brand" placeholder="Brand" value="<?= $editToy['brand'] ?? '' ?>"></div>
          
          <!-- CATEGORY DROPDOWN -->
          <div class="col-md-2">
            <select class="form-control" name="category" required>
              <option value="">-- Select Category --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= ($editToy && $editToy['category'] === $cat) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2"><input class="form-control" name="price" type="number" step="0.01" placeholder="Price" value="<?= $editToy['price'] ?? '' ?>" required></div>
          <div class="col-md-3"><input class="form-control" name="image" type="file" accept="image/*"></div>
          <?php if ($editToy && !empty($editToy['image'])): ?>
            <div class="col-12"><img src="<?= htmlspecialchars($editToy['image']) ?>" alt="Current" style="max-height:100px;"></div>
          <?php endif; ?>
          <div class="col-12"><textarea class="form-control" name="description" placeholder="Description"><?= $editToy['description'] ?? '' ?></textarea></div>
          <div class="col-12">
            <?php if ($editToy): ?>
              <button class="btn btn-warning" type="submit" name="update">Update Toy</button>
              <a href="admin.php" class="btn btn-secondary">Cancel</a>
            <?php else: ?>
              <button class="btn btn-success" type="submit" name="add">Add Toy</button>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <table class="table table-bordered">
          <tr>
            <th>No</th><th>Name</th><th>Brand</th><th>Category</th><th>Price</th><th>Image</th><th>Action</th>
          </tr>
          <?php if (count($toys) > 0): ?>
            <?php $i = 1; foreach($toys as $row): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['brand']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td>₹<?= number_format($row['price'], 2) ?></td>
                <td><?php if (!empty($row['image'])): ?><img src="<?= htmlspecialchars($row['image']) ?>" width="60" alt="toy"><?php endif; ?></td>
                <td>
                  <a href="admin.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <a href="admin.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this toy?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
              <tr><td colspan="7" class="text-center">❌ No toys found</td></tr>
          <?php endif; ?>
        </table>
      </div>
    </main>
  </div>

  <footer class="bg-gray-900 text-gray-300 mt-6">
    <div class="container mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 py-10 px-6">
      <div><h4 class="font-semibold text-white mb-3">ABOUT</h4><ul class="space-y-1"><li><a href="#" class="hover:text-white">Contact Us</a></li><li><a href="#" class="hover:text-white">About Us</a></li><li><a href="#" class="hover:text-white">Careers</a></li></ul></div>
      <div><h4 class="font-semibold text-white mb-3">HELP</h4><ul class="space-y-1"><li><a href="#" class="hover:text-white">Payments</a></li><li><a href="#" class="hover:text-white">Shipping</a></li><li><a href="#" class="hover:text-white">Returns</a></li></ul></div>
      <div><h4 class="font-semibold text-white mb-3">POLICY</h4><ul class="space-y-1"><li><a href="#" class="hover:text-white">Terms of Use</a></li><li><a href="#" class="hover:text-white">Privacy Policy</a></li><li><a href="#" class="hover:text-white">Security</a></li></ul></div>
      <div><h4 class="font-semibold text-white mb-3">SOCIAL</h4><ul class="space-y-1"><li><a href="#" class="hover:text-white">Facebook</a></li><li><a href="#" class="hover:text-white">Twitter</a></li><li><a href="#" class="hover:text-white">YouTube</a></li></ul></div>
    </div>
    <div class="bg-gray-800 text-center py-3 text-sm text-gray-400">© 2025 ToyStore Admin — Inspired by Flipkart Clone</div>
  </footer>

</body>
</html>
