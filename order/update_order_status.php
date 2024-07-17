<?php
include '../connection.php';

if (isset($_POST['order_id']) && isset($_POST['payment'])) {
    $order_id = $_POST['order_id'];
    $payment = $_POST['payment'];

    // Order statusunu güncelle ve ödeme miktarını kaydet
    $sql = "UPDATE orders SET status_number = 3, payment = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $payment, $order_id);
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error: ' . $stmt->error;
    }
    $stmt->close();
} else {
    echo 'error: missing parameters';
}

$conn->close();
?>
