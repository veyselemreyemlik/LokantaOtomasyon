<?php
include '../connection.php';

if (isset($_GET['table_id'])) {
    $table_id = $_GET['table_id'];

    // Veritabanından sil
    $sql = "DELETE FROM tables WHERE table_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $table_id);

    if ($stmt->execute()) {
        echo "Masa başarıyla silindi.";
    } else {
        echo "Masa silinirken hata oluştu: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Silme işleminden sonra masalar sayfasına yönlendir
    header("Location: table.php");
    exit();
} else {
    echo "Geçersiz istek.";
}
?>
