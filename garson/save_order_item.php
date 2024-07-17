<?php
include '../connection.php';
session_start();
$order_id = $_POST['order_id'];
$menu_id = $_POST['menu_id'];
$piece = $_POST['piece'];
$statement = $_POST['statement'];

// Insert new order item
$sql = "INSERT INTO order_details (order_id, menu_id, piece, status, statement) VALUES ($order_id, $menu_id, $piece, 'P', '$statement')";

if ($conn->query($sql) === TRUE) {
    echo "Yeni sipariş başarıyla eklendi.";
} else {
    echo "Sipariş eklenirken hata oluştu: " . $conn->error;
}

$conn->close();

header("Location: order_details.php?table_id=" . $_POST['table_id']);
exit();
?>