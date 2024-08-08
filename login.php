<?php
session_start(); // Session başlatma
include 'connection.php'; // Veritabanı bağlantısı

// Eğer kullanıcı zaten giriş yapmışsa ve place_id tanımlıysa yönlendir
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT place_id FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        switch ($user['place_id']) {
            case 1:
                header("Location: izgara/izgara.php");
                exit;
            case 2:
                header("Location: mutfak/mutfak.php");
                exit;
            case 3:
                header("Location: firin/firin.php");
                exit;
            case 4:
                header("Location: admin/admin.php");
                exit;
            case 5:
                header("Location: garson/garson_order.php");
                exit;
            default:
                header("Location: index.php");
                exit;
        }
    }
}

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Veritabanı bağlantısını dahil edin
    include '../connection.php';
    
    // Kullanıcı adı ve şifreyi alın
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // SQL sorgusu
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kullanıcıyı kontrol edin
    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Şifre kontrolü (Not: Güvenlik için bu kısmı password_hash ve password_verify ile güncelleyin)
        if ($password === $user['password']) { 
            // Oturum değişkenlerini ayarlayın
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['place_id'] = $user['place_id'];  // place_id'yi oturuma kaydedin
            
            // Kullanıcının place_id'sine göre yönlendirin
            switch ($user['place_id']) {
                case 1:
                    header("Location: izgara/izgara.php");
                    exit;
                case 2:
                    header("Location: mutfak/mutfak.php");
                    exit;
                case 3:
                    header("Location: firin/firin.php");
                    exit;
                case 4:
                    header("Location: admin/admin.php");
                    exit;
                case 5:
                    header("Location: garson/garson_order.php");
                    exit;
                default:
                    header("Location: index.php");
                    exit;
            }
        } else {
            $error_message = "Kullanıcı adı veya şifre yanlış.";
        }
    } else {
        $error_message = "Kullanıcı adı veya şifre yanlış.";
    }

    // Veritabanı bağlantısını kapatın
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 400px;
            width: 100%;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .container img {
            width: 80px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .alert {
            margin-top: 15px;
        }
        
    </style>
</head>

<body>
    <div class="container">
        <img style="width: 100%;" src="image/logo.png" alt="Logo"> <!-- Burada logo görselinin yolunu belirtin -->
        <h2 class="mb-4">Giriş Yap</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Giriş Yap</button>
        </form>
    </div>
</body>

</html>
