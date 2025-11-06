<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        /* Font modern */
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
            padding: 50px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            width: 400px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-25px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 15px;
        }

        p {
            color: #666;
            font-size: 15px;
            margin-bottom: 25px;
        }

        a {
            display: inline-block;
            text-decoration: none;
            background: linear-gradient(135deg, #36b9cc, #4e73df);
            color: #fff;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        a:hover {
            background: linear-gradient(135deg, #2e59d9, #17a673);
            transform: scale(1.05);
        }

        /* Tambahan efek bayangan ringan untuk elemen */
        .container:hover {
            box-shadow: 0 12px 35px rgba(0,0,0,0.2);
        }

        /* Animasi teks selamat datang */
        h2 span {
            color: #1cc88a;
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Selamat Datang, <span><?php echo $_SESSION['username']; ?></span>!</h2>
    <p>Anda berhasil login ke sistem.</p>
    <a href="logout.php">Logout</a>
</div>
</body>
</html>
