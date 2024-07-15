<?php
session_start(); // Session başlatma
include 'connection.php'; // Veritabanı bağlantısı
// Eğer kullanıcı zaten giriş yapmışsa ve place_id tanımlıysa yönlendir
if (isset($_SESSION['user_id'])) {
    // Kullanıcının place_id'sini al
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT place_id FROM users WHERE id = $user_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Kullanıcının place_id'sine göre yönlendirme yap
        switch ($user['place_id']) {
            case 1:
            case 2:
            case 3:
                header("Location: place.php?place_id=" . $user['place_id']); // Place sayfasına yönlendir
                exit;
            case 4:
                header("Location: admin.php"); // Admin sayfasına yönlendir
                exit;
            case 5:
                header("Location: garson.php"); // Garson sayfasına yönlendir
                exit;
            default:
                header("Location: index.php"); // Varsayılan olarak ana sayfaya yönlendir
                exit;
        }
    }
}


// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kullanıcı adı ve şifre ile sorgu yap
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id']; // Oturumda kullanıcı ID'sini sakla

        // Kullanıcının place_id'sini kontrol et
        switch ($user['place_id']) {
            case 1:
            case 2:
            case 3:
                header("Location: place.php?place_id=" . $user['place_id']); // Place sayfasına yönlendir
                break;
            case 4:
                header("Location: admin.php"); // Admin sayfasına yönlendir
                break;
            case 5:
                header("Location: garson.php"); // Garson sayfasına yönlendir
                break;
            default:
                header("Location: index.php"); // Varsayılan olarak ana sayfaya yönlendir
                break;
        }
        exit;
    } else {
        $error_message = "Kullanıcı adı veya şifre yanlış.";
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z"
          crossorigin="anonymous">
    <style>
        .container {
            max-width: 400px;
            margin-top: 100px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Giriş Yap</h2>
    <?php if (isset($error_message)) : ?>
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

<?php include 'footer.php'; ?>
