<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pertashop";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    $sql = "SELECT * FROM users WHERE username=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            if ($row['role'] !== $role) {
                $error = "❌ Role tidak sesuai!";
            } else {
                // session_name supaya beda session untuk admin/operator
                if ($row['role'] === 'admin') {
                    session_name("PERTASHOP_ADMIN");
                } elseif ($row['role'] === 'operator') {
                    session_name("PERTASHOP_OPERATOR");
                }
                session_start();

                // ✅ perbaikan di sini: pakai user_id, bukan id
                $_SESSION['user_id']  = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama']     = $row['nama_lengkap'];
                $_SESSION['role']     = $row['role'];

                // redirect sesuai role
                if ($row['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: operator/dashboard_operator.php");
                }
                exit();
            }
        } else {
            $error = "❌ Password salah!";
        }
    } else {
        $error = "❌ Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pertashop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #800000;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 15px;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 360px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from {opacity:0; transform: translateY(12px);}
            to {opacity:1; transform: translateY(0);}
        }
        h2 {
            margin-bottom: 20px;
            color: #800000;
            font-size: 22px;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #5a0000ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #cb3a00ff;
        }
        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        /* ✅ Responsive untuk HP */
        @media (max-width: 480px) {
            .login-box {
                padding: 20px;
                border-radius: 10px;
            }
            h2 {
                font-size: 18px;
            }
            input, select, button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔐 Login Pertashop</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <select name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin">👨‍💼 Admin</option>
                <option value="operator">👷 Operator</option>
            </select>

            <button type="submit">Login</button>
        </form>
        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
