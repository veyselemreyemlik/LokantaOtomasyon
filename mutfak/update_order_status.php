<?php
include '../connection.php';

// POST verilerini al
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

// Sipariş detaylarını güncelle
$update_order_details_sql = "UPDATE order_details od
                             JOIN menu_items mi ON od.menu_id = mi.menu_id
                             SET od.status_number = 1
                             WHERE od.order_id = ? AND mi.place_id = 2 AND od.status_number = 0";
$stmt = $conn->prepare($update_order_details_sql);
$stmt->bind_param("i", $order_id);
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
    echo 'error';
}

$stmt->close();
$conn->close();
?>
