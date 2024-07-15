<?php
include '../connection.php';

if (isset($_GET['menu_id'])) {
    $menu_id = $_GET['menu_id'];

    // Veritabanından sil
    $sql = "DELETE FROM menu_items WHERE menu_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $menu_id);

    if ($stmt->execute()) {
        echo "Ürün başarıyla silindi.";
    } else {
        echo "Ürün silinirken hata oluştu: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Silme işleminden sonra menü sayfasına yönlendir
    header("Location: menu.php");
    exit();
} else {
    echo "Geçersiz istek.";
}
?>
