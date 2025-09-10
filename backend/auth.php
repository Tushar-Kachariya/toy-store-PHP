<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

// ===================== REGISTER =====================
if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'] ?? "";
    $role     = "user";

    $errors = [];

    if (empty($name)) {
        $errors[] = "Full name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $name)) {
        $errors[] = "Name can only contain letters and spaces.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
       
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='" . mysqli_real_escape_string($conn, $email) . "' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $errors[] = "Email is already registered.";
        }
    }

    
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

   
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../frontend/register.php");
        exit;
    }


    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed, $role);

    if (mysqli_stmt_execute($stmt)) {
        $user_id = mysqli_insert_id($conn);

        $_SESSION['user'] = [
            'id'    => (int)$user_id,
            'name'  => $name,
            'email' => $email,
            'role'  => $role
        ];

        $_SESSION['success'] = "🎉 Registration successful!";
        header("Location: ../frontend/index.php");
        exit;
    } else {
        $_SESSION['errors'] = ["Registration failed. Please try again."];
        header("Location: ../frontend/register.php");
        exit;
    }
}

// ===================== LOGIN =====================
if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $errors = [];

    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../frontend/login.php");
        exit;
    }

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='" . mysqli_real_escape_string($conn, $email) . "' LIMIT 1");

    if ($res && mysqli_num_rows($res) === 1) {
        $user = mysqli_fetch_assoc($res);

        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id'    => (int)$user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role']
            ];

            header("Location: " . ($user['role'] === 'admin' ? "../frontend/admin.php" : "../frontend/index.php"));
            exit;
        } else {
            $_SESSION['errors'] = ["Invalid password."];
            header("Location: ../frontend/login.php");
            exit;
        }
    } else {
        $_SESSION['errors'] = ["User not found."];
        header("Location: ../frontend/login.php");
        exit;
    }
}

// ===================== LOGOUT =====================
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_unset();
    session_destroy();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    header("Location: ../frontend/index.php");
    exit;
}
