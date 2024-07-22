<?php
include '../connection.php';
session_start();

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Oturum süresi dolmuş.']);
    exit();
}

$table_id = $_POST['table_id'];
$order_items = json_decode($_POST['order_items'], true);

if (empty($order_items)) {
    echo json_encode(['success' => false, 'error' => 'Sipariş verisi eksik.']);
    exit();
}

// Sipariş kontrolü
$order_id = null;
$sql = "SELECT order_id FROM orders WHERE table_id = ? AND status_number != 3 ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $table_id);
$stmt->execute();
$stmt->bind_result($order_id);
$stmt->fetch();
$stmt->close();

if ($order_id === null) {
    // Yeni sipariş oluşturma
    $sql = "INSERT INTO orders (table_id, user_id, status_number) VALUES (?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $table_id, $_SESSION['user_id']);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Yeni sipariş ID'sini al
    $stmt->close();
}

// Sipariş detaylarını ekleme
$sql = "INSERT INTO order_details (order_id, menu_id, piece, statement) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

foreach ($order_items as $item) {
    $stmt->bind_param("iiis", $order_id, $item['menu_id'], $item['piece'], $item['statement']);
    $stmt->execute();
}

$stmt->close();

// Sipariş durumunu 0 olarak güncelle
$sql = "UPDATE orders SET status_number = 0 WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
$conn->close();
?>
