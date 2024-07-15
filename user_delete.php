<?php
include 'connection.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Veritabanından sil
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "Kullanıcı başarıyla silindi.";
    } else {
        echo "Kullanıcı silinirken hata oluştu: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Silme işleminden sonra kullanıcılar sayfasına yönlendir
    header("Location: users.php");
    exit();
} else {
    echo "Geçersiz istek.";
}
?>
