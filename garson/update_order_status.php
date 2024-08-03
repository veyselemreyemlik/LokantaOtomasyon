<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['order_id']) && isset($_POST['status_number'])) {
        $order_id = intval($_POST['order_id']);
        $status_number = intval($_POST['status_number']);

        // Veritabanı bağlantısının başarılı olup olmadığını kontrol edin
        if ($conn->connect_error) {
            die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
        }

        // Sipariş durumunu güncelleme
        $sql_update_order = "UPDATE orders SET status_number = ? WHERE order_id = ?";
        $stmt_update_order = $conn->prepare($sql_update_order);
        if (!$stmt_update_order) {
            echo "Sorgu hazırlama hatası: " . $conn->error;
            exit;
        }
        $stmt_update_order->bind_param("ii", $status_number, $order_id);

        // Sipariş detaylarını güncelleme (status_number'ı 2 yapmak için)
        $sql_update_details = "UPDATE order_details SET status_number = 2 WHERE order_id = ?";
        $stmt_update_details = $conn->prepare($sql_update_details);
        if (!$stmt_update_details) {
            echo "Sorgu hazırlama hatası: " . $conn->error;
            exit;
        }
        $stmt_update_details->bind_param("i", $order_id);

        if ($stmt_update_order->execute() && $stmt_update_details->execute()) {
            echo 'success';
        } else {
            echo 'error: ' . $stmt_update_order->error . ' - ' . $stmt_update_details->error;
        }

        $stmt_update_order->close();
        $stmt_update_details->close();
    } else {
        echo 'missing_parameters';
    }
} else {
    echo 'invalid_request';
}

$conn->close();
?>
