<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['order_id']) && isset($_POST['status_number'])) {
        $order_id = intval($_POST['order_id']);
        $status_number = intval($_POST['status_number']);

        // Sipariş durumunu güncelleme
        $sql = "UPDATE orders SET status_number = ? WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $status_number, $order_id);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }

        $stmt->close();
    } else {
        echo 'missing_parameters';
    }
} else {
    echo 'invalid_request';
}

$conn->close();
?>
