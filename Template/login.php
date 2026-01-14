<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistem ATK & ARK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            padding: 40px;
            border-radius: 24px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }
        .btn-login {
            background: #002d5a;
            color: #fff;
            font-weight: 800;
            border-radius: 14px;
            padding: 14px;
            width: 100%;
            border: none;
        }
        .btn-login:hover {
            background: #004488;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <img src="asset/Logo BPS Kota Manado - All White.png"
             style="height:45px; filter:brightness(0) saturate(100%) invert(13%) sepia(37%) saturate(4208%) hue-rotate(195deg) brightness(92%) contrast(106%);">
        <h3 class="fw-extrabold mt-3">Login Sistem ATK</h3>
        <p class="text-muted small">BPS Kota Manado</p>
    </div>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger text-center small">
        Username atau password salah
    </div>
<?php endif; ?>


    <form method="POST" action="../DB/process_login.php">
        <div class="mb-3">
            <label class="fw-bold small">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>  

        <div class="mb-4">
            <label class="fw-bold small">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-login">
            Login
        </button>
    </form>
</div>

</body>
</html>
