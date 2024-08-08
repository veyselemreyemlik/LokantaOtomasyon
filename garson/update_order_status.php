<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['detail_id']) && $_POST['action'] == 'detail_delivery') {
        $detail_id = intval($_POST['detail_id']);

        // Sipariş detayının `status_number` değerini güncelle
        $sql_update_detail = "UPDATE order_details SET status_number = 2 WHERE detail_id = ?";
        $stmt_update_detail = $conn->prepare($sql_update_detail);
        if (!$stmt_update_detail) {
            echo "Sorgu hazırlama hatası: " . $conn->error;
            exit;
        }
        $stmt_update_detail->bind_param("i", $detail_id);

        if ($stmt_update_detail->execute()) {
            // Tüm sipariş detaylarının `status_number` değeri 2 mi kontrol et
            $order_id_sql = "SELECT order_id FROM order_details WHERE detail_id = ?";
            $stmt_order_id = $conn->prepare($order_id_sql);
            $stmt_order_id->bind_param("i", $detail_id);
            $stmt_order_id->execute();
            $order_id_result = $stmt_order_id->get_result();
            $order_id_row = $order_id_result->fetch_assoc();
            $order_id = $order_id_row['order_id'];

            $all_details_sql = "SELECT COUNT(*) AS total, SUM(CASE WHEN status_number = 2 THEN 1 ELSE 0 END) AS delivered
                                FROM order_details WHERE order_id = ?";
            $stmt_all_details = $conn->prepare($all_details_sql);
            $stmt_all_details->bind_param("i", $order_id);
            $stmt_all_details->execute();
            $all_details_result = $stmt_all_details->get_result();
            $all_details_row = $all_details_result->fetch_assoc();

            if ($all_details_row['total'] == $all_details_row['delivered']) {
                // Tüm sipariş detayları teslim edildiyse, siparişin `status_number` değerini 2 olarak güncelle
                $sql_update_order = "UPDATE orders SET status_number = 2 WHERE order_id = ?";
                $stmt_update_order = $conn->prepare($sql_update_order);
                $stmt_update_order->bind_param("i", $order_id);
                $stmt_update_order->execute();
                $stmt_update_order->close();
            }

            echo 'success';
        } else {
            echo 'error: ' . $stmt_update_detail->error;
        }

        $stmt_update_detail->close();
    } else {
        echo 'missing_parameters';
    }
} else {
    echo 'invalid_request';
}

$conn->close();
?>
