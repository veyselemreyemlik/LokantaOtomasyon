<?php
include '../connection.php';
session_start();
$table_id = $_POST['table_id'];
$menu_id = $_POST['menu_id'];
$piece = $_POST['piece'];
$statement = $_POST['statement'];
$user_id = 1; // Buraya giriş yapmış kullanıcının ID'sini koymanız gerekiyor

// Insert new order
$sql = "INSERT INTO orders (user_id, table_id, status_number) VALUES ($user_id, $table_id, 1)";
if ($conn->query($sql) === TRUE) {
    $order_id = $conn->insert_id;
    $sql = "INSERT INTO order_details (order_id, menu_id, piece, status, statement) VALUES ($order_id, $menu_id, $piece, 'P', '$statement')";
    if ($conn->query($sql) === TRUE) {
        echo "Yeni sipariş başarıyla oluşturuldu.";
    } else {
        echo "Sipariş detayları eklenirken hata oluştu: " . $conn->error;
    }
} else {
    echo "Sipariş eklenirken hata oluştu: " . $conn->error;
}

$conn->close();

header("Location: garson.php");
exit();
?>