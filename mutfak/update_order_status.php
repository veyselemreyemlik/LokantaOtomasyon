<?php
include '../connection.php';

// POST verilerini al
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$detail_id = isset($_POST['detail_id']) ? intval($_POST['detail_id']) : 0;
$status_number = isset($_POST['status_number']) ? intval($_POST['status_number']) : 0;

if ($order_id > 0 && $detail_id > 0 && $status_number >= 0) {
    // Sipariş detayını güncelle
    $update_order_detail_sql = "UPDATE order_details SET status_number = ? WHERE detail_id = ?";
    $stmt = $conn->prepare($update_order_detail_sql);
    $stmt->bind_param("ii", $status_number, $detail_id);
    $success = $stmt->execute();

    if ($success) {
        // Siparişin tüm detaylarının durumunu kontrol et
        $check_details_sql = "SELECT COUNT(*) AS total, 
                              SUM(CASE WHEN status_number IN (1, 2) THEN 1 ELSE 0 END) AS ready_count
                              FROM order_details WHERE order_id = ?";
        $stmt = $conn->prepare($check_details_sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        // Eğer tüm detaylar hazırlandıysa, siparişin durumunu güncelle
        if ($data['total'] == $data['ready_count']) {
            $update_order_sql = "UPDATE orders SET status_number = 1 WHERE order_id = ?";
            $stmt = $conn->prepare($update_order_sql);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
        }

        echo 'success';
    } else {
        echo 'error: ' . $conn->error;
    }

    $stmt->close();
} else {
    echo 'error: Geçersiz sipariş ID veya detay ID';
}

$conn->close();
?>
