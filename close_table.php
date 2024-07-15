<?php
include 'connection.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    $delete_order_details_sql = "DELETE FROM order_details WHERE order_id = $order_id";
    $delete_order_sql = "DELETE FROM orders WHERE id = $order_id";

    if ($conn->query($delete_order_details_sql) === TRUE && $conn->query($delete_order_sql) === TRUE) {
        echo 'success';
    } else {
        echo 'Hata: ' . $conn->error;
    }
}
?>