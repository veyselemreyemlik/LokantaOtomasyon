<?php
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $username = $_POST['username'];
    $place_id = $_POST['place_id'];
    $password = $_POST['password'];

    // Veritabanına ekleme işlemi
    $sql = "INSERT INTO users (username, place_id, password) VALUES ('$username', '$place_id', '$password')";


    if ($conn->query($sql) === TRUE) {
        // Yeni kullanıcı başarıyla eklendiğinde ana sayfaya yönlendir
        header("Location: users.php");
        exit();
    } else {
        // Ekleme işlemi sırasında hata oluştuysa
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
