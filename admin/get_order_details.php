<?php
include '../connection.php';

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    $sql_order_details = "SELECT o.order_id, t.table_name, o.created_at, u.username AS waiter_name, od.piece, mi.menu_name AS menu_item, mi.price, o.status_number
                          FROM orders o
                          JOIN tables t ON o.table_id = t.table_id
                          JOIN users u ON o.user_id = u.user_id
                          JOIN order_details od ON o.order_id = od.order_id
                          JOIN menu_items mi ON od.menu_id = mi.menu_id
                          WHERE o.order_id = '$order_id'";
    $result_order_details = $conn->query($sql_order_details);

    if ($result_order_details && $result_order_details->num_rows > 0) {
        $order_details = $result_order_details->fetch_assoc();
        echo '<p><strong>Sipariş Numarası:</strong> ' . $order_details['order_id'] . '</p>';
        echo '<p><strong>Masa:</strong> ' . $order_details['table_name'] . '</p>';
        echo '<p><strong>Sipariş Zamanı:</strong> ' . $order_details['created_at'] . '</p>';
        echo '<p><strong>Garson:</strong> ' . $order_details['waiter_name'] . '</p>';
        echo '<hr>';
        echo '<h5>Sipariş Detayları:</h5>';
        echo '<ul>';
        $result_order_details->data_seek(0); // Sonuç kümesini başa sar
        while ($row = $result_order_details->fetch_assoc()) {
            echo '<li>' . $row['menu_item'] . ' - ' . $row['piece'] . ' Adet - ' . number_format($row['price'], 2) . ' TL</li>';
        }
        echo '</ul>';
        echo '<input type="hidden" id="order_status_number" value="' . $order_details['status_number'] . '">';
    } else {
        echo '<p>Sipariş detayları bulunamadı.</p>';
    }
}
?>
