<?php
// Veritabanı bağlantı dosyasını dahil et
include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $table_id = $_POST["table_id"];
    $table_name = $_POST["table_name"];

    // Güncelleme sorgusu
    $sql = "UPDATE tables SET table_name='$table_name' WHERE table_id='$table_id'";

    if ($conn->query($sql) === TRUE) {
        // Başarıyla güncellendiğini bildir ve anasayfaya yönlendir
        echo "<script>alert('Masa başarıyla güncellendi');</script>";
        echo "<script>window.location.href = 'table.php';</script>";
    } else {
        // Hata durumunda hatayı göster
        echo "Error updating record: " . $conn->error;
    }
}

// Veritabanı bağlantısını kapat
$conn->close();
?>
