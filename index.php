<?php

session_start();
include 'connection.php';

// Eğer kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kullanıcı kimliğini oturumdan al
$user_id = $_SESSION['user_id'];

// Kullanıcının place_id bilgisini al
$sql = "SELECT u.place_id, p.place_name FROM users u JOIN place p ON u.place_id = p.place_id WHERE u.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$place_id = $user['place_id'];
$place_name = $user['place_name'];

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
<style>
    body {
        background-color: #DDDDDD;
        font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        color: indigo;
    }
</style>
<body>
    <div class="container mt-5">
        <h1>Hoşgeldiniz, Kullanıcı!</h1>
        <p><?php echo $place_name; ?> Yetkisine Sahipsiniz. Diğer Sayfalara Giremezsiniz.</p>
        
        <div class="btn-group btn-group-lg" role="group">
            <a href="admin/admin.php" class="btn btn-danger">Admin Sayfası</a>
            <a href="garson/garson_Order.php" class="btn btn-warning">Garson Sayfası</a>
            <a href="izgara/izgara.php" class="btn btn-primary">Izgara Sayfası</a>
            <a href="firin/firin.php" class="btn btn-light">Fırın Sayfası</a>
            <a href="mutfak/mutfak.php" class="btn btn-success">Mutfak Sayfası</a>
        </div>
    </div>
</body>

</html>
