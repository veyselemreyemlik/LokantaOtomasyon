<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $payment = $_POST['payment'];
    $payment_type = $_POST['payment_type'];

    // Veritabanında ödeme miktarını ve ödeme türünü güncelleme sorgusu
    $sql = "UPDATE orders SET status_number =3, payment = ?, payment_type = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('dii', $payment, $payment_type, $order_id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
    $conn->close();
}
?>
