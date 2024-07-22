<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];

    $sql = "UPDATE orders 
            SET status_number = 3, payment = 0, payment_type = 0 
            WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo "Masa başarıyla kapatıldı.";
    } else {
        echo "Masa kapatılırken bir hata oluştu.";
    }

    $stmt->close();
    $conn->close();
}
?>
