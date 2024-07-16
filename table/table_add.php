<?php
include "../connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $table_name = $_POST['table_name'];

    // Veritabanına ekleme işlemi
    $sql = "INSERT INTO tables (table_name) VALUES ('$table_name')";

    if ($conn->query($sql) === TRUE) {
        // Yeni masa başarıyla eklendiğinde ana sayfaya yönlendir
        header("Location: table.php");
        exit();
    } else {
        // Ekleme işlemi sırasında hata oluştuysa
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
