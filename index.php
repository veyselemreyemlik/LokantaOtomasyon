<?php
include 'header.php';
session_start();

// Eğer kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kullanıcı kimliğini oturumdan al
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h1>Hoşgeldiniz, Kullanıcı!</h1>
        <p>Bu sayfa, kullanıcıların yönetim paneline erişebileceği ana sayfadır.</p>
        <a href="menu.php" class="btn btn-primary">Menü</a>
        <a href="orders.php" class="btn btn-primary">Siparişler</a>
        <a href="users.php" class="btn btn-primary">Kullanıcılar</a>
    </div>
</body>

</html>

<?php include 'footer.php'; ?>