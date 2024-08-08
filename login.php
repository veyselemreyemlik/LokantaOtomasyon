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
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <style>
        body {
            background-color: #DDDDDD;
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: indigo;
        }

        .container {
            max-width: 400px;
            margin-top: 100px;
        }
    </style>
</head>

<body>
    <div class="container">
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