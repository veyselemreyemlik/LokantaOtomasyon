<?php
include '../connection.php';

// POST verilerini al
$detail_id = isset($_POST['detail_id']) ? intval($_POST['detail_id']) : 0;
$status_number = isset($_POST['status_number']) ? intval($_POST['status_number']) : 0;

if ($detail_id > 0 && $status_number >= 0) {
    // Sipariş detayını güncelle
    $update_order_detail_sql = "UPDATE order_details SET status_number = ? WHERE detail_id = ?";
    $stmt = $conn->prepare($update_order_detail_sql);
    $stmt->bind_param("ii", $status_number, $detail_id);
    $success = $stmt->execute();

    if ($success) {
        echo 'success';
    } else {
        echo 'error: ' . $conn->error;
    }

    $stmt->close();
} else {
    echo 'error: Geçersiz detay ID veya durum numarası';
}

$conn->close();
?>
