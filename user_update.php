<?php
// Veritabanı bağlantı dosyasını dahil et
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $user_id = $_POST["user_id"];
    $username = $_POST["username"];
    $place_id = $_POST["place_id"];
    $password = $_POST["password"];

    // Güncelleme sorgusu
    $sql = "UPDATE users SET username = '$username', place_id = '$place_id', password = '$password' WHERE user_id = $user_id";


    if ($conn->query($sql) === TRUE) {
        // Başarıyla güncellendiğini bildir ve anasayfaya yönlendir
        echo "<script>alert('Kullanıcı başarıyla güncellendi');</script>";
        echo "<script>window.location.href = 'users.php';</script>";
    } else {
        // Hata durumunda hatayı göster
        echo "Error updating record: " . $conn->error;
    }
}

// Veritabanı bağlantısını kapat
$conn->close();
?>
