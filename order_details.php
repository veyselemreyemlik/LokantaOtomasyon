<?php
include 'connection.php';
session_start();
$table_id = $_GET['table_id'];

// Fetch order details
$order_details = [];
$sql = "SELECT o.order_id, o.created_at, u.username, mi.product_name, od.piece, od.statement
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN order_details od ON o.order_id = od.order_id
        JOIN menu_items mi ON od.menu_id = mi.menu_id
        WHERE o.table_id = $table_id AND o.status_number = 1";
$result = $conn->query($sql);

if ($result === FALSE) {
    die("Sorgu başarısız: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $order_details[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Sipariş Detayları</title>
</head>

<body>

    <h1>Masa <?php echo $table_id; ?> Sipariş Detayları</h1>

    <?php if (!empty($order_details)): ?>
        <table>
            <tr>
                <th>Sipariş ID</th>
                <th>Sipariş Saati</th>
                <th>Siparişi Alan</th>
                <th>Ürün</th>
                <th>Adet</th>
                <th>Açıklama</th>
            </tr>
            <?php foreach ($order_details as $detail): ?>
                <tr>
                    <td><?php echo $detail['order_id']; ?></td>
                    <td><?php echo $detail['created_at']; ?></td>
                    <td><?php echo $detail['username']; ?></td>
                    <td><?php echo $detail['product_name']; ?></td>
                    <td><?php echo $detail['piece']; ?></td>
                    <td><?php echo $detail['statement']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <button onclick="location.href='add_order.php?table_id=<?php echo $table_id; ?>'">Sipariş Ekle</button>
    <?php else: ?>
        <p>Bu masada henüz sipariş yok.</p>
    <?php endif; ?>

</body>

</html>