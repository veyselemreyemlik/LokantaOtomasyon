<?php
include "../connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $menu_name = $_POST['menu_name'];
    $price = $_POST['price'];
    $place_id = $_POST['place_id'];

    // Veritabanına ekleme işlemi
    $sql = "INSERT INTO menu_items (menu_name, price, place_id) VALUES ('$menu_name', '$price', '$place_id')";

    if ($conn->query($sql) === TRUE) {
        // Yeni öğe başarıyla eklendiğinde ana sayfaya yönlendir
        header("Location: menu.php");
        exit();
    } else {
        // Ekleme işlemi sırasında hata oluştuysa
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
