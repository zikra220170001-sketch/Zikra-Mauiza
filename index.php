<?php
session_start();
include "koneksi.php";
include "mail_config.php";

$error_message = ""; // Menampung pesan error
$is_blocked = false; // Status akun diblokir
$remaining_seconds = 0; // Untuk countdown JS

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $ip       = $_SERVER['REMOTE_ADDR'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $current_time = new DateTime();
        $blocked_until = isset($user['blocked_until']) && $user['blocked_until'] ? new DateTime($user['blocked_until']) : null;

        // Cek apakah user sedang diblokir
        if ($blocked_until && $current_time < $blocked_until) {
            $remaining = $blocked_until->getTimestamp() - $current_time->getTimestamp();
            $remaining_seconds = $remaining; // untuk JS
            $minutes = floor($remaining / 60);
            $seconds = $remaining % 60;
            $error_message = "⚠️ Akun dibekukan sementara. Coba lagi dalam {$minutes} menit {$seconds} detik.";
            $is_blocked = true;
        } else {
            // Reset blokir jika waktu blokir sudah lewat
            $conn->query("UPDATE users SET blocked_until = NULL, login_attempts = 0 WHERE username='$username'");

            // Cek password
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username;
                $conn->query("INSERT INTO login_logs (username, status, ip_address) VALUES ('$username','berhasil','$ip')");
                header("Location: dashboard.php");
                exit;
            } else {
                // Password salah
                $conn->query("INSERT INTO login_logs (username, status, ip_address) VALUES ('$username','gagal','$ip')");

                // Tambah 1 percobaan gagal
                $login_attempts = $user['login_attempts'] + 1;
                $blocked_time = null;

                if ($login_attempts >= 3) {
                    // Blokir 2 menit
                    $blocked_time = date('Y-m-d H:i:s', strtotime('+2 minutes'));
                    sendAlertEmail($user['email'], $username, $ip);
                    $error_message = "⚠️ Terjadi 3 kali gagal login. Email peringatan dikirim! Akun dibekukan 2 menit.";
                    $login_attempts = 0; // reset setelah blokir
                    $is_blocked = true;
                    $remaining_seconds = 120; // 2 menit
                } else {
                    $error_message = "Password salah!";
                }

                // Update database
                $conn->query("UPDATE users SET login_attempts = $login_attempts, last_attempt = NOW(), blocked_until = " . ($blocked_time ? "'$blocked_time'" : "NULL") . " WHERE username='$username'");
            }
        }
    } else {
        // Username tidak ditemukan
        $conn->query("INSERT INTO login_logs (username, status, ip_address) VALUES ('$username','gagal','$ip')");
        $error_message = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Aman</title>
    <style>
        /* Import font modern */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 40px 35px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
            font-weight: 600;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #4e73df;
            box-shadow: 0 0 5px rgba(78,115,223,0.3);
            outline: none;
        }

        button {
            background: linear-gradient(135deg, #4e73df, #36b9cc);
            color: #fff;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        button:hover {
            background: linear-gradient(135deg, #2e59d9, #17a673);
            transform: scale(1.02);
        }

        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .error-message {
            background-color: #ffe1e1;
            color: #c0392b;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
        }

        a {
            color: #4e73df;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #1cc88a;
        }

        p {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Login Sistem Aman</h2>

    <?php if (!empty($error_message)) : ?>
        <div class="error-message" id="error-message"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" id="login-form">
        <input type="text" name="username" placeholder="Username" required
        <?= $is_blocked ? "disabled" : "" ?>
        ><br>
        <input type="password" name="password" placeholder="Password" required
        <?= $is_blocked ? "disabled" : "" ?>
        ><br>
        <button type="submit" name="login" <?= $is_blocked ? "disabled" : "" ?>>Login</button>
        <p>Belum punya akun? <a href="register.php">Daftar</a></p>
    </form>
</div>

<?php if ($is_blocked && $remaining_seconds > 0): ?>
<script>
let remaining = <?= $remaining_seconds ?>;
const form = document.getElementById('login-form');
const errorDiv = document.getElementById('error-message');

const countdown = setInterval(() => {
    if (remaining <= 0) {
        clearInterval(countdown);
        errorDiv.innerText = "Akun sudah bisa login kembali.";
        form.querySelectorAll('input, button').forEach(el => el.disabled = false);
    } else {
        let minutes = Math.floor(remaining / 60);
        let seconds = remaining % 60;
        errorDiv.innerText = `⚠️ Akun dibekukan sementara. Coba lagi dalam ${minutes} menit ${seconds} detik.`;
        remaining--;
    }
}, 1000);
</script>
<?php endif; ?>
</body>
</html>
