<?php
// Veritabanı bağlantı dosyasını dahil et
include "../connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $menu_id = $_POST["menu_id"];
    $menu_name = $_POST["menu_name"];
    $price = $_POST["price"];
    $place_id = $_POST["place_id"];

    // Güncelleme sorgusu
    $sql = "UPDATE menu_items SET menu_name='$menu_name', price='$price', place_id='$place_id' WHERE menu_id='$menu_id'";

    if ($conn->query($sql) === TRUE) {
        // Başarıyla güncellendiğini bildir ve anasayfaya yönlendir
        echo "<script>alert('Ürün başarıyla güncellendi');</script>";
        echo "<script>window.location.href = 'menu.php';</script>";
    } else {
        // Hata durumunda hatayı göster
        echo "Error updating record: " . $conn->error;
    }
}

// Veritabanı bağlantısını kapat
$conn->close();
?>
