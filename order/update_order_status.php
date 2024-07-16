<?php
include '../connection.php';

if (isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    
    // Sipariş durumunu 3 olarak güncelle
    $sql = "UPDATE orders SET status_number = 3 WHERE order_id = $order_id";
    
    if ($conn->query($sql) === TRUE) {
        echo 'success';
    } else {
        echo 'Error: ' . $conn->error;
    }
} else {
    echo 'Sipariş ID belirtilmedi.';
}

$conn->close();
?>
